<?php


namespace BigCommerce\Rest;


use BigCommerce\Accounts\Login;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\CartApi;
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\Model\CartResponse;
use BigCommerce\Api\v3\Model\CartUpdateRequest;
use BigCommerce\Cart\Cart_Mapper;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;
use BigCommerce\Util\Cart_Item_Iterator;
use WP_REST_Server;

class Cart_Controller extends Rest_Controller {
	private $cart_api;

	public function __construct( $namespace_base, $version, $rest_base, CartApi $cart_api ) {
		parent::__construct( $namespace_base, $version, $rest_base );
		$this->cart_api = $cart_api;
	}


	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->create_route_path(), [
			'args'   => [
				'context' => $this->get_context_param(),
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_cart' ],
				'permission_callback' => [ $this, 'create_cart_permissions_check' ],
				'args'                => [
					'product_id' => $this->product_id_param( true ),
					'variant_id' => $this->variant_id_param( true ),
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
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => [
					'cart_id' => $this->cart_id_param( true ),
				],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'create_item_permissions_check' ],
				'args'                => [
					'cart_id'    => $this->cart_id_param( true ),
					'product_id' => $this->product_id_param( true ),
					'variant_id' => $this->variant_id_param( true ),
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
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'update_item_permissions_check' ],
				'args'                => [
					'cart_id'  => $this->cart_id_param( true ),
					'item_id'  => $this->item_id_param( true ),
					'quantity' => $this->quantity_param( false ),
				],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'args'                => [
					'cart_id' => $this->cart_id_param( true ),
					'item_id' => $this->item_id_param( true ),
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
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
	 * @param  \BigCommerce\Api\v3\Model\Cart $cart    Cart object.
	 * @param \WP_REST_Request                $request Request object.
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

	public function get_items_permissions_check( $request ) {
		return true; // no access control
	}

	public function create_item_permissions_check( $request ) {
		return true; // no access control
	}

	public function update_item_permissions_check( $request ) {
		return true; // no access control
	}

	public function delete_item_permissions_check( $request ) {
		return true; // no access control
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
		$line_item = $this->create_postable_line_item( $request );
		try {
			$cart = $this->create_remote_cart( $line_item );
		} catch ( ApiException $e ) {
			return new \WP_Error( 'rest_cannot_create', __( 'Error creating cart.', 'bigcommerce' ), [
				'status' => 502,
				'error'  => $e->getMessage(),
			] );
		}

		$data = $this->prepare_item_for_response( $cart, $request );

		$response = rest_ensure_response( $data );

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
		$cart_id   = $request->get_param( 'cart_id' );
		$line_item = $this->create_postable_line_item( $request );
		try {
			$cart = $this->add_to_cart( $cart_id, $line_item );
		} catch ( ApiException $e ) {
			return new \WP_Error( 'rest_cannot_create', __( 'Error adding to cart.', 'bigcommerce' ), [
				'status' => 502,
				'error'  => $e->getMessage(),
			] );
		}

		$data = $this->prepare_item_for_response( $cart, $request );

		$response = rest_ensure_response( $data );

		return $response;
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
	 * @param array $line_item
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 * @throws ApiException
	 */
	protected function create_remote_cart( $line_item ) {
		$request = new CartRequestData( [
			'line_items' => [ $line_item ],
		] );
		$request->setGiftCertificates( [] );
		$customer_id = (int) ( is_user_logged_in() ? get_user_option( Login::CUSTOMER_ID_META, get_current_user_id() ) : 0 );
		if ( $customer_id ) {
			$request->setCustomerId( $customer_id );
		}
		$cart    = $this->cart_api->cartsPost( $request )->getData();

		return $cart;
	}

	/**
	 * Adds an item to the remote cart
	 *
	 * @param string $cart_id
	 * @param array  $line_item
	 *
	 * @return \BigCommerce\Api\v3\Model\Cart
	 * @throws ApiException
	 */
	protected function add_to_cart( $cart_id, $line_item ) {
		$request = new CartRequestData( [
			'line_items' => [ $line_item ],
		] );

		return $this->cart_api->cartsCartIdItemsPost( $cart_id, $request )->getData();
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
			 * @var int $status_code
			 */
			list($cart_response, $status_code) = $this->cart_api->cartsCartIdItemsItemIdDeleteWithHttpInfo( $cart_id, $item_id );

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
			$cart = $this->cart_api->cartsCartIdItemsItemIdPut( $cart_id, $item_id, $request_data )->getData();
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
		return $this->cart_api->cartsCartIdGet( $cart_id )->getData();
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
						'formattted' => [
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
						'formattted' => [
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
						'formattted' => [
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
									'formattted' => [
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
									'formattted' => [
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
									'formattted' => [
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
									'formattted' => [
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
			$schema[ 'properties' ][ 'items' ][ 'items' ][ 'properties' ][ $tax ] = [
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
}