<?php

namespace BigCommerce\Rest;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Container\Api;
use BigCommerce\Container\GraphQL;
use BigCommerce\Logging\Error_Log;
use Pimple\Container;

class Terms_Controller extends Rest_Controller {

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * Checks if a given request has access to read terms.
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
	 * Retrieves a collection of terms.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$container    = bigcommerce()->container();
		$request_data = $request->get_params();
		if ( ! empty( $request_data['slug'] ) ) {
			return $this->get_items_graphql( $container, $request_data );
		}

		return $this->get_items_rest( $container, $request_data );
	}

	/**
	 * @param $request_data
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	private function get_items_rest(Container $container, $request_data ) {
		$client  = $container[ Api::CLIENT ];
		$catalog = new CatalogApi( $client );

		try {
			$params = [
					'page'  => $request_data['page'],
					'limit' => $request_data['per_page'],
			];

			if ( ! empty( $request_data['id'] ) ) {
				$params['id'] = $request_data['id'];
			}

			if ( $request_data['term'] === 'category' ) {
				$response = $catalog->getCategories( $params );
			} else {
				$response = $catalog->getBrands( $params );
			}

			return $this->parse_result( $response, $client );
		} catch ( ApiException $e ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [ 'request' => $request_data ], 'rest' );

			return new \WP_Error( 'api_error', sprintf(
				__( 'There was an error retrieving terms data. Error message: "%s"', 'bigcommerce' ),
				$e->getMessage()
			), [ 'exception' => [ 'message' => $e->getMessage(), 'code' => $e->getCode() ] ] );
		}
	}

	private function get_items_graphql(Container $container, $request_data ) {
		try {
			return rest_ensure_response( $container[ GraphQL::GRAPHQL_REQUESTOR ]->request_terms( $request_data['slug'], $request_data['term'] ) );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [ 'request' => $request_data ], 'rest' );

			return new \WP_Error( 'api_error', sprintf(
				__( 'There was an error retrieving terms via GQL. Error message: "%s"', 'bigcommerce' ),
				$e->getMessage()
			), [ 'exception' => [ 'message' => $e->getMessage(), 'code' => $e->getCode() ] ] );
		}
	}

	/**
	 * Register endpoint params
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$query_params         = parent::get_collection_params();
		$query_params['term'] = [
			'description' => __( 'Term to retrieve(brand or category)', 'bigcommerce' ),
			'type'        => 'string',
			'default'     => 'category',
		];

		$query_params['id'] = [
			'description' => __( 'ID of the term to retrieve(brand or category)', 'bigcommerce' ),
			'type'        => 'integer',
			'default'     => 0,
		];

		$query_params['slug'] = [
			'description' => __( 'Slug of the term to retrieve(brand or category)', 'bigcommerce' ),
			'type'        => 'string',
			'default'     => '',
		];

		return $query_params;
	}
}
