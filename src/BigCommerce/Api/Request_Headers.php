<?php


namespace BigCommerce\Api;


use BigCommerce\Plugin;

/**
 * Provides functionality for adding custom headers to API requests, including plugin-specific
 * information like WordPress version, plugin version, and PHP version.
 *
 * @package BigCommerce\Api
 */
class Request_Headers {

	/**
	 * Add plugin-related information to the request headers.
	 *
	 * This method adds additional headers containing details about the client type, client version,
	 * plugin version, and PHP version to the provided headers array.
	 *
	 * @param array $headers The existing array of request headers to which plugin info will be added.
	 *
	 * @return array The updated array of headers with added plugin information.
	 *
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