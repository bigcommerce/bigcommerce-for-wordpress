<?php


namespace BigCommerce\Rest;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Templates\Review_List;
use BigCommerce\Templates\Review_Single;

class Reviews_Listing_Controller extends Rest_Controller {

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<post_id>[0-9]+)/html', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_rendered_item' ],
				'permission_callback' => [ $this, 'get_rendered_item_permissions_check' ],
				'args'                => $this->get_rendered_item_params(),
			],
			'schema' => [ $this, 'get_rendered_item_schema' ],
		] );
	}


	public function get_rendered_item_params() {
		$params               = [];
		$params[ 'paged' ]    = [
			'type'    => 'integer',
			'default' => 1,
		];
		$params[ 'per_page' ] = [
			'type'    => 'integer',
			'default' => 0,
		];
		$params[ 'ajax' ]     = [
			'type'    => 'integer',
			'default' => 0,
		];

		return $params;
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

	public function get_rendered_item_schema() {
		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'bigcommerce_review_list_rendered',
			'type'       => 'object',
			'properties' => [
				'rendered' => [
					'description' => __( 'The rendered review list string', 'bigcommerce' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
			],
		];

		return $schema;
	}

	/**
	 * Retrieves the rendered review list
	 *
	 * @since 4.7.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_rendered_item( $request ) {
		$attributes = $request->get_params();
		$product    = new Product( $attributes[ 'post_id' ] );

		$page = absint( $attributes[ 'paged' ] ) ?: 1;
		$per_page = absint( $attributes[ 'per_page' ] ) ?: 12;

		$reviews = $product->get_reviews( [
			'page'    => $page,
			'per_page' => $per_page,
		] );

		$total_reviews = $product->get_review_count();
		$total_pages   = empty( $total_reviews ) ? 0 : ceil( $total_reviews / $per_page );

		$reviews = array_map( function ( $review ) use ( $product ) {
			$controller = Review_Single::factory( array_merge( [
				Review_Single::PRODUCT => $product,
			], $review ) );

			return $controller->render();
		}, $reviews );

		$controller = Review_List::factory( [
			Review_List::PRODUCT => $product,
			Review_List::REVIEWS => $reviews,
			Review_List::WRAP    => false,
			Review_List::NEXT_PAGE_URL => $this->next_page_url( $attributes[ 'post_id' ], $per_page, $page, $total_pages ),
		] );

		$output = $controller->render();

		$response = rest_ensure_response( [
			'rendered' => $output,
		] );

		return $response;
	}

	private function next_page_url( $post_id, $per_page, $current_page, $max_pages ) {
		if ( $current_page >= $max_pages ) {
			return '';
		}

		$base_url = $this->product_reviews_url( $post_id );

		$attr = [
			'per_page' => $per_page,
			'paged' => $current_page + 1,
			'ajax'  => 1,
		];

		$url = add_query_arg( $attr, $base_url );
		$url = wp_nonce_url( $url, 'wp_rest' );

		return $url;
	}

	public function product_reviews_url( $post_id ) {
		return sprintf( '%s/%d/html', $this->get_base_url(), $post_id );
	}
}