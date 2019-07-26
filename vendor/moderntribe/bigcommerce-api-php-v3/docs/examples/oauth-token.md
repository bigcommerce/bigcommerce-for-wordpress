# BigCommerce API V3 Client Library

## Requesting an OAuth Token

If you create an application for the [BigCommerce Marketplace](https://www.bigcommerce.com/apps/),
you will need to build an application for requesting new OAuth tokens to
enable connections to the BigCommerce API.

When you create your app in the [Developer Portal](https://devtools.bigcommerce.com/my/apps),
it will give you a Client ID and Client Secret. Take note of these; you'll
need them later.

When you create your app, it will ask you for three callback URLs. You'll want
to create a handler for all three of those, but of concern here is the
"Auth Callback URL". When a merchant installs your application through
the Marketplace, this URL will load in a frame for that user in the BigCommerce
administration interface.

When your application receives a GET request at the auth callback URL,
the request will include the query parameters `code`, `scope`, and `context`.
Using these parameters, along with your app's Client ID and Client Secret,
send a `POST` request back to BigCommerce, at https://login.bigcommerce.com/oauth2/token.

```php
$request_token_url = 'https://login.bigcommerce.com/oauth2/token';
$request_body = [
	'client_id'     => $client_id,
	'client_secret' => $client_secret,
	'redirect_uri'  => $original_auth_callback_url,
	'grant_type'    => 'authorization_code',
	'code'          => filter_input( INPUT_GET, 'code' ),
	'scope'         => filter_input( INPUT_GET, 'scope' ),
	'context'       => filter_input( INPUT_GET, 'context' ),
];

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $request_token_url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_POST, 1 );
curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $request_body ) ); 
curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded' ] ); 

$response = json_decode( curl_exec($ch), true );

curl_close( $ch );

$access_token = $response['access_token'];
$context      = explode( '/', $response['context'] );
$store_hash   = $context[1];
$api_url      = sprintf( 'https://api.bigcommerce.com/stores/%s/v3/', $store_hash );
```

Store your Access Token in a safe place, and use it along with the Client ID
when instantiating the API client.

```php
$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$client  = new \BigCommerce\Api\v3\ApiClient( $config );
```

## Further Reading

For more information about building apps and requesting OAuth tokens, see:

https://developer.bigcommerce.com/api-docs/getting-started/building-apps-bigcommerce/building-apps

https://developer.bigcommerce.com/api-docs/getting-started/authentication#authentication_client-id-secret

