<?php


namespace BigCommerce\Api;


class Configuration extends \BigCommerce\Api\v3\Configuration {

	/**
	 * Gets the default header
	 *
	 * @return array An array of default header(s)
	 */
	public function getDefaultHeaders() {
		return apply_filters( 'bigcommerce/api/default_headers', parent::getDefaultHeaders() );
	}

}