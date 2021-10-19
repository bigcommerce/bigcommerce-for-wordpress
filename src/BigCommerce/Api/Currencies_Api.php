<?php


namespace BigCommerce\Api;

/**
 * Class Currencies_Api
 *
 * Get currencies from v2 api collection
 *
 * @package BigCommerce\Api
 */
class Currencies_Api extends v2ApiAdapter {
	public function get_currencies() {
		try {
			$currencies = array_map( function ( Resource $resource ) {
				return get_object_vars( $resource->getUpdateFields() );
			}, $this->getCollection( '/currencies' ) );
		} catch ( \Exception $e ) {
			$currencies = [];
		}

		return $currencies ?: [];
	}
}
