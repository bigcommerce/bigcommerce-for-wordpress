<?php
/**
 * This class adds a new Proxy endpoint via the WordPress REST API.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Proxy;

use WP_REST_Controller, WP_REST_Server, WP_REST_Response, WP_REST_Request;

/**
 * Proxy_Controller class
 */
class Proxy_Controller extends WP_REST_Controller {

	/**
	 * Configuration
	 *
	 * @var array Configurations.
	 */
	private $config = [
		'proxy_base' => 'bc/v3',
	];

	/**
	 * Proxy base namespace.
	 *
	 * @var string
	 */
	protected $proxy_base;

	/**
	 * Proxy_Controller class constructor
	 *
	 * @param array $config Configuration details.
	 */
	public function __construct( array $config ) {
		$this->config     = $config;
		$this->proxy_base = $this->config['proxy_base'];
	}

	/**
	 * Init Proxy endpoints.
	 *
	 * @return void
	 */
	public function register_routes() {
		$public_args = [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_items' ],
			'permission_callback' => [ $this, 'get_items_permissions_check' ],
		];

		/**
		 * Proxy for GET requests.
		 */
		register_rest_route( $this->proxy_base, '/catalog/.*', $public_args );
		register_rest_route( $this->proxy_base, '/channels(\/?.*?)', $public_args );

		register_rest_route(
			$this->proxy_base,
			'/carts(\/?$)',
			[
				[
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => [ $this, 'create_cart' ],
				],
			]
		);
		register_rest_route(
			$this->proxy_base,
			'/carts/([^/]*\/?$)',
			[
				$public_args,
				[
					'methods'  => [ WP_REST_Server::DELETABLE, WP_REST_SERVER::CREATABLE ],
					'callback' => [ $this, 'delete_cart' ],
				],
			]
		);
		register_rest_route(
			$this->proxy_base,
			'/carts/(.*)/items/(.*)',
			[
				[
					'methods'  => [ WP_REST_Server::DELETABLE, WP_REST_SERVER::CREATABLE ],
					'callback' => [ $this, 'update_cart_item' ],
				],
			]
		);
		register_rest_route(
			$this->proxy_base,
			'/carts/(.*)/redirect_urls(\/?$)',
			[
				[
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => [ $this, 'create_redirect_url' ],
				],
			]
		);
	}

	/**
	 * Creates a new cart and returns the BigCommerce API response.
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @return WP_REST_Response
	 */
	public function create_cart( $request ) {
		$route = $this->route( $request );

		$response = wp_remote_request(
			$route,
			[
				'method'  => 'POST',
				'headers' => $this->get_request_headers( $request, $route ),
				'body'    => wp_json_encode(
					[
						'line_items' => $request['line_items'],
					]
				),
			]
		);

		return rest_ensure_response( json_decode( wp_remote_retrieve_body( $response ), true ) );
	}

	/**
	 * Creates redirect URLs from a cart ID.
	 *
	 * @param WP_REST_Request $request Request instance.
	 * @return WP_REST_Response
	 */
	public function create_redirect_url( $request ) {
		$route = $this->route( $request );

		$response = wp_remote_request(
			$route,
			[
				'method'  => 'POST',
				'headers' => $this->get_request_headers( $request, $route ),
			]
		);

		return rest_ensure_response( json_decode( wp_remote_retrieve_body( $response ), true ) );
	}

	/**
	 * Deletes a cart.
	 *
	 * @param WP_REST_Request $request Request instance.
	 * @return WP_REST_Response
	 */
	public function delete_cart( $request ) {
		$route = $this->route( $request );

		$response = wp_remote_request(
			$route,
			[
				'method'  => 'DELETE',
				'headers' => $this->get_request_headers( $request, $route ),
			]
		);

		return rest_ensure_response( json_decode( wp_remote_retrieve_body( $response ), true ) );
	}

	/**
	 * Updates or deletes a cart line item.
	 *
	 * @param WP_REST_Request $request Request instance.
	 * @return WP_REST_Response
	 */
	public function update_cart_item( $request ) {
		$route = $this->route( $request );

		if ( $request['cartId'] ) {
			$route = str_replace( '_cart_id_', $request['cartId'], $route );
		}

		$params = $request->get_body_params();
		if ( empty( $params ) ) {
			$params = json_decode( $request->get_body(), true );
		}

		$args = [
			'method'  => ! empty( $request->get_param( 'delete' ) ) ? WP_REST_SERVER::DELETABLE : $request->get_method(),
			'headers' => $this->get_request_headers( $request, $route ),
		];

		if ( 'POST' === $args['method'] ) {
			$args['method'] = 'PUT'; // BigCommerce requires PUT for this endpoint.
		}

		if ( $params['line_item'] ) {
			$args['body'] = wp_json_encode(
				[
					'line_item' => $params['line_item'],
				]
			);
		} elseif ( 'POST' === $request->get_method() ) {
			// Allow the line_item JSON object to be built from HTML form inputs.
			$body = [ 'line_item' => [] ];

			foreach ( [ 'product_id', 'variant_id', 'quantity' ] as $field ) {
				if ( $params[ $field ] ) {
					$body['line_item'][ $field ] = $params[ $field ];
				}
			}

			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_request( $route, $args );

		return rest_ensure_response( json_decode( wp_remote_retrieve_body( $response ), true ) );
	}

	/**
	 * Provides request headers for use in multiple methods.
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @param string          $route The BigCommerce request route.
	 * @return array A headers associative array.
	 */
	public function get_request_headers( $request, $route ) {
		/**
		 * Filter the request headers.
		 *
		 * @param array            $        Header KV pairs.
		 * @param string           $route   Route requested.
		 * @param WP_REST_Request $request API request.
		 */
		return apply_filters(
			'bigcommerce/proxy/request_headers',
			[
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'X-Auth-Client' => $this->config['client_id'],
				'X-Auth-Token'  => $this->config['access_token'],
			],
			$route,
			$request
		);
	}

	/**
	 * Permission check for REST requests to the proxy endpoint.
	 *
	 * @param WP_REST_Request $request REST request to check.
	 * @return \WP_Error|true True if the request is allowed, or else an error.
	 */
	public function get_items_permissions_check( $request ) {
		/**
		 * Filters whether the REST request should run.
		 *
		 * @param WP_REST_Request $request
		 */
		return apply_filters( 'bigcommerce/proxy/response_override', true, $request );
	}

	/**
	 * Proxy requests.
	 *
	 * @param WP_REST_Request $request Proxied request.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_items( $request ) {

		/**
		 * Filters a proxy REST request before it is run.
		 *
		 * @param WP_REST_Request $request Original request.
		 */
		$request = apply_filters( 'bigcommerce/proxy/request', $request );

		/**
		 * Fires before a proxy REST request is run.
		 *
		 * @param WP_REST_Request $request API request.
		 */
		do_action( 'bigcommerce/proxy/request_received', $request );

		/**
		 * Pre-fetch results. Can be from database or from cache.
		 *
		 * @param array $ Empty array.
		 * @param WP_REST_Request $request API request.
		 * @param array            $        Proxy configuration.
		 */
		$result = apply_filters( 'bigcommerce/proxy/result_pre', [], $request );

		$params = $request->get_params();

		// Get the route.
		$route = $this->route( $request );

		// We have no results, use the BigCommerce API.
		if ( is_wp_error( $result ) || empty( $result ) ) {

			// Request arguments.
			$args = [
				'headers' => $this->get_request_headers( $request, $route ),
			];

			// Proxy the request.
			$response = wp_remote_get( add_query_arg( $params, $route ), $args );

			/**
			 * Raw API response received.
			 *
			 * @param object|array     $response Response from BigCommerce API.
			 * @param string           $route    Route requested.
			 * @param WP_REST_Request $request  API request.
			 */
			do_action( 'bigcommerce/proxy/raw_response_received', $response, $route, $request );

			if ( 200 !== $response['response']['code'] || is_wp_error( $response ) ) {
				return $response;
			}

			$result         = json_decode( $response['body'], true );
			$result['data'] = $this->filter_excluded_fields( $request->get_route(), $result['data'] );

			/**
			 * Do something with the response before it is returned. E.g. Import the product(s) and/or cache it.
			 *
			 * @param array|\WP_Error  $result  Result from API call.
			 * @param WP_REST_Request $request API request.
			 */
			do_action( 'bigcommerce/proxy/response_received', $result, $request );
		}

		/**
		 * Filter the response results before returning it.
		 *
		 * @param array|\WP_Error  $result  Result from API call.
		 * @param string           $route   Route requested.
		 * @param WP_REST_Request $request API request.
		 */
		$result = apply_filters( 'bigcommerce/proxy/result', $result, $route, $request );

		$rest_response = new WP_REST_Response( $result );

		/**
		 * Filter the WordPress REST response before it gets dispatched.
		 *
		 * @param WP_REST_Response $rest_response Response to send back to request..
		 * @param string            $route         Route requested.
		 * @param WP_REST_Request  $request       API request.
		 */
		$rest_response = apply_filters( 'bigcommerce/proxy/rest_response', $rest_response, $route, $request );

		return rest_ensure_response( $rest_response );
	}

	/**
	 * Filters specified fields out of an array of data.
	 *
	 * @param array $data Data to filter.
	 * @param array $exclude_fields Fields to exclude, by key.
	 * @return array Filtered data.
	 */
	private function filter_out_fields( $data, $exclude_fields ) {
		foreach ( $exclude_fields as $field ) {
			if ( array_key_exists( $field, $data ) ) {
				unset( $data[ $field ] );
			}
		}

		return $data;
	}

	/**
	 * Filters fields out of product data.
	 *
	 * @param array $product Product data.
	 * @return array Filtered data.
	 */
	private function filter_excluded_product_fields( $product ) {
		$exclude_fields = [
			'bin_picking_number',
			'cost_price',
			'date_created',
			'date_modified',
			'inventory_tracking',
			'layout_file',
			'product_tax_code',
			'search_keywords',
			'sku_id',
			'tax_class_id',
			'total_sold',
			'view_count',
		];

		if ( true === $product['is_price_hidden'] ) {
			$exclude_fields = array_merge(
				$exclude_fields,
				[
					'price',
					'retail_price',
					'sale_price',
					'map_price',
				]
			);
		}

		if ( true === $product['is_condition_shown'] ) {
			$exclude_fields[] = 'condition';
		}

		if ( true !== $product['is_preorder_only'] ) {
			$exclude_fields = array_merge(
				$exclude_fields,
				[
					'preorder_release_date',
					'preorder_message',
				]
			);
		}

		if ( isset( $product['variants'] ) ) {
			foreach ( $product['variants'] as &$variant ) {
				$variant = $this->filter_excluded_variant_fields( $variant, $product );
			}
		}

		if ( isset( $product['images'] ) ) {
			foreach ( $product['images'] as &$image ) {
				$image = $this->filter_excluded_image_fields( $image );
			}
		}

		return $this->filter_out_fields( $product, $exclude_fields );
	}

	/**
	 * Filters fields out of a variant.
	 *
	 * @param array      $variant Variant data.
	 * @param array|null $product_data Optionally passed in data for the variant's associated product.
	 * @return array Filtered data.
	 */
	private function filter_excluded_variant_fields( $variant, $product_data = null ) {
		$exclude_fields = [
			'sku_id',
			'cost_price',
		];

		if ( empty( $product_data ) || $variant['product_id'] !== $product_data['id'] ) {
			// This takes a long time if not cached.
			$product_request = new WP_REST_Request(
				'GET',
				sprintf( '/%scatalog/products/%d', trailingslashit( $this->proxy_base ), $variant['product_id'] )
			);

			$response     = rest_do_request( $product_request );
			$product_data = isset( $response->data['data'] ) ? $response->data['data'] : [];
		}

		if ( empty( $product_data ) || true === $product_data['is_price_hidden'] ) {
			$exclude_fields = array_merge(
				$exclude_fields,
				[
					'price',
					'calculated_price',
					'sale_price',
					'retail_price',
					'map_price',
				]
			);
		}

		return $this->filter_out_fields( $variant, $exclude_fields );
	}

	/**
	 * Filters fields out of image data.
	 *
	 * @param array $image Image data.
	 * @return array Filtered data.
	 */
	private function filter_excluded_image_fields( $image ) {
		$exclude_fields = [ 'date_modified' ];

		return $this->filter_out_fields( $image, $exclude_fields );
	}

	/**
	 * Filters fields out of review data.
	 *
	 * @param array $review Review data.
	 * @return array|null Filtered data or null if the idea review should be filtered out.
	 */
	private function filter_excluded_review_fields( $review ) {
		if ( 'approved' !== $review['status'] ) {
			return null;
		}

		$exclude_fields = [
			'email',
			'status',
			'date_created',
			'date_modified',
			'date_reviewed',
		];

		return $this->filter_out_fields( $review, $exclude_fields );
	}

	/**
	 * Filters fields out of category data.
	 *
	 * @param array $category Category data.
	 * @return array Filtered data.
	 */
	private function filter_excluded_category_fields( $category ) {
		$exclude_fields = [ 'views' ];

		return $this->filter_out_fields( $category, $exclude_fields );
	}

	/**
	 * Provides blacklisted fields for the given request route.
	 *
	 * @param string $route WP REST request route.
	 * @param array  $data Data to filter.
	 * @return array Exclude fields parameter or an empty array if nothing to exclude.
	 */
	private function filter_excluded_fields( $route, $data ) {
		$regex_proxy_base = preg_quote( trailingslashit( $this->proxy_base ), '/' );

		// Regex checks for a product listing.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/?$/', $route ) ) {
			foreach ( $data as &$product ) {
				$product = $this->filter_excluded_product_fields( $product );
			}

			return $data;
		}

		// Regex checks for a single product.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/?$/', $route ) ) {
			$data = $this->filter_excluded_product_fields( $data );
			return $data;
		}

		// Regex checks for a variants listing.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/variants\/?$/', $route ) ) {
			foreach ( $data as &$variant ) {
				$variant = $this->filter_excluded_variant_fields( $variant );
			}

			return $data;
		}

		// Regex checks for a single variant.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/variants\/\d+\/?$/', $route ) ) {
			$data = $this->filter_excluded_variant_fields( $data );
			return $data;
		}

		// Regex checks for an image listing.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/images\/?$/', $route ) ) {
			foreach ( $data as &$image ) {
				$image = $this->filter_excluded_image_fields( $image );
			}

			return $data;
		}

		// Regex checks for a single image.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/images\/\d+\/?$/', $route ) ) {
			$data = $this->filter_excluded_image_fields( $data );
			return $data;
		}

		// Regex checks for a review listing.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/reviews\/?$/', $route ) ) {
			foreach ( $data as &$review ) {
				$review = $this->filter_excluded_review_fields( $review );
			}

			return $data;
		}

		// Regex checks for a single review.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/products\/\d+\/reviews\/\d+\/?$/', $route ) ) {
			$data = $this->filter_excluded_review_fields( $data );
			return $data;
		}

		// Regex checks for a category listing.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/categories\/?$/', $route ) ) {
			foreach ( $data as &$category ) {
				$category = $this->filter_excluded_category_fields( $category );
			}

			return $data;
		}

		// Regex checks for a single category.
		if ( preg_match( '/^\/' . $regex_proxy_base . 'catalog\/categories\/\d+\/?$/', $route ) ) {
			$data = $this->filter_excluded_category_fields( $data );
			return $data;
		}

		return $data;
	}

	/**
	 * Given a request, return the real URL.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return string
	 */
	public function route( WP_REST_Request $request ) {
		$route = str_replace( '/' . $this->proxy_base . '/', '', $request->get_route() );
		$route = sprintf( '%s%s', trailingslashit( $this->config['host'] ), $route );

		return apply_filters( 'bigcommerce/proxy/request_url', $route, $request );
	}
}
