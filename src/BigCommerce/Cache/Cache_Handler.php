<?php

namespace BigCommerce\Cache;

/**
 * Cache_Handler class
 *
 * Responsible for flushing the catalog API WP_Object_Cache to ensure that product information is up-to-date
 * from the BigCommerce catalog.
 *
 * @package BigCommerce\Cache
 */
class Cache_Handler
{
	/**
	 * The catalog API path for products.
	 *
	 * @var string
	 */
	private $catalog_path = '/catalog/products/';

	/**
	 * The default HTTP header parameters used for API requests.
	 *
	 * @var array
	 */
	private $header_params = [
		'Accept'       => 'application/json',
		'Content-Type' => 'application/json',
	];

	/**
	 * The type of response expected from the API for product requests.
	 *
	 * @var string
	 */
	private $product_response_type = '\BigCommerce\Api\v3\Model\ProductResponse';

	/**
	 * The group key used in WordPress object caching for BigCommerce API data.
	 *
	 * @var string
	 */
	private $group_key             = 'bigcommerce_api';

	/**
	 * Flushes the WordPress object cache for the product catalog.
	 *
	 * This method ensures that the cache is cleared for the specified product, allowing fresh product information
	 * to be retrieved from the BigCommerce catalog.
	 *
	 * @param int   $product_id   The ID of the product to flush the cache for.
	 * @param array $query_params Optional query parameters to customize the cache key.
	 *
	 * @return void
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
	 * Builds a serialized cache key for a product based on product ID and query parameters.
	 *
	 * This method generates a unique cache key used to store and retrieve the product's data from the cache.
	 *
	 * @param int   $product_id   The ID of the product.
	 * @param array $query_params Optional query parameters to customize the cache key.
	 *
	 * @return string The generated cache key.
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
