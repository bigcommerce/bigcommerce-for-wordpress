<?php


namespace BigCommerce\Rest;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Shortcodes;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Shortcode_Controller extends Products_Controller {

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args'                => $this->get_item_params(),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/html', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_rendered_item' ],
				'permission_callback' => [ $this, 'get_rendered_item_permissions_check' ],
				'args'                => $this->get_rendered_item_params(),
			],
			'schema' => [ $this, 'get_rendered_item_schema' ],
		] );
	}


	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 4.7.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_item_params() {
		$params = parent::get_collection_params();
		unset( $params[ 'page' ] );
		$params[ 'per_page' ][ 'default' ] = 0;
		$params[ 'per_page' ][ 'minimum' ] = 0;

		$params[ 'post_id' ] = [
			'description' => __( 'Limit results to only those items with the specified post IDs.', 'bigcommerce' ),
			'type'        => 'array',
			'items'       => [
				'type' => 'integer',
			],
			'default'     => [],
		];

		$params[ 'bc_id' ] = [
			'description' => __( 'Limit results to only those items with the specified product IDs.', 'bigcommerce' ),
			'type'        => 'array',
			'items'       => [
				'type' => 'integer',
			],
			'default'     => [],
		];

		return $params;
	}

	public function get_rendered_item_params() {
		$params = [];
		foreach ( Shortcodes\Products::default_attributes() as $key => $default ) {
			$params[ $key ] = [
				'type'    => is_int( $default ) ? 'integer' : 'string',
				'default' => $default,
			];
		}

		return $params;
	}


	/**
	 * Checks if a given request has access to read shortcodes.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		// no access checks for now
		return true;
	}


	/**
	 * Checks if a given request has access to read the rendered shortcodes.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_rendered_item_permissions_check( $request ) {
		// no access checks for now
		return true;
	}


	/**
	 * Retrieves a single shortcode
	 *
	 * @since 4.7.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		if ( ! empty( $request[ 'post_id' ] ) || ! empty( $request[ 'bc_id' ] ) ) {
			$args = $this->build_selection_shortcode_args( $request );
		}
		if ( empty( $args ) ) {
			$args = $this->build_query_shortcode_args( $request );
		}
		$data     = $this->prepare_item_for_response( $args, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepares a single product output for response.
	 *
	 * @param array            $args    Shortcode args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $args, $request ) {
		$shortcode = $this->build_shortcode_string( $args );

		// Wrap the data in a response object.
		$response = rest_ensure_response( [
			'shortcode'  => $shortcode,
			'attributes' => $args,
		] );

		/**
		 * Filters the product data for a response.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param \WP_Post          $post     Post object.
		 * @param \WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'bigcommerce/rest/shortcode/prepare_item_for_response', $response, $shortcode, $request );
	}

	public function build_shortcode_string( $args ) {
		$attributes = '';
		foreach ( $args as $key => $value ) {
			$attributes .= sprintf( ' %s="%s"', $key, esc_attr( $value ) );
		}

		return sprintf( '[%s%s]', Shortcodes\Products::NAME, $attributes );
	}

	/**
	 * Translate a request into shortcode args for a product single/list
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return string[]
	 */
	protected function build_selection_shortcode_args( $request ) {
		$query_args  = $this->build_query_shortcode_args( $request );
		if ( ! empty( $request[ 'bc_id' ] ) ) {
			$bcids = array_map( 'intval', $request[ 'bc_id' ] );
		} else {
			$bcids = array_map( function ( $post_id ) {
				$product = new Product( $post_id );

				return $product->bc_id();
			}, $request['post_id'] );
		}
		$ids   = implode( ',', array_unique( array_filter( array_map( 'intval', $bcids ) ) ) );
		if ( ! empty( $ids ) ) {
			$args = [
				'id' => $ids,
			];
		}
		foreach ( $query_args as $key => $value ) {
			if ( in_array( $key, [ 'order', 'orderby', 'per_page' ] ) ) {
				$args[ $key ] = $value;
			}
		}

		return apply_filters( 'bigcommerce/rest/shortcode/selection', $args, $request );
	}


	/**
	 * Translate a request into shortcode args for a product query
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return string[]
	 */
	protected function build_query_shortcode_args( $request ) {
		$args = [];
		if ( ! empty( $request[ 'search' ] ) ) {
			// Striping [] from shortcode search to avoid shortcode break
			// If you wanna keep then you may want to consider [ "&#91;" , "&#93;" ] as replacement
			$args[ 'search' ] =  str_replace( [ '[' , ']' ] , '' , $request[ 'search' ] );
		}
		if ( ! empty( $request[ Brand::NAME ] ) ) {
			$args[ 'brand' ] = implode( ',', array_filter( array_map( [
				$this,
				'term_id_to_slug',
			], $request[ Brand::NAME ] ) ) );
		}
		if ( ! empty( $request[ Product_Category::NAME ] ) ) {
			$args[ 'category' ] = implode( ',', array_filter( array_map( [
				$this,
				'term_id_to_slug',
			], $request[ Product_Category::NAME ] ) ) );
		}
		if ( ! empty( $request[ Flag::NAME ] ) ) {
			foreach ( $request[ Flag::NAME ] as $flag_id ) {
				$term = get_term( $flag_id, Flag::NAME );
				switch ( $term->slug ) {
					case Flag::FEATURED:
						$args[ 'featured' ] = 1;
						break;
					case Flag::SALE:
						$args[ 'sale' ] = 1;
						break;
				}
			}
		}
		if ( ! empty( $request[ 'per_page' ] ) ) {
			$args[ 'per_page' ] = intval( $request[ 'per_page' ] );
		}
		if ( ! empty( $request[ 'order' ] ) && strtoupper( $request[ 'order' ] ) == 'DESC' ) {
			$args[ 'order' ] = 'DESC';
		} else {
			$args[ 'order' ] = 'ASC';
		}

		if ( ! empty( $request[ 'orderby' ] ) ) {
			$args[ 'orderby' ] = $request[ 'orderby' ];
		}

		if ( ! empty( $request[ 'recent' ] ) ) {
			$args[ 'recent' ] = 1;
		}

		return apply_filters( 'bigcommerce/rest/shortcode/query', $args, $request );
	}

	private function term_id_to_slug( $term_id ) {
		return get_term_field( 'slug', $term_id );
	}

	/**
	 * Retrieves the response's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'bigcommerce_shortcode',
			'type'       => 'object',
			'properties' => [
				'shortcode'  => [
					'description' => __( 'The complete shortcode string', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'attributes' => [
					'description' => __( 'The attributes of the shortcode', 'bigcommerce' ),
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
					'type'        => 'object',
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );
	}

	public function get_rendered_item_schema() {
		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'bigcommerce_shortcode_rendered',
			'type'       => 'object',
			'properties' => [
				'rendered' => [
					'description' => __( 'The rendered shortcode string', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
			],
		];

		return $schema;
	}

	/**
	 * Retrieves a single shortcode
	 *
	 * @since 4.7.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_rendered_item( $request ) {
		$attributes = $request->get_params();
		$attributes = array_filter( $attributes, function ( $att ) {
			return isset( $att );
		} );
		$shortcode  = $this->build_shortcode_string( $attributes );
		$output     = do_shortcode( $shortcode );
		$response   = rest_ensure_response( [
			'rendered' => $output,
		] );

		return $response;
	}
}