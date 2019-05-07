# BigCommerce API V3 Client Library

## Request products from the catalog

```php
$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$client = new \BigCommerce\Api\v3\ApiClient( $config );
$catalog  = new \BigCommerce\Api\v3\Api\CatalogApi( $client );

$product_ids = [ 100, 101, 102 ];

try {
	/*
	 * List of request parameters and response properties available at
	 * https://developer.bigcommerce.com/api-reference/catalog/catalog-api/products/getproducts
	 */
	$products_response = $catalog->getProducts( [
		'id:in'   => $product_ids,
		'include' => [ 'variants', 'custom_fields', 'images', 'bulk_pricing_rules', 'options', 'modifiers' ],
	] );
} catch ( \BigCommerce\Api\v3\ApiException $e ) {
	$error_message = $e->getMessage();
	$error_body    = $e->getResponseBody();
	$error_headers = $e->getResponseHeaders();
	// do something with the error
	return;
}

$product_ids = array_map( function( \BigCommerce\Api\v3\Model\Product $product ) {
	return $product->getId();
}, $products_response->getData() );
```