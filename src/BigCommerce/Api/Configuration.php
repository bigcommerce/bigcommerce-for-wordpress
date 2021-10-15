<?php


namespace BigCommerce\Api;


class Configuration extends \BigCommerce\Api\v3\Configuration {

	/**
	 * Gets the default header
	 *
	 * @return array An array of default header(s)
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
