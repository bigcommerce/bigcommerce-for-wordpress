# BigCommerce API V3 Client Library

## Add a route to the site

```php
$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$client = new \BigCommerce\Api\v3\ApiClient( $config );
$sites  = new \BigCommerce\Api\v3\Api\SitesApi( $client );

$channel_id = 7;

try {
	$site_id = $sites->getChannelSite( $channel_id )->getData()->getId();
	$route = new \BigCommerce\Api\v3\Model\Route( [
		'type'     => 'login',
		'matching' => '',
		'route'    => 'https://www.example.com/login/',
	] );
	$sites->postSiteRoute( $site_id, $route );
} catch ( \BigCommerce\Api\v3\ApiException $e ) {
	$error_message = $e->getMessage();
	$error_body    = $e->getResponseBody();
	$error_headers = $e->getResponseHeaders(); // do something with the error
	return;
}
```