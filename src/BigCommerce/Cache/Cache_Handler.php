<?php

namespace BigCommerce\Cache;

/**
 * Cache_Handler class
 *
 * Flushes catalog api WP_Object_Cache data
 */
class Cache_Handler
{
	private $catalog_path = '/catalog/products/';

	private $header_params = [
		'Accept'       => 'application/json',
		'Content-Type' => 'application/json',
	];

	private $product_response_type = '\BigCommerce\Api\v3\Model\ProductResponse';
	private $group_key             = 'bigcommerce_api';

	/**
	 * Flush WP_Object_Cache for catalog API in order to get up-to-date product info from BC catalog
	 *
	 * @param $product_id
	 * @param array $query_params
	 */
	public function flush_product_catalog_object_cache( $product_id, array $query_params = [] ): void {
		$default_params = [
			'include' => 'variants,custom_fields,images,videos,bulk_pricing_rules,options,modifiers',
		];

		$params = array_merge( $default_params, $query_params );

		$key = $this->build_serialized_key( $product_id, $params );
		wp_cache_delete( $key, $this->group_key );
	}

	/**
	 * Build a cache key
	 *
	 * @param $product_id
	 * @param array $query_params
	 *
	 * @return string
	 */
	private function build_serialized_key( $product_id, array $query_params = [] ): string {
		$args = [
			'method'       => 'GET',
			'queryParams'  => $query_params,
			'postData'     => '',
			'headerParams' => $this->header_params,
			'responseType' => $this->product_response_type,
		];

		$serialized = md5( wp_json_encode( $args ) );

		$generation_key = (wp_cache_get( 'generation_key', 'bigcommerce_api' ) ?? md5( microtime( true ) ));
		$path           = $this->catalog_path . $product_id;

		return $path . ':' . $serialized . ':' . $generation_key;
	}

}
