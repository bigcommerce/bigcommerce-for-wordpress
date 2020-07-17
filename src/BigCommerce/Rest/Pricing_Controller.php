<?php


namespace BigCommerce\Rest;


use BigCommerce\Accounts\Customer;
use BigCommerce\Api\v3\Api\PricingApi;
use BigCommerce\Api\v3\Model\ItemPricing;
use BigCommerce\Api\v3\Model\PricingRequest;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Currency\With_Currency;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Settings\Sections\Currency;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Pricing_Controller extends Rest_Controller {
	use With_Currency;

	private $pricing_api;

	public function __construct( $namespace_base, $version, $rest_base, PricingApi $pricing_api ) {
		parent::__construct( $namespace_base, $version, $rest_base );
		$this->pricing_api = $pricing_api;
	}

	/**
	 * Add data to the JS config to support pricing requests
	 *
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 */
	public function js_config( $config ) {
		$config['pricing'] = [
			'api_url'            => $this->get_base_url(),
			'ajax_pricing_nonce' => wp_create_nonce( 'wp_rest' ),
		];

		return $config;
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => [ 'GET', 'POST' ],
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
		] );
	}


	/**
	 * Retrieves a collection of products.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$customer = new Customer( get_current_user_id() );

		/**
		 * Filter the customer group ID passed to the BigCommerce API.
		 * Null to use the default guest group. 0 to use unmodified catalog pricing.
		 *
		 * @param int|null The customer group ID
		 */
		$customer_group = apply_filters( 'bigcommerce/pricing/customer_group_id', $customer->get_group_id() );
		$currency_code  = get_option( Currency::CURRENCY_CODE, 'USD' );

		$args = [
			'items'             => $this->filter_empty_options( $request->get_param( 'items' ) ?: [] ),
			'channel_id'        => $this->get_channel_id(),
			'currency_code'     => $currency_code,
			'customer_group_id' => $customer_group,
		];
		$args = apply_filters( 'bigcommerce/pricing/request_args', $args, $request );

		try {
			$pricing_request  = new PricingRequest( $args );
			$pricing_response = $this->pricing_api->getPrices( $pricing_request );

			// convert items from objects to JSON
			$items    = array_map( [ $this, 'format_prices' ], $pricing_response->getData() );
			$response = [
				'items' => $items,
			];

			return rest_ensure_response( $response );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'gateway_error', $e->getMessage(), [ 'exception' => $e ] );
		}
	}

	private function filter_empty_options( $items ) {
		$items = array_map( function ( $item ) {
			if ( empty( $item['options'] ) ) {
				return $item;
			}
			$item['options'] = array_filter( $item['options'], function ( $option ) {
				return (int) $option['value_id'] !== 0;
			} );
			if ( empty( $item['options'] ) ) {
				unset( $item['options'] );
			}

			return $item;
		}, $items );

		return $items;
	}

	private function get_channel_id() {
		try {
			$connections = new Connections();
			$current     = $connections->current();

			return (int) get_term_meta( $current->term_id, Channel::CHANNEL_ID, true );
		} catch ( Channel_Not_Found_Exception $e ) {
			return 0;
		}
	}

	private function format_prices( ItemPricing $item ) {
		$return_data = [
			'product_id' => $item->getProductId(),
			'variant_id' => $item->getVariantId(),
			'options'    => $this->object_to_array( $item->getOptions() ),
		];
		$retail      = $item->getRetailPrice();
		$calculated  = $item->getCalculatedPrice();
		$original    = $item->getPrice();
		$sale        = $item->getSalePrice();
		$price       = $item->getPriceRange();
		$minimum     = $price->getMinimum();
		$maximum     = $price->getMaximum();
		switch ( get_option( Currency::PRICE_DISPLAY, Currency::DISPLAY_TAX_EXCLUSIVE ) ) {
			case Currency::DISPLAY_TAX_INCLUSIVE:
				$min_value        = $minimum ? $minimum->getTaxInclusive() : 0;
				$max_value        = $maximum ? $maximum->getTaxInclusive() : 0;
				$retail_value     = $retail ? $retail->getTaxInclusive() : 0;
				$calculated_value = $calculated ? $calculated->getTaxInclusive() : 0;
				$original_value   = $original ? $original->getTaxInclusive() : 0;
				$sale_value       = $sale ? $sale->getTaxInclusive() : 0;
				break;
			case Currency::DISPLAY_TAX_EXCLUSIVE:
			default:
				$min_value        = $minimum ? $minimum->getTaxExclusive() : 0;
				$max_value        = $maximum ? $maximum->getTaxExclusive() : 0;
				$retail_value     = $retail ? $retail->getTaxExclusive() : 0;
				$calculated_value = $calculated ? $calculated->getTaxExclusive() : 0;
				$original_value   = $original ? $original->getTaxExclusive() : 0;
				$sale_value       = $sale ? $sale->getTaxExclusive() : 0;
				break;
		}

		$return_data['retail_price'] = [
			'raw'       => $retail_value,
			'formatted' => $this->format_currency( $retail_value ),
		];

		if ( $min_value != $max_value ) {
			$return_data['display_type'] = 'price_range';
			$return_data['price_range']  = [
				'min' => [
					'raw'       => $min_value,
					'formatted' => $this->format_currency( $min_value ),
				],
				'max' => [
					'raw'       => $max_value,
					'formatted' => $this->format_currency( $max_value ),
				],
			];

			return $return_data;
		}

		$return_data['display_type']     = 'simple';
		$return_data['calculated_price'] = [
			'raw'       => $calculated_value,
			'formatted' => $this->format_currency( $calculated_value ),
		];

		if ( $sale_value && $sale_value < $original_value && $calculated_value < $original_value ) {
			// If the sale value and calculated value is different and less the original value, it's on sale.
			// If it's more, we shouldn't display it as a sale.
			// Calculated value might be different than Sale value if the customer is in a group with special pricing.
			$return_data['display_type']   = 'sale';
			$return_data['original_price'] = [
				'raw'       => $original_value,
				'formatted' => $this->format_currency( $original_value ),
			];
		}

		return $return_data;
	}

	/**
	 * Convert an API response object into an associative array
	 *
	 * @param object|array $object A BigCommerce API response object, or an array thereof
	 *
	 * @return array
	 */
	private function object_to_array( $object ) {
		if ( is_array( $object ) ) {
			return array_map( [ $this, 'object_to_array' ], $object );
		}
		$json = wp_json_encode( ObjectSerializer::sanitizeForSerialization( $object ) );

		return json_decode( $json, true );
	}

	/**
	 * Checks if a given request has access to read pricing.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		// no access checks for now
		return true;
	}


	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return [
			'context' => $this->get_context_param(),
			'items'   => [
				'description'       => __( 'The option values for the product to add to the cart', 'bigcommerce' ),
				'validate_callback' => 'rest_validate_request_arg',
				'required'          => true,
				'type'              => 'array',
				'items'             => [
					'type'       => 'object',
					'properties' => [
						'product_id' => [
							'type'     => 'integer',
							'required' => true,
						],
						'variant_id' => [
							'type' => 'integer',
						],
						'options'    => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'option_id' => [
										'type' => 'integer',
									],
									'value_id'  => [
										'type' => 'integer',
									],
								],
							],
						],
					],
				],
			],
		];
	}
}
