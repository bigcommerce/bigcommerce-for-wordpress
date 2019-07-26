# BigCommerce API V3 Client Library

## Update variants for a product 

```php
use \BigCommerce\Api\v3\Model\Variant;
use BigCommerce\Api\v3\Model\VariantPut;
use BigCommerce\Api\v3\Model\VariantResponse;

$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$client  = new \BigCommerce\Api\v3\ApiClient( $config );
$catalog = new \BigCommerce\Api\v3\Api\CatalogApi( $client );

$product_id = 101;

try {
	$variants_responses = [];
	$page = 0;
	$per_page = 100;
	
	// Request all variants. There may be more than one page of results.
	do {
		$page ++;
		/*
		 * List of request parameters and response properties available at
		 * https://developer.bigcommerce.com/api-reference/catalog/catalog-api/product-variants/getvariantsbyproductid
		 */
		$variants_response = $catalog->getVariantsByProductId( $product_id, [
			'page'  => $page,
			'limit' => $per_page,
		] );
		$max_pages = $variants_response->getMeta()->getPagination()->getTotalPages();
		$variants_responses[] = $variants_response;
	} while ( $page < $max_pages );

	// Merge all the responses into one array
	$variants = array_merge( ...array_map( function( VariantResponse $response ) {
		return $response->getData();
	}, $variants_responses ) );
	
	// Increase inventory of each variant by 5
	array_map( function( Variant $variant ) use ( $catalog ) {
		$inventory_level = $variant->getInventoryLevel() + 5;
		
		$request = new VariantPut( $variant->get() );
		$request->setInventoryLevel( $inventory_level );
		
		$catalog->updateVariant( $variant->getProductId(), $variant->getId(), $request );
	}, $variants );

} catch ( \BigCommerce\Api\v3\ApiException $e ) {
	$error_message = $e->getMessage();
	$error_body    = $e->getResponseBody();
	$error_headers = $e->getResponseHeaders();
	// do something with the error
	return;
}
```