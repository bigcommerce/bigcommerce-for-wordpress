# BigCommerce API V3 Client Library

## Create a product with variants and a modifier 

```php
use BigCommerce\Api\v3\Model\ModifierPost;
use BigCommerce\Api\v3\Model\ProductPost;

$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$client  = new \BigCommerce\Api\v3\ApiClient( $config );
$catalog = new \BigCommerce\Api\v3\Api\CatalogApi( $client );

try {
	
	/*
	 * List of request parameters and response properties available at
	 * https://developer.bigcommerce.com/api-reference/catalog/catalog-api/products/createproduct
	 */
	$product_request = new ProductPost( [
		'name'   => 'BigCommerce Coffee Mug',
		'price'  => '10.00',
		'weight' => 4,
		'type'   => 'physical',
		'variants' => [
			[
				'sku'           => 'MUG-BLUE',
				'option_values' => [
					'option_display_name' => 'Mug Color',
					'label'               => 'Blue',
				],
			],
			[
				'sku'           => 'MUG-GREY',
				'option_values' => [
					'option_display_name' => 'Mug Color',
					'label'               => 'Grey',
				],
			],
		]
	] );

	$product_response = $catalog->createProduct( $product_request );
	
	$product_id = $product_response->getData()->getId();
	
	/*
	 * List of request parameters and response properties available at
	 * https://developer.bigcommerce.com/api-reference/catalog/catalog-api/product-modifiers/createmodifier
	 */
	$modifier_request = new ModifierPost( [
		'type'         => 'text',
		'required'     => false,
		'display_name' => 'Custom Message',
	] );
	
	$catalog->createModifier( $product_id, $modifier_request );

} catch ( \BigCommerce\Api\v3\ApiException $e ) {
	$error_message = $e->getMessage();
	$error_body    = $e->getResponseBody();
	$error_headers = $e->getResponseHeaders();
	// do something with the error
	return;
}
```