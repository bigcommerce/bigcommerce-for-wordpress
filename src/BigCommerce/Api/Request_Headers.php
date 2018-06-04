<?php


namespace BigCommerce\Api;


use BigCommerce\Plugin;

class Request_Headers {

	/**
	 * @param array $headers
	 *
	 * @return array
	 * @filter bigcommerce/api/default_headers
	 */
	public function add_plugin_info_headers( $headers ) {
		$headers[ 'X-Client-Type' ]    = 'WordPress';
		$headers[ 'X-Client-Version' ] = $GLOBALS[ 'wp_version' ];
		$headers[ 'X-Plugin-Version' ] = Plugin::VERSION;
		$headers[ 'X-Php-Version' ]    = PHP_VERSION;

		return $headers;
	}
}