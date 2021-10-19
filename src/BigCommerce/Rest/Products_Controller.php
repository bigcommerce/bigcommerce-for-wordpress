<?php


namespace BigCommerce\Rest;

use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Product\Query_Mapper;
use BigCommerce\Shortcodes;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use WP_REST_Server;

/**
 * Class Products_Controller
 *
 * REST controller to provide product information
 *
 * Usage:
 *
 * /wp-json/bigcommerce/v1/products
 *
 * Query Args:
 *  - per_page: results per page, defaults to 10
 *  - page: which page of results, defaults to 1
 *  - search: search string to filter results
 *  - bigcommerce_category: Product category term IDs, accepts array or comma delimited term IDs
 *  - bigcommerce_brand: Product brand term IDs, accepts array or comma delimited term IDs
 *  - bigcommerce_flag: Product flag term IDs (e.g., featured, sale), accepts array or comma delimited term IDs
 *  - order: sort results by title. Valid values are 'asc' or 'desc' (case sensitive), defaults to 'asc'.
 */
class Products_Controller extends Rest_Controller {
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->products_query_route_path(), [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	private function products_query_route_path() {
		return $this->rest_base;
	}

	/**
	 * Checks if a given request has access to read products.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		// no access checks for now
		return true;
	}


	/**
	 * Retrieves a collection of products.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$mapper = new Query_Mapper();
		$args   = $mapper->map_rest_args_to_query( $request->get_params() );

		/**
		 * Filters rest products query.
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request request.
		 */
		$query_args = apply_filters( 'bigcommerce/rest/products_query', $args, $request );

		$query_args['post_type']      = Product::NAME;
		$query_args['post_status']    = 'publish';
		$query_args['posts_per_page'] = 12;
		if ( ! empty( $query_args['bigcommerce_id__in'] ) ) {
			$query_args['posts_per_page'] = - 1;
		}

		$channel_filter = $this->get_channel_filter( $request->get_param( Channel::NAME ) );
		add_action( 'pre_get_posts', $channel_filter, 9, 1 ); // run before Query_Filter::set_tax_query()

		$posts_query  = new \WP_Query();
		$query_result = $posts_query->query( $query_args );

		$posts = [];

		foreach ( $query_result as $post_id ) {
			$bcid = get_post_meta( $post_id, Product::BIGCOMMERCE_ID, true );
			$data = $this->prepare_item_for_response( get_post( $post_id ), $request );
			// ensure that we only have one result per BCID, no matter how many channels it's in
			$posts[ $bcid ] = $this->prepare_response_for_collection( $data );
		}

		$page        = (int) $query_args['paged'];
		$total_posts = $posts_query->found_posts;

		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['paged'] );

			$count_query = new \WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		remove_action( 'pre_get_posts', $channel_filter, 9 );

		if ( $posts_query->query_vars['posts_per_page'] === - 1 ) {
			$max_pages = 1;
		} else {
			$max_pages = ceil( $total_posts / (int) $posts_query->query_vars['posts_per_page'] );
		}

		if ( $page > $max_pages && $total_posts > 0 ) {
			return new \WP_Error( 'rest_post_invalid_page_number', __( 'The page number requested is larger than the number of pages available.', 'bigcommerce' ), [ 'status' => 400 ] );
		}

		$response = rest_ensure_response( array_values( $posts ) );

		$response->header( 'X-WP-Total', (int) $total_posts );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$base           = add_query_arg( $request_params, rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Get a callback to run on pre_get_posts for a query
	 * to set an appropriate channel filter for the given channel
	 *
	 * @param int|int[] $channel
	 *
	 * @return \Closure
	 */
	private function get_channel_filter( $channel ) {
		$no_op = function () {
			// do nothing
		};
		if ( empty( $channel ) ) {
			return $no_op;
		}

		try {
			$connections = new Connections();
			$primary     = $connections->primary();
			$active      = $connections->active();
		} catch ( Channel_Not_Found_Exception $e ) {
			return $no_op;
		}

		$active_channel_ids = wp_list_pluck( $active, 'term_id' );
		if ( $channel === - 1 ) {
			$valid_channel_ids = $active_channel_ids;
		} else {
			$valid_channel_ids = array_intersect( (array) $channel, $active_channel_ids );
		}
		if ( empty( $valid_channel_ids ) || $valid_channel_ids === [ $primary->term_id ] ) {
			return $no_op;
		}

		/**
		 * Create a filter for the query to set the channel ID(s)
		 *
		 * @param \WP_Query $query
		 *
		 * @return void
		 * @see \BigCommerce\Taxonomies\Channel\Query_Filter::set_tax_query()
		 */
		$filter = function ( \WP_Query $query ) use ( $valid_channel_ids ) {
			$filter_query = [
				'relation' => 'AND',
				[
					'taxonomy' => Channel::NAME,
					'terms'    => $valid_channel_ids,
					'field'    => 'term_id',
					'operator' => 'IN',
				],
			];

			if ( ! isset( $query->tax_query ) ) {
				$query->tax_query = new \WP_Tax_Query( $filter_query );
			}

			$existing_queries          = $query->tax_query->queries;
			$query->tax_query->queries = $filter_query;
			if ( ! empty( $existing_queries ) ) {
				$query->tax_query->queries[] = $existing_queries;
			}

			$query->query_vars['tax_query'] = $query->tax_query->queries;
		};

		return $filter;
	}

	public function get_collection_params() {
		$query_params = parent::get_collection_params();
		foreach ( Shortcodes\Products::default_attributes() as $key => $default ) {
			$params[ $key ] = [
				'type'    => is_int( $default ) ? 'integer' : 'string',
				'default' => $default,
			];
		}

		$query_params['order'] = [
			'description' => __( 'Direction to sort results', 'bigcommerce' ),
			'type'        => 'string',
			'default'     => 'asc',
			'enum'        => [ 'asc', 'desc' ],
		];

		$query_params['bcid'] = [
			'description' => __( 'BigCommerce product IDs', 'bigcommerce' ),
			'type'        => 'array',
			'items'       => [
				'type' => 'integer',
			],
			'default'     => [],
		];

		$query_params['recent'] = [
			'description' => __( 'Limits results to products updated in the last 2 days', 'bigcommerce' ),
			'type'        => 'boolean',
			'default'     => false,
		];

		$query_params[ Channel::NAME ] = [
			'description' => __( 'Limits results to products from the given channel', 'bigcommerce' ),
			'type'        => 'integer',
			'default'     => 0,
		];

		foreach ( $this->taxonomy_params() as $taxonomy ) {
			$query_params[ $taxonomy ] = [
				/* translators: %s: taxonomy name */
				'description' => sprintf( __( 'Limit result set to all items that have the specified term assigned in the %s taxonomy.', 'bigcommerce' ), $taxonomy ),
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
				'default'     => [],
			];
		}

		return $query_params;
	}

	/**
	 * @return array The taxonomies that can be used in requests and responses
	 */
	private function taxonomy_params() {
		return [
			Brand::NAME,
			Flag::NAME,
			Product_Category::NAME,
		];
	}


	/**
	 * Prepares a single product output for response.
	 *
	 * @param \WP_Post         $post    Post object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$backup_post     = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$product         = new Product( $post->ID );
		$GLOBALS['post'] = $post;

		setup_postdata( $post );

		$schema = $this->get_item_schema();

		// Base fields for every post.
		$data = [];

		foreach ( $schema['properties'] as $key => $meta ) {
			if ( empty( $meta ) ) {
				continue;
			}
			$data[ $key ] = $this->get_item_property( $product, $key, $meta );
		}
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$GLOBALS['post'] = $backup_post;
		wp_reset_postdata();

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		/**
		 * Filters the product data for a response.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param \WP_Post          $post     Post object.
		 * @param \WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'bigcommerce/rest/products/prepare_item_for_response', $response, $post, $request );
	}

	private function get_item_property( Product $product, $key, $schema ) {
		switch ( $key ) {
			case 'post_id':
				return (int) $product->post_id();
			case 'bigcommerce_id':
				return (int) $product->bc_id();
			case 'date':
				return mysql_to_rfc3339( get_post_field( 'post_date', $product->post_id() ) );
			case 'date_gmt':
				return mysql_to_rfc3339( get_post_field( 'post_date_gmt', $product->post_id() ) );
			case 'title':
				return get_the_title( $product->post_id() );
			case 'content':
				return $this->get_content_object( $product->post_id() );
			case 'image':
				$attachment_id = get_post_thumbnail_id( $product->post_id() );

				return [
					'id'    => $attachment_id,
					'sizes' => $this->get_image_urls( $attachment_id, $schema ),
				];
			case 'sku':
				return $product->sku();
			case 'price_range':
				return $product->calculated_price_range();
			default:
				if ( in_array( $key, $this->taxonomy_params() ) ) {
					return $this->get_taxonomy_properties( $product->post_id(), $key );
				}

				return '';
		}
	}

	/**
	 * @param int $post_id
	 *
	 * @return array The structured content array
	 */
	protected function get_content_object( $post_id ) {
		$content   = get_post_field( 'post_content', $post_id );
		$formatted = apply_filters( 'the_content', $content );
		/**
		 * Filters rest product content trim words length.
		 *
		 * @param int $trimmed_length Length.
		 */
		$trimmed   = wp_trim_words( $content, apply_filters( 'bigcommerce/rest/product/content_trim_words_length', 15 ) );

		return [
			'raw'       => $content,
			'formatted' => $formatted,
			'trimmed'   => $trimmed,
		];
	}

	/**
	 * Get the URLs for the attachment in all requested sizes
	 *
	 * @param int   $attachment_id
	 * @param array $schema
	 *
	 * @return array
	 */
	protected function get_image_urls( $attachment_id, $schema ) {
		$sizes = [];
		if ( empty( $schema[ 'properties' ][ 'sizes' ][ 'properties' ] ) ) {
			return [];
		}
		foreach ( array_keys( $schema[ 'properties' ][ 'sizes' ][ 'properties' ] ) as $size ) {
			if ( ! $attachment_id ) {
				$sizes[ $size ] = $this->missing_image( $size );
				continue;
			}
			$image = wp_get_attachment_image_src( $attachment_id, $size );
			if ( empty( $image ) ) {
				$sizes[ $size ] = $this->missing_image( $size );
				continue;
			}
			$sizes[ $size ] = [
				'url'    => $image[ 0 ],
				'width'  => $image[ 1 ],
				'height' => $image[ 2 ],
			];
		}

		return $sizes;
	}

	protected function missing_image( $size ) {
		/**
		 * Filters rest missing image data.
		 *
		 * @param array $missing_image_data Data.
		 */
		return apply_filters( 'bigcommerce/rest/missing_image', [
			'url'    => '',
			'width'  => '',
			'height' => '',
		], $size );
	}

	/**
	 * Get the terms for the product
	 *
	 * @param int    $post_id
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	protected function get_taxonomy_properties( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [];
		}

		return array_map( function ( \WP_Term $term ) {
			return [
				'id'    => $term->term_id,
				'label' => $term->name,
				'slug'  => $term->slug,
			];
		}, $terms );
	}


	/**
	 * Retrieves the response's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'bigcommerce_product_view',
			'type'       => 'object',
			// Base properties for every Post.
			'properties' => [
				'post_id'        => [
					'description' => __( 'WordPress identifier for the object.', 'bigcommerce' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'bigcommerce_id' => [
					'description' => __( 'BigCommerce identifier for the object.', 'bigcommerce' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'date'           => [
					'description' => __( "The date the object was published, in the site's timezone.", 'bigcommerce' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => [ 'view', 'edit', 'embed' ],
				],
				'date_gmt'       => [
					'description' => __( 'The date the object was published, as GMT.', 'bigcommerce' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => [ 'view', 'edit' ],
				],
				/*'link'            => array(
					'description' => __( 'URL to the object.', 'bigcommerce' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),*/
				'title'          => [
					'description' => __( 'The title for the object.', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'arg_options' => [
						'sanitize_callback' => null, // Note: sanitization implemented in self::prepare_item_for_database()
						'validate_callback' => null, // Note: validation implemented in self::prepare_item_for_database()
					],
				],
				'content'        => [
					'description' => __( 'The content for the object.', 'bigcommerce' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'properties'  => [
						'raw'       => [
							'description' => __( 'The unaltered post_content', 'bigcommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit', 'embed' ],
						],
						'formatted' => [
							'description' => __( 'The post content with the_content filters applied', 'bigcommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit', 'embed' ],
						],
						'trimmed'   => [
							'description' => __( 'The post content trimmed to 15 words', 'bigcommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit', 'embed' ],
						],
					],
				],
				'image'          => [
					'description' => __( 'The featured image of the object', 'bigcommerce' ),
					'type'        => 'object',
					'context'     => [ 'view', 'edit', 'embed' ],
					'properties'  => $this->get_image_schema(),
				],
				'sku'            => [
					'description' => __( 'The SKU for the product.', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
				],
				'price_range'    => [
					'description' => __( 'The price for the product.', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
				],
			],
		];

		foreach ( $this->taxonomy_params() as $taxonomy ) {
			$schema['properties'][ $taxonomy ] = [
				'description' => sprintf( __( 'A term from the %s taxonomy', 'bigcommerce' ), $taxonomy ),
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

	private function get_image_schema() {
		$sizes = wp_get_additional_image_sizes();
		$sizes = array_filter( array_unique( array_merge( array_keys( $sizes ), [
			'thumbnail',
			'medium',
			'large',
			'full',
		] ) ) );
		/**
		 * Filters rest image sizes.
		 *
		 * @param array $sizes Sizes.
		 */
		$sizes = apply_filters( 'bigcommerce/rest/image_sizes', $sizes );
		$sizes = array_combine( $sizes, array_map( function ( $size ) {
			return [
				'description' => __( 'An image', 'bigcommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit', 'embed' ],
				'properties'  => [
					'url'    => [
						'type'        => 'string',
						'description' => __( 'The image URL', 'bigcommerce' ),
						'context'     => [ 'view', 'edit', 'embed' ],
					],
					'width'  => [
						'type'        => 'integer',
						'description' => __( 'The image width', 'bigcommerce' ),
						'context'     => [ 'view', 'edit', 'embed' ],
					],
					'height' => [
						'type'        => 'integer',
						'description' => __( 'The image height', 'bigcommerce' ),
						'context'     => [ 'view', 'edit', 'embed' ],
					],
				],
			];
		}, $sizes ) );

		return [
			'sizes' => [
				'type'        => 'object',
				'description' => __( 'Image sizes', 'bigcommerce' ),
				'context'     => [ 'view', 'edit', 'embed' ],
				'properties'  => $sizes,
			],
			'id'    => [
				'type'        => 'integer',
				'description' => __( 'The ID of the image', 'bigcommerce' ),
				'context'     => [ 'view', 'edit', 'embed' ],
			],
		];
	}

}
