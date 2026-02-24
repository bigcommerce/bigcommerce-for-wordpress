<?php


namespace BigCommerce\Api;

/**
 * Handles retrieval of currencies from the BigCommerce v2 API.
 * Provides a method to fetch and process a collection of currencies,
 * returning their properties as an associative array.
 *
 * @package BigCommerce\Api
 * @extends v2ApiAdapter
 */
class Currencies_Api extends v2ApiAdapter {

	/**
	 * Retrieve the list of currencies from the BigCommerce API.
	 *
	 * Fetches currencies from the `/currencies` endpoint and processes
	 * them into an array of associative arrays, each representing a currency
	 * and its updateable fields.
	 *
	 * @return array An array of currencies, where each currency is represented
	 *               as an associative array of its properties.
	 *
	 * @throws \Exception Handles exceptions gracefully, returning an empty array
	 *                    if the API call fails.
	 */
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
