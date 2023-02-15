<?php

namespace BigCommerce\Rest;

use BigCommerce\Import\Processors\Storefront_Processor;
use BigCommerce\Taxonomies\Channel\Connections;

class Storefront_Controller extends Rest_Controller {

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
		] );
	}

	/**
	 * Add data to the JS config to support storefront requests
	 *
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 */
	public function js_config( $config ) {
		$config['storefront'] = [
			'api_url'               => $this->get_base_url(),
			'ajax_storefront_nonce' => wp_create_nonce( 'wp_rest' ),
		];

		return $config;
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
		try {
			$connections = new Connections();
			$current     = $connections->current();

			return rest_ensure_response( [
				'email'   => get_term_meta( $current->term_id, Storefront_Processor::STOREFRONT_EMAIl, true ) ?: '',
				'address' => get_term_meta( $current->term_id, Storefront_Processor::STOREFRONT_ADDRESS, true ) ?: '',
				'title'   => get_term_meta( $current->term_id, Storefront_Processor::STOREFRONT_NAME, true ) ?: '',
				'phone'   => get_term_meta( $current->term_id, Storefront_Processor::STOREFRONT_PHONE, true ) ?: '',
			] );
		} catch ( \Exception $exception ) {
			return new \WP_Error( 'gateway_error', $exception->getMessage(), [ 'exception' => $exception ] );
		}
	}

}
