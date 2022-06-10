<?php

namespace BigCommerce\Rest;

use BigCommerce\Cart\Cart_Mapper;
use BigCommerce\Api\Shipping_Api;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use Bigcommerce\Api\Resources\ShippingZone;
use BigCommerce\Templates\Shipping_Methods;
use BigCommerce\Templates\Shipping_Zones_Dropdown;

class Shipping_Controller extends Rest_Controller {

	const SHIPPING_METHOD_TYPE_PERITEM       = 'peritem';
	const SHIPPING_METHOD_TYPE_FREE          = 'freeshipping';
	const SHIPPING_METHOD_TYPE_WEIGHT_WEIGHT = 'weight';
	const SHIPPING_METHOD_TYPE_WEIGHT_TOTAL  = 'total';

	/**
	 * @var Shipping_Api
	 */
	private $shipping_api;

	/**
	 * @var CartApi
	 */
	private $cart_api;

	/**
	 * Rest_Controller constructor.
	 *
	 * @param string       $namespace_base
	 * @param string       $version
	 * @param string       $rest_base
	 * @param Shipping_Api $shipping_api
	 * @param CartApi      $cart_api
	 */
	public function __construct( $namespace_base, $version, $rest_base, Shipping_Api $shipping_api, CartApi $cart_api ) {
		parent::__construct( $namespace_base, $version, $rest_base );

		$this->shipping_api = $shipping_api;
		$this->cart_api     = $cart_api;
	}

	/**
	 * Add data to the JS config to support cart ajax
	 *
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 */
	public function js_config( $config ) {
		$config['cart']['methods_api_url'] = $this->get_base_url();
		$config['cart']['zones_api_url']   = $this->get_base_url() . '/zones/html';

		return $config;
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/zones/html', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_zones' ],
				'permission_callback' => [ $this, 'get_rendered_item_permissions_check' ],
				'args'                => [],
			],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<zone_id>[0-9]+)/methods/html', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_zone_methods' ],
				'permission_callback' => [ $this, 'get_rendered_item_permissions_check' ],
				'args'                => [
					'zone_id' => [
						'type'    => 'integer',
						'default' => 0,
					]
				],
			],
		] );
    }

    public function get_rendered_zones_schema() {
		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'bigcommerce_review_list_rendered',
			'type'       => 'object',
			'properties' => [
				'rendered' => [
					'description' => __( 'The rendered shipping zones dropdown', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
			],
		];

		return $schema;
	}


	/**
	 * Checks if a given request has access to read the rendered shortcodes.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_rendered_item_permissions_check( $request ) {
		// no access checks for now
		return true;
	}

	/**
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 *
	 */
	public function get_zones( $request ) {
		$zones = $this->shipping_api->get_zones();

		$zones = array_filter( $this->shipping_api->get_zones() ?: [], function ( ShippingZone $zone ) {
			return $zone->enabled;
		} );

		$zones = array_map( function ( $zone ) {
			return [
				'id'   => $zone->id,
				'name' => $zone->name,
			];
		}, $zones );

		$controller = Shipping_Zones_Dropdown::factory( [
			Shipping_Zones_Dropdown::ZONES => $zones,
		] );

		$output = $controller->render();

		$response = rest_ensure_response( [
			'rendered' => $output,
		] );

		return $response;
    }

    /**
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 *
	 */
	public function get_zone_methods( $request ) {
		$attributes = $request->get_params();
		$cart       = $this->get_cart();

		$methods = $this->shipping_api->get_shipping_methods( $attributes['zone_id'] );

		$methods = array_filter( array_map( function ( $method ) use ( $cart ) {
			$rate_raw = $this->get_method_rate( $method, $cart );

			if ( ! $method->enabled ) {
				return;
			}

			$method_name = $method->name;
			if ( $method->type === self::SHIPPING_METHOD_TYPE_FREE && $rate_raw > 0 ) {
				$method_name = __( 'Fixed Shipping', 'bigcommerce' );
			}

			$method = [
				'id'              => $method->id,
				'name'            => $method_name,
				'type'            => $method->type,
				'rate_raw'        => $rate_raw,
				'rate'            => $this->format_price( $rate_raw ),
				'fixed_surcharge' => isset( $method->handling_fees->fixed_surcharge ) ? $method->handling_fees->fixed_surcharge : 0,
			];

			$cart_subtotal = $this->cart_subtotal_for_method( $method, $cart );

			$method['cart_subtotal_raw'] = $cart_subtotal;
			$method['cart_subtotal']     = $this->format_price( $cart_subtotal );

			$cart_total = $this->cart_total_for_method( $cart_subtotal, $cart );

			$method['cart_total_raw'] = $cart_total;
			$method['cart_total']     = $this->format_price( $cart_total );

			return $method;
		}, $methods ) );

		$controller = Shipping_Methods::factory( [
			Shipping_Methods::METHODS => $methods,
		] );

		$output = $controller->render();

		$response = rest_ensure_response( [
			'rendered' => $output,
		] );

		return $response;
	}

	private function format_price( $price ) {
		/**
		 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $price ), $price );
	}

	private function cart_subtotal_for_method( $method, $cart ) {
		$rate = 0;
		if ( $method['type'] === self::SHIPPING_METHOD_TYPE_PERITEM ) {
			foreach ( $cart['items'] as $cart_item ) {
				foreach ( $cart_item['bigcommerce_product_type'] as $item_type ) {
					if ( $item_type['slug'] === 'physical' ) {
						$rate += $method['rate_raw'] * $cart_item['quantity'];
						break;
					}
				}
			}
		} else {
			$rate = $method['rate_raw'];
		}

		$total = $cart['subtotal']['raw'] + $rate + $method['fixed_surcharge'];

		return $total;
	}

	private function cart_total_for_method( $subtotal, $cart ) {

		if ( empty( $cart['tax_amount']['raw'] ) ) {
			return $subtotal;
		}

		return $subtotal + $cart['tax_amount']['raw'];
	}

	private function get_method_rate( $method, $cart ) {
		$rate = isset( $method->settings->rate ) ? $method->settings->rate : 0;

		switch ( $method->type ) {
			case self::SHIPPING_METHOD_TYPE_FREE:

				$minimum_subtotal                = $method->settings->carrier_options->minimum_sub_total ?? 0;
				$exclude_fixed_shipping_products = (bool) $method->settings->carrier_options->exclude_fixed_shipping_products ?? true;
				$use_discounted_sub_total        = (bool) $method->settings->carrier_options->use_discounted_sub_total ?? true;

				$subtotal = $cart['subtotal']['raw'];
				if ( ! $use_discounted_sub_total ) {
					// TODO: Add coupons amount
					$subtotal = $cart['subtotal']['raw'] + $cart['discount_amount']['raw'];
				}

				if ( $subtotal >= $minimum_subtotal ) {
					if ( $exclude_fixed_shipping_products ) {
						$rate = $this->get_cart_items_fixed_shipping( $cart );
					}
				} else {
					$method->enabled = false;
				}
				break;

			case self::SHIPPING_METHOD_TYPE_WEIGHT_WEIGHT:

				$cart_weight = $this->get_cart_weight( $cart );
				$ranges      = $method->settings->range ?? [];

				foreach ( $ranges as $range ) {
					// upper limit is set to "up to but not included"
					if ( $cart_weight >= $range->lower_limit && $cart_weight < $range->upper_limit ) {
						$rate = $range->shipping_cost;

						break;
					}
				}

				break;

			case self::SHIPPING_METHOD_TYPE_WEIGHT_TOTAL:

				$ranges = $method->settings->range ?? [];

				// "Use discounted order subtotal" setting for this method type available in BC admin
				// is not reflected in api data
				$subtotal = $cart['subtotal']['raw'];

				foreach ( $ranges as $range ) {
					// upper limit is set to "up to but not included"
					if ( $subtotal >= $range->lower_limit && $subtotal < $range->upper_limit ) {
						$rate = $range->shipping_cost;

						break;
					}
				}

				break;

			default:
				break;
		}

		return $rate;
	}

	private function get_cart() {
		$cart_id = $this->get_cart_id();
		if ( empty( $cart_id ) ) {
			return $this->get_empty_cart();
		}
		try {
			$include = [
				'line_items.physical_items.options',
				'line_items.digital_items.options',
				'redirect_urls',
			];
			$cart   = $this->cart_api->cartsCartIdGet( $cart_id, [ 'include' => $include ] )->getData();
			$mapper = new Cart_Mapper( $cart );

			return $mapper->map();
		} catch ( ApiException $e ) {
			return $this->get_empty_cart();
		}
	}

	private function get_cart_id() {
		$cart = new \BigCommerce\Cart\Cart( $this->cart_api );
		return $cart->get_cart_id();
	}

	private function get_empty_cart() {
		return [
			'cart_id'         => '',
			'base_amount'     => 0,
			'discount_amount' => 0,
			'cart_amount'     => 0,
			'items'           => [],
		];
	}

	private function get_cart_items_fixed_shipping( $cart ) {
		$fixed_shipping = 0;
		foreach ( $cart['items'] as $cart_item ) {
			if ( $cart_item['is_free_shipping'] ) {
				continue;
			}

			$fixed_shipping += $cart_item['fixed_cost_shipping_price']['raw'] * $cart_item['quantity'];
		}

		return $fixed_shipping;
	}

	private function get_cart_weight( $cart ) {
		$weight = 0;
		foreach ( $cart['items'] as $cart_item ) {
			$weight += $cart_item['weight'];
		}

		return $weight;
	}

}
