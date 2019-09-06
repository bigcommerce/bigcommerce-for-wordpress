<?php


namespace BigCommerce\Api;

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
