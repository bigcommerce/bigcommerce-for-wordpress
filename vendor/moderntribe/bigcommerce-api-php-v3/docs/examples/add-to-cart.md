# BigCommerce API V3 Client Library

## Add a product to a cart 

```php
use BigCommerce\Api\v3\Model\CartRequestData;
use BigCommerce\Api\v3\Model\Currency;
use BigCommerce\Api\v3\Model\LineItemRequestData;
use BigCommerce\Api\v3\Model\ProductOptionSelection;

$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$client = new \BigCommerce\Api\v3\ApiClient( $config );
$cart   = new \BigCommerce\Api\v3\Api\CartApi( $client );

try {
	$request_data      = new CartRequestData();
	$option_selections = [];

	// Both Options and Modifiers are submitted the same way
	foreach ( $options as $option_key => $option_value ) {
		$option_selections[] = new ProductOptionSelection( [
			'option_id'    => $option_key,
			'option_value' => $option_value,
		] );
	}
	

	$request_data->setLineItems( [
		new LineItemRequestData( [
			'quantity'          => $quantity,
			'product_id'        => $product_id,
			'option_selections' => $option_selections,
		] ),
	] );

	// gift certificates must be present, even if an empty array
	$request_data->setGiftCertificates( [] );
	
	if ( $cart_id ) {
		// add the items to an existing cart
		$response = $cart->cartsCartIdItemsPost( $cart_id, $request_data );
	} else {
		// create a new cart with the items
		
		// Set optional parameters that would already be in place on an existing cart
		
		if ( $customer_id ) { // optional parameter, if we have a customer ID
			$request_data->setCustomerId( $customer_id );
		}
		if ( $channel_id ) { // optional parameter, if we have a channel ID
			$request_data->setChannelId( $channel_id );
		}
		if ( $currency ) { // optional parameter, if using a non-default currency
			$request_data->setCurrency( new Currency( [ 'code' => $currency ] ) );
		}

		$response = $cart->cartsPost( $request_data );
		$cart_id = $response->getData()->getId();
	}

} catch ( \BigCommerce\Api\v3\ApiException $e ) {
	$error_message = $e->getMessage();
	$error_body    = $e->getResponseBody();
	$error_headers = $e->getResponseHeaders();
	// do something with the error
	return;
}
```