<?php


namespace BigCommerce\Rest;


use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\CartResponse;
use BigCommerce\Api\v3\Model\CartUpdateRequest;
use BigCommerce\Cart\Cart;
use BigCommerce\Cart\Cart_Mapper;
use BigCommerce\Cart\Item_Counter;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;
use BigCommerce\Templates\Cart_Empty;
use BigCommerce\Templates\Mini_Cart;
use BigCommerce\Util\Cart_Item_Iterator;
use WP_REST_Server;

class Cart_Controller extends Rest_Controller {
	private $cart_api;

	public function __construct( $namespace_base, $version, $rest_base, CartApi $cart_api ) {
		parent::__construct( $namespace_base, $version, $rest_base );
		$this->cart_api = $cart_api;
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
		$config['cart']['api_url']         = $this->get_base_url();
		$config['cart']['ajax_enabled']    = (bool) get_option( Cart_Settings::OPTION_AJAX_CART, true );
		$config['cart']['ajax_cart_nonce'] = wp_create_nonce( 'wp_rest' );

		return $config;
	}


	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->create_route_path(), [
			'args'   => [
				'context' => $this->get_context_param(),
			],
			[
				// Create a new cart with a product
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_cart' ],
				'permission_callback' => [ $this, 'create_cart_permissions_check' ],
				'args'                => [
					'product_id' => $this->product_id_param( true ),
					'variant_id' => $this->variant_id_param( false ),
					'options'    => $this->options_param( false ),
					'modifiers'  => $this->modifiers_param( false ),
					'quantity'   => $this->quantity_param( false ),
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->cart_route_path(), [
			'args'   => [
				'context' => $this->get_context_param(),
			],
			[
				// Get a cart
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'cart_id_access_check' ],
				'args'                => [
					'cart_id' => $this->cart_id_param( true ),
				],
			],
			[
				// Add a new product to a cart
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'cart_id_access_check' ],
				'args'                => [
					'cart_id'    => $this->cart_id_param( true ),
					'product_id' => $this->product_id_param( true ),
					'variant_id' => $this->variant_id_param( false ),
					'options'    => $this->options_param( false ),
					'modifiers'  => $this->modifiers_param( false ),
					'quantity'   => $this->quantity_param( false ),
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );


		register_rest_route( $this->namespace, '/' . $this->item_route_path(), [
			'args'   => [
				'context' => $this->get_context_param(),
			],
			[
				// Update the quantity for an item already in the cart
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'cart_id_access_check' ],
				'args'                => [
					'cart_id'  => $this->cart_id_param( true ),
					'item_id'  => $this->item_id_param( true ),
					'quantity' => $this->quantity_param( false ),
				],
			],
			[
				// Delete an item from the cart
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'cart_id_access_check' ],
				'args'                => [
					'cart_id' => $this->cart_id_param( true ),
					'item_id' => $this->item_id_param( true ),
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->item_route_path() . '/delete', [
			'args'   => [
				'context' => $this->get_context_param(),
			],
			[
				/*
				 * Delete an item from the cart
				 * This is a duplicate of the method above, but it doesn't use the DELETE
				 * method, because GoDaddy has decided that DELETE requests are too esoteric
				 * to be allowed
				 */
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'cart_id_access_check' ],
				'args'                => [
					'cart_id' => $this->cart_id_param( true ),
					'item_id' => $this->item_id_param( true ),
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->mini_cart_route_path(), [
			'args'   => [
				'context' => $this->get_context_param(),
			],
			[
				// Get a cart
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_mini_cart' ],
				'permission_callback' => [ $this, 'cart_id_access_check' ],
				'args'                => [
					'cart_id' => $this->cart_id_param( true ),
				],
			],
			'schema' => [ $this, 'get_rendered_item_schema' ],
		] );
	}

	private function create_route_path() {
		return $this->rest_base;
	}

	private function cart_route_path() {
		return $this->rest_base . '/(?P<cart_id>[0-9a-f\-]+)';
	}

	private function item_route_path() {
		return $this->cart_route_path() . '/items/(?P<item_id>[0-9a-f\-]+)';
	}

	private function mini_cart_route_path() {
		return $this->cart_route_path() . '/mini';
	}

	/**
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$cart_id = $request->get_param( 'cart_id' );
		if ( empty( $cart_id ) ) {
			return new \WP_Error( 'rest_cart_invalid', __( 'Cart does not exist.', 'bigcommerce' ), [ 'status' => 404 ] );
		}

		try {
			$cart = $this->get_cart_data( $cart_id );
		} catch ( ApiException $e ) {
			return new \WP_Error( 'rest_cart_invalid', __( 'Cart does not exist.', 'bigcommerce' ), [ 'status' => 404 ] );
		}

		$data = $this->prepare_item_for_response( $cart, $request );

		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepares a cart for response.
	 *
	 * @param \BigCommerce\Api\v3\Model\Cart $cart    Cart object.
	 * @param \WP_REST_Request               $request Request object.
	 *
	 * @return \WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $cart, $request ) {
		$mapper   = new Cart_Mapper( $cart );
		$response = $mapper->map();

		return rest_ensure_response( $response );
	}

	public function create_cart_permissions_check( $request ) {
		return true; // no access control
	}

	/**
	 * Checks that the user's cart cookie matches the cart in the request
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function cart_id_access_check( $request ) {
		return $request->get_param( 'cart_id' ) === $this->get_cart_id();
	}

	private function cart_id_param( $required = false ) {
		return [
			'description'       => __( 'The alphanumeric identifier for the cart' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => $required,
		];
	}

	private function item_id_param( $required = false ) {
		return [
			'description'       => __( 'The alphanumeric identifier for the item', 'bigcommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => $required,
		];
	}

	private function product_id_param( $required = false ) {
		return [
			'description'       => __( 'The BigCommerce ID of the product to add to the cart', 'bigcommerce' ),
			'type'              => 'integer',
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => $required,
		];
	}

	private function variant_id_param( $required = false ) {
		return [
			'description'       => __( 'The BigCommerce ID of the variant to add to the cart', 'bigcommerce' ),
			'type'              => 'integer',
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => $required,
		];
	}

	private function options_param( $required = false ) {
		return [
			'description'       => __( 'The option values for the product to add to the cart', 'bigcommerce' ),
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => $required,
			'type'              => 'array',
			'items'             => [
				'type'       => 'object',
				'properties' => [
					'id'    => [
						'description' => __( 'The option field ID', 'bigcommerce' ),
						'type'        => 'integer',
					],
					'value' => [
						'description' => __( 'The option value', 'bigcommerce' ),
						'type'        => 'string',
					],
				],
			],
		];
	}

	private function modifiers_param( $required = false ) {
		return [
			'description'       => __( 'The modifier values for the product to add to the cart. Deprecated since 1.7.0.', 'bigcommerce' ),
			'validate_callback' => 'rest_validate_request_arg',
			'required'          => $required,
			'type'              => 'array',
			'items'             => [
				'type'       => 'object',
				'properties' => [
					'id'    => [
						'description' => __( 'The modifier field ID', 'bigcommerce' ),
						'type'        => 'integer',
					],
					'value' => [
						'description' => __( 'The modifier value', 'bigcommerce' ),
						'type'        => 'string',
					],
				],
			],
		];
	}

	private function quantity_param( $required = false ) {
		return [
			'description'       => __( 'The quantity of the item that should be in the cart', 'bigcommerce' ),
			'type'              => 'integer',
			'validate_callback' => 'rest_validate_request_arg',
			'default'           => 1,
			'required'          => $required,
		];
	}

	/**
	 * Creates the cart with an item in it
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function create_cart( $request ) {

		try {
			$response = $this->add_to_cart( $request );
		} catch ( Product_Not_Found_Exception $e ) {
			return new \WP_Error( 'rest_cannot_create', __( 'Product not found.', 'bigcommerce' ), [
				'status' => 404,
			] );
		} catch ( ApiException $e ) {
			if ( strpos( (string) $e->getCode(), '4' ) === 0 ) {
				$body = $e->getResponseBody();
				if ( $body && ! empty( $body->title ) ) {
					$message = sprintf( '[%d] %s', $e->getCode(), $body->title );
				} else {
					$message = $e->getMessage();
				}

				return new \WP_Error( 'rest_cannot_create', sprintf(
					__( 'Error creating your cart. Error message: "%s"', 'bigcommerce' ),
					$message
				), [ 'status' => $e->getCode() ] );
			}

			return new \WP_Error( 'rest_cannot_create', __( 'Error creating your cart. It might be out of stock or unavailable.', 'bigcommerce' ), [
				'status' => $e->getCode(),
			] );
		}

		$response = rest_ensure_response( $this->prepare_item_for_response( $response, $request ) );

		return $response;
	}

	/**
	 * Adds an item to the cart.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {

		try {
			$response = $this->add_to_cart( $request );
		} catch ( Product_Not_Found_Exception $e ) {
			return new \WP_Error( 'rest_cannot_create', __( 'Product not found.', 'bigcommerce' ), [
				'status' => 404,
			] );
		} catch ( ApiException $e ) {
			if ( strpos( (string) $e->getCode(), '4' ) === 0 ) {
				$body = $e->getResponseBody();
				if ( $body && ! empty( $body->title ) ) {
					$message = sprintf( '[%d] %s', $e->getCode(), $body->title );
				} else {
					$message = $e->getMessage();
				}

				return new \WP_Error( 'rest_cannot_create', sprintf(
					__( 'Error updating your cart. Error message: "%s"', 'bigcommerce' ),
					$message
				), [ 'status' => $e->getCode() ] );
			}

			return new \WP_Error( 'rest_cannot_create', __( 'Error updating your cart. It might be out of stock or unavailable.', 'bigcommerce' ), [
				'status' => $e->getCode(),
			] );
		}

		$response = rest_ensure_response( $this->prepare_item_for_response( $response, $request ) );

		return $response;
	}

	/**
	 * Adds an item to the cart. Creates a cart if necessary.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 *
	 * @throws ApiException
	 * @throws Product_Not_Found_Exception
	 */
	private function add_to_cart( $request ) {

		$product_id = $request->get_param( 'product_id' );
		$quantity   = $request->get_param( 'quantity' );
		$options    = [];

		$product = Product::by_product_id( $product_id );

		$submitted_options = wp_list_pluck( (array) $request->get_param( 'options' ), 'value', 'id' );

		$option_config   = $product->options();
		$modifier_config = $product->modifiers();
		foreach ( $option_config as $config ) {
			if ( array_key_exists( $config['id'], $submitted_options ) ) {
				$options[ $config['id'] ] = absint( $submitted_options[ $config['id'] ] );
			}
		}
		foreach ( $modifier_config as $config ) {
			if ( array_key_exists( $config['id'], $submitted_options ) ) {
				$options[ $config['id'] ] = $this->sanitize_option( $submitted_options[ $config['id'] ], $config );
			}
		}

		$cart = new Cart( $this->cart_api );

		return $cart->add_line_item( $product_id, $options, $quantity );
	}

	private function sanitize_option( $value, $config ) {
		switch ( $config['type'] ) {
			case 'date':
				return strtotime( $value );
			case 'multi_line_text':
				return sanitize_textarea_field( $value );
			case 'numbers_only_text':
				return filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND );
			case 'text':
				return sanitize_text_field( $value );
			default: // checkboxes, selects, and radios
				return (int) $value;
		}
	}

	protected function create_postable_line_item( \WP_REST_Request $request ) {
		$item = [
			'quantity'   => $request->get_param( 'quantity' ),
			'product_id' => $request->get_param( 'product_id' ),
			'variant_id' => $request->get_param( 'variant_id' ),
		];


		return $item;
	}

	/**
	 * Deletes one item from the cart.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$cart_id = $request->get_param( 'cart_id' );
		$item_id = $request->get_param( 'item_id' );
		try {
			/**
			 * @var CartResponse $cart_response
			 * @var int          $status_code
			 */
			list( $cart_response, $status_code ) = $this->cart_api->cartsCartIdItemsItemIdDeleteWithHttpInfo( $cart_id, $item_id );

			if ( $status_code == 204 || empty( $cart_response ) || ! $cart_response->getData() ) {
				$response = rest_ensure_response( '' );
				$response->set_status( 204 );

				return $response;
			} else {
				$cart = $cart_response->getData();
			}
		} catch ( ApiException $e ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'Error deleting item', 'bigcommerce' ), [ 'status' => 502 ] );
		}

		$data = $this->prepare_item_for_response( $cart, $request );

		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Updates one item from the cart.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$cart_id  = $request->get_param( 'cart_id' );
		$item_id  = $request->get_param( 'item_id' );
		$quantity = $request->get_param( 'quantity' );

		$item = $this->get_cart_item( $cart_id, $item_id );
		if ( is_wp_error( $item ) ) {
			return $item;
		}

		$updated      = [
			'quantity'   => $quantity,
			'product_id' => $item->getProductId(),
			'variant_id' => $item->getVariantId(),
		];
		$request_data = new CartUpdateRequest( [
			'line_item' => $updated,
		] );

		try {
			$this->cart_api->cartsCartIdItemsItemIdPut( $cart_id, $item_id, $request_data );
			$include = [
				'line_items.physical_items.options',
				'line_items.digital_items.options',
				'redirect_urls',
			];
			$cart    = $this->cart_api->cartsCartIdGet( $cart_id, [ 'include' => $include ] )->getData();
		} catch ( ApiException $e ) {
			return new \WP_Error( 'rest_cannot_update', __( 'Cannot update cart', 'bigcommerce' ), [ 'status' => 502 ] );
		}

		$data = $this->prepare_item_for_response( $cart, $request );

		$response = rest_ensure_response( $data );

		return $response;
	}


	/**
	 * Get the item, if the ID is valid.
	 *
	 * @param string $cart_id
	 * @param string $item_id
	 *
	 * @return \BigCommerce\Api\v3\Model\BaseItem|\WP_Error Term object if ID is valid, WP_Error otherwise.
	 */
	protected function get_cart_item( $cart_id, $item_id ) {
		$error = new \WP_Error( 'rest_item_invalid', __( 'Item does not exist.', 'bigcommerce' ), [ 'status' => 404 ] );

		if ( empty( $item_id ) ) {
			return $error;
		}

		if ( empty( $cart_id ) ) {
			return $error;
		}

		try {
			$cart = $this->get_cart_data( $cart_id );
		} catch ( ApiException $e ) {
			return $error;
		}

		foreach ( Cart_Item_Iterator::factory( $cart ) as $bc_id => $item ) {
			if ( $bc_id == $item_id ) {
				return $item;
			}
		}

		return $error;
	}

	/**
	 * @param $cart_id
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 * @throws ApiException
	 */
	protected function get_cart_data( $cart_id ) {
		$include = [
			'line_items.physical_items.options',
			'line_items.digital_items.options',
			'redirect_urls',
		];

		return $this->cart_api->cartsCartIdGet( $cart_id, [ 'include' => $include ] )->getData();
	}

	/**
	 * Retrieves the response's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title'   => 'bigcommerce_cart',
			'type'    => 'object',

			'properties' => [
				'cart_id'         => [
					'description' => __( 'BigCommerce identifier for the cart.', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'base_amount'     => [
					'description' => __( "Cost of cart’s contents, before applying discounts.", 'bigcommerce' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
					'properties'  => [
						'raw'        => [
							__( 'The unformatted value', 'bigcommerce' ),
							'type' => 'string',
						],
						'formatted' => [
							__( 'The unformatted value', 'bigcommerce' ),
							'type' => 'string',
						],
					],
				],
				'discount_amount' => [
					'description' => __( "Discounted amount.", 'bigcommerce' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
					'properties'  => [
						'raw'        => [
							__( 'The unformatted value', 'bigcommerce' ),
							'type' => 'string',
						],
						'formatted' => [
							__( 'The unformatted value', 'bigcommerce' ),
							'type' => 'string',
						],
					],
				],
				'cart_amount'     => [
					'description' => __( "Sum of line-items amounts, minus cart-level discounts and coupons. This amount includes taxes (where applicable).", 'bigcommerce' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
					'properties'  => [
						'raw'        => [
							__( 'The unformatted value', 'bigcommerce' ),
							'type' => 'string',
						],
						'formatted' => [
							__( 'The unformatted value', 'bigcommerce' ),
							'type' => 'string',
						],
					],
				],
				'items'           => [
					'description' => __( 'The items in the cart', 'bigcommerce' ),
					'type'        => 'array',
					'items'       => [
						'type'       => 'object',
						'context'    => [ 'view', 'edit', 'embed' ],
						'properties' => [
							'id'               => [
								'description' => __( 'The cart item ID', 'bigcommerce' ),
								'type'        => 'string',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'variant_id'       => [
								'description' => __( 'The variant ID corresponding to the selected options', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'product_id'       => [
								'description' => __( 'The base product ID', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'post_id'          => [
								'description' => __( 'The WordPress post ID', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'thumbnail_id'     => [
								'description' => __( 'The WordPress featured image ID', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'name'             => [
								'description' => __( 'The product name', 'bigcommerce' ),
								'type'        => 'string',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'quantity'         => [
								'description' => __( 'The selected quantity for the item', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
							],
							'list_price'       => [
								'description' => __( 'The list price of the item', 'bigcommerce' ),
								'type'        => 'object',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
								'properties'  => [
									'raw'        => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
									'formatted' => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
								],
							],
							'sale_price'       => [
								'description' => __( 'The sale price of the item', 'bigcommerce' ),
								'type'        => 'object',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
								'properties'  => [
									'raw'        => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
									'formatted' => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
								],
							],
							'total_list_price' => [
								'description' => __( 'The total list price based on the quantity selected', 'bigcommerce' ),
								'type'        => 'object',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
								'properties'  => [
									'raw'        => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
									'formatted' => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
								],
							],
							'total_sale_price' => [
								'description' => __( 'The total sale price based on the quantity selected', 'bigcommerce' ),
								'type'        => 'object',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
								'properties'  => [
									'raw'        => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
									'formatted' => [
										__( 'The unformatted value', 'bigcommerce' ),
										'type' => 'string',
									],
								],
							],
							'is_featured'      => [
								'description' => __( 'If the product is featured', 'bigcommerce' ),
								'type'        => 'boolean',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'on_sale'          => [
								'description' => __( 'If the product is on sale', 'bigcommerce' ),
								'type'        => 'boolean',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'show_condition'   => [
								'description' => __( 'If the product condition should be displayed', 'bigcommerce' ),
								'type'        => 'boolean',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'sku'              => [
								'description' => __( 'The item SKU', 'bigcommerce' ),
								'type'        => 'object',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
								'properties'  => [
									'product' => [
										__( 'The SKU of the product', 'bigcommerce' ),
										'type' => 'string',
									],
									'variant' => [
										__( 'The SKU of the variant', 'bigcommerce' ),
										'type' => 'string',
									],
								],
							],
							'options'          => [
								'description' => __( 'The options selected for the item', 'bigcommerce' ),
								'type'        => 'array',
								'items'       => [
									'type'       => 'object',
									'context'    => [ 'view', 'edit', 'embed' ],
									'properties' => [
										'label' => [
											__( 'The option group label', 'bigcommerce' ),
											'type' => 'string',
										],
										'key'   => [
											__( 'The option ID', 'bigcommerce' ),
											'type' => 'string',
										],
										'value' => [
											__( 'The option display value', 'bigcommerce' ),
											'type' => 'string',
										],
									],
								],
							],
							'minimum_quantity' => [
								'description' => __( 'The minimum purchase amount.', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'maximum_quantity' => [
								'description' => __( 'The maximum purchase amount. 0 for no limit', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
							'inventory_level'  => [
								'description' => __( 'Remaining inventory for the item. -1 for no limit', 'bigcommerce' ),
								'type'        => 'int',
								'context'     => [ 'view', 'edit', 'embed' ],
								'readonly'    => true,
							],
						],
					],
				],
			],
		];


		$taxonomies = [
			Availability::NAME,
			Condition::NAME,
			Product_Type::NAME,
			Brand::NAME,
			Product_Category::NAME,
		];

		foreach ( $taxonomies as $tax ) {
			$schema['properties']['items']['items']['properties'][ $tax ] = [
				'description' => sprintf( __( 'A term from the %s taxonomy', 'bigcommerce' ), $tax ),
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'context'    => [ 'view', 'edit', 'embed' ],
					'properties' => [
						'id'    => __( 'The term ID', 'bigcommerce' ),
						'label' => __( 'The term label', 'bigcommerce' ),
						'slug'  => __( 'The term slug', 'bigcommerce' ),
					],
				],
			];
		}

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Render the current user's mini-cart, and return as a rest
	 * response object
	 *
	 * @return \WP_REST_Response
	 */
	public function get_mini_cart() {
		$cart_id = $this->get_cart_id();
		if ( empty( $cart_id ) ) {
			return $this->get_empty_mini_cart();
		}
		try {
			$cart     = $this->fetch_cart( $cart_id );
			$mapper   = new Cart_Mapper( $cart );
			$template = Mini_Cart::factory( [
				Mini_Cart::CART => $mapper->map(),
			] );

			return rest_ensure_response( [
				'rendered' => $template->render(),
				'count'    => Item_Counter::count_bigcommerce_cart( $cart ),
			] );
		} catch ( ApiException $e ) {
			return $this->get_empty_mini_cart();
		}
	}

	/**
	 * Render an empty mini-cart and return as a rest response object
	 *
	 * @return \WP_REST_Response
	 */
	private function get_empty_mini_cart() {
		$template = Cart_Empty::factory( [
			Cart_Empty::CART => [
				'cart_id'         => '',
				'base_amount'     => 0,
				'discount_amount' => 0,
				'cart_amount'     => 0,
				'items'           => [],
			],
		] );

		return rest_ensure_response( [
			'rendered' => $template->render(),
			'count'    => 0,
		] );
	}

	private function get_cart_id() {
		$cart = new Cart( $this->cart_api );

		return $cart->get_cart_id();
	}

	/**
	 * Fetch a cart from the BigCommerce API
	 *
	 * @param string $cart_id
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 * @throws ApiException
	 */
	private function fetch_cart( $cart_id ) {
		$include = [
			'line_items.physical_items.options',
			'line_items.digital_items.options',
			'redirect_urls',
		];

		return $this->cart_api->cartsCartIdGet( $cart_id, [ 'include' => $include ] )->getData();
	}

	public function get_rendered_item_schema() {
		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'bigcommerce_cart_rendered',
			'type'       => 'object',
			'properties' => [
				'rendered' => [
					'description' => __( 'The rendered template', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'count'    => [
					'description' => __( 'The number of items in the cart', 'bigcommerce' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
			],
		];

		return $schema;
	}
}
