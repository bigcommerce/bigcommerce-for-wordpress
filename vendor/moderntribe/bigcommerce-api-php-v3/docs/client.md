# BigCommerce API V3 Client Library

## Instantiating the API Client

The first step to instantiating the API client is to create a configuration
object:

```php
$config = new \BigCommerce\Api\v3\Configuration();
$config->setHost( $api_url );
$config->setClientId( $client_id );
$config->setAccessToken( $access_token );
$config->setClientSecret( $client_secret ); // not required for most requests
```

You can find the required values in the BigCommerce admin when you create
a new access token, or create an application that [uses OAuth to request
an access token](/docs/examples/oauth-token.md).

With the configuration in hand, create an instance of the ApiClient object.

```php
$client = new \BigCommerce\Api\v3\ApiClient( $config );
```

There are then a number of APIs that you can instantiate using the client object:

```php
$catalog  = new \BigCommerce\Api\v3\Api\CatalogApi( $client );
$cart     = new \BigCommerce\Api\v3\Api\CartApi( $client );
$channels = new \BigCommerce\Api\v3\Api\ChannelsApi( $client );
$sites    = new \BigCommerce\Api\v3\Api\SitesApi( $client );
```

## Usage Examples

* [Request products from the catalog](/docs/examples/catalog-get-products.md)
* [Add a route to a site](/docs/examples/add-site-route.md)
* [Create a product with variants and a modifier](/docs/examples/create-product.md)
* [Update variants for a product](/docs/examples/udate-variants.md)
* [Add a product to a cart](/docs/examples/add-to-cart.md)
* [Create a widget](/docs/examples/create-widget.md)