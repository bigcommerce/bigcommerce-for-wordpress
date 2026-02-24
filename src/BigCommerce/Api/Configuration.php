<?php


namespace BigCommerce\Api;

/**
 * Extends the base Configuration class to provide additional
 * functionality, such as filtering default headers for BigCommerce API requests.
 *
 * This class allows developers to modify API behavior through WordPress filters.
 *
 * @package BigCommerce\Api
 */
class Configuration extends \BigCommerce\Api\v3\Configuration {

	/**
	 * Retrieves the default headers for API requests.
	 *
	 * Allows modification of the headers via the `bigcommerce/api/default_headers` WordPress filter.
	 *
	 * @return array An array of default headers to be included with API requests.
	 */
	public function getDefaultHeaders() {
		/**
		 * Filters API default headers.
		 *
		 * @param array $default_headers An array of default header(s).
		 */
		return apply_filters( 'bigcommerce/api/default_headers', parent::getDefaultHeaders() );
	}

}
