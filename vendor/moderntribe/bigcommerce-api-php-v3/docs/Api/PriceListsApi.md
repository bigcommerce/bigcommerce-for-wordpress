# BigCommerce\Api\v3\PriceListsApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createPriceList**](PriceListsApi.md#createPriceList) | **POST** /pricelists | 
[**deletePriceList**](PriceListsApi.md#deletePriceList) | **DELETE** /pricelists/{price_list_id} | 
[**deletePriceListRecord**](PriceListsApi.md#deletePriceListRecord) | **DELETE** /pricelists/{price_list_id}/records/{variant_id}/{currency_code} | 
[**deletePriceListRecordsByFilter**](PriceListsApi.md#deletePriceListRecordsByFilter) | **DELETE** /pricelists/{price_list_id}/records | 
[**deletePriceListRecordsByVariantId**](PriceListsApi.md#deletePriceListRecordsByVariantId) | **DELETE** /pricelists/{price_list_id}/records/{variant_id} | 
[**deletePriceListsByFilter**](PriceListsApi.md#deletePriceListsByFilter) | **DELETE** /pricelists | 
[**getPriceList**](PriceListsApi.md#getPriceList) | **GET** /pricelists/{price_list_id} | 
[**getPriceListCollection**](PriceListsApi.md#getPriceListCollection) | **GET** /pricelists | 
[**getPriceListRecord**](PriceListsApi.md#getPriceListRecord) | **GET** /pricelists/{price_list_id}/records/{variant_id}/{currency_code} | 
[**getPriceListRecordCollection**](PriceListsApi.md#getPriceListRecordCollection) | **GET** /pricelists/{price_list_id}/records | 
[**getPriceListRecordsByVariantId**](PriceListsApi.md#getPriceListRecordsByVariantId) | **GET** /pricelists/{price_list_id}/records/{variant_id} | 
[**setPriceListRecord**](PriceListsApi.md#setPriceListRecord) | **PUT** /pricelists/{price_list_id}/records/{variant_id}/{currency_code} | 
[**setPriceListRecordCollection**](PriceListsApi.md#setPriceListRecordCollection) | **PUT** /pricelists/{price_list_id}/records | 
[**updatePriceList**](PriceListsApi.md#updatePriceList) | **PUT** /pricelists/{price_list_id} | 


# **createPriceList**
> \BigCommerce\Api\v3\Model\PriceListResponse createPriceList($price_list)



Creates a `Price List` in BigCommerce.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list = new \BigCommerce\Api\v3\Model\PriceListPost(); // \BigCommerce\Api\v3\Model\PriceListPost | A BigCommerce `PriceList` object.

try {
    $result = $api_instance->createPriceList($price_list);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->createPriceList: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list** | [**\BigCommerce\Api\v3\Model\PriceListPost**](../Model/\BigCommerce\Api\v3\Model\PriceListPost.md)| A BigCommerce &#x60;PriceList&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\PriceListResponse**](../Model/PriceListResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deletePriceList**
> deletePriceList($price_list_id)



Deletes one `Price List` object from BigCommerce by its ID. Also removes all associated Price Records.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.

try {
    $api_instance->deletePriceList($price_list_id);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->deletePriceList: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deletePriceListRecord**
> deletePriceListRecord($price_list_id, $variant_id, $currency_code)



Deletes one `Price Record` object from BigCommerce, by `variant_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$currency_code = "currency_code_example"; // string | The currency code associated with the price record being acted upon.

try {
    $api_instance->deletePriceListRecord($price_list_id, $variant_id, $currency_code);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->deletePriceListRecord: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **currency_code** | **string**| The currency code associated with the price record being acted upon. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deletePriceListRecordsByFilter**
> \BigCommerce\Api\v3\Model\NoContent deletePriceListRecordsByFilter($price_list_id, $variant_id)



Deletes one or more `Price Record` objects from BigCommerce using a filter.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | The ID of the `Variant` whose prices were requested.

try {
    $result = $api_instance->deletePriceListRecordsByFilter($price_list_id, $variant_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->deletePriceListRecordsByFilter: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| The ID of the &#x60;Variant&#x60; whose prices were requested. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\NoContent**](../Model/NoContent.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deletePriceListRecordsByVariantId**
> deletePriceListRecordsByVariantId($price_list_id, $variant_id)



Deletes the collection of `Price Record` objects associated with a certain price list and variant ID.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.

try {
    $api_instance->deletePriceListRecordsByVariantId($price_list_id, $variant_id);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->deletePriceListRecordsByVariantId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deletePriceListsByFilter**
> deletePriceListsByFilter($id, $name)



Deletes a set of `Price List` objects from BigCommerce using a filter. Also removes all associated Price Recordss.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$id = 56; // int | Filter items by id.
$name = "name_example"; // string | Filter items by name.

try {
    $api_instance->deletePriceListsByFilter($id, $name);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->deletePriceListsByFilter: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Filter items by id. | [optional]
 **name** | **string**| Filter items by name. | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPriceList**
> \BigCommerce\Api\v3\Model\PriceListResponse getPriceList($price_list_id)



Returns a `Price List` object from BigCommerce.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.

try {
    $result = $api_instance->getPriceList($price_list_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->getPriceList: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |

### Return type

[**\BigCommerce\Api\v3\Model\PriceListResponse**](../Model/PriceListResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPriceListCollection**
> \BigCommerce\Api\v3\Model\PriceListCollectionResponse getPriceListCollection($id, $name, $page, $limit)



Returns a paginated collection of `Price List` objects from BigCommerce.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$id = 56; // int | Filter items by id.
$name = "name_example"; // string | Filter items by name.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->getPriceListCollection($id, $name, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->getPriceListCollection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Filter items by id. | [optional]
 **name** | **string**| Filter items by name. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\PriceListCollectionResponse**](../Model/PriceListCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPriceListRecord**
> \BigCommerce\Api\v3\Model\PriceRecordResponse getPriceListRecord($price_list_id, $variant_id, $currency_code)



Returns a `Price Record` object from BigCommerce.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$currency_code = "currency_code_example"; // string | The currency code associated with the price record being acted upon.

try {
    $result = $api_instance->getPriceListRecord($price_list_id, $variant_id, $currency_code);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->getPriceListRecord: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **currency_code** | **string**| The currency code associated with the price record being acted upon. |

### Return type

[**\BigCommerce\Api\v3\Model\PriceRecordResponse**](../Model/PriceRecordResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPriceListRecordCollection**
> \BigCommerce\Api\v3\Model\PriceRecordCollectionResponse getPriceListRecordCollection($price_list_id, $variant_id, $product_id, $currency, $page, $limit)



Fetches the `Price Records` associated with a particular Price List, using a filter.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | The ID of the `Variant` whose prices were requested.
$product_id = "product_id_example"; // string | A comma-separated list of ids of `Product`s whose prices were requested.
$currency = "currency_example"; // string | Filter items by currency.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->getPriceListRecordCollection($price_list_id, $variant_id, $product_id, $currency, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->getPriceListRecordCollection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| The ID of the &#x60;Variant&#x60; whose prices were requested. | [optional]
 **product_id** | **string**| A comma-separated list of ids of &#x60;Product&#x60;s whose prices were requested. | [optional]
 **currency** | **string**| Filter items by currency. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\PriceRecordCollectionResponse**](../Model/PriceRecordCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPriceListRecordsByVariantId**
> \BigCommerce\Api\v3\Model\PriceRecordCollectionResponse getPriceListRecordsByVariantId($price_list_id, $variant_id)



Fetches an array of `Price Records` matching a particular Price List and Variant ID. Will contain any set price records by currency.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.

try {
    $result = $api_instance->getPriceListRecordsByVariantId($price_list_id, $variant_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->getPriceListRecordsByVariantId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |

### Return type

[**\BigCommerce\Api\v3\Model\PriceRecordCollectionResponse**](../Model/PriceRecordCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **setPriceListRecord**
> \BigCommerce\Api\v3\Model\PriceRecordResponse setPriceListRecord($price_list_id, $variant_id, $currency_code, $price_record)



Creates or updates a single `Price Record` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$currency_code = "currency_code_example"; // string | The currency code associated with the price record being acted upon.
$price_record = new \BigCommerce\Api\v3\Model\PriceRecordPut(); // \BigCommerce\Api\v3\Model\PriceRecordPut | A BigCommerce `Price Record` object.

try {
    $result = $api_instance->setPriceListRecord($price_list_id, $variant_id, $currency_code, $price_record);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->setPriceListRecord: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **currency_code** | **string**| The currency code associated with the price record being acted upon. |
 **price_record** | [**\BigCommerce\Api\v3\Model\PriceRecordPut**](../Model/\BigCommerce\Api\v3\Model\PriceRecordPut.md)| A BigCommerce &#x60;Price Record&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\PriceRecordResponse**](../Model/PriceRecordResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **setPriceListRecordCollection**
> \BigCommerce\Api\v3\Model\SuccessBatchResponse setPriceListRecordCollection($price_list_id, $price_record_batch, $x_strict_mode)



Creates or updates a batch of `Price Records` associated with a particular Price List.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$price_record_batch = new \BigCommerce\Api\v3\Model\PriceRecordCollectionPut(); // \BigCommerce\Api\v3\Model\PriceRecordCollectionPut | A BigCommerce `Price Record` request.
$x_strict_mode = 0; // int | Header that determines whether the Batch API operates in strict mode or not.  Strict mode will reject the entire request if any item in the batch has an error.

try {
    $result = $api_instance->setPriceListRecordCollection($price_list_id, $price_record_batch, $x_strict_mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->setPriceListRecordCollection: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **price_record_batch** | [**\BigCommerce\Api\v3\Model\PriceRecordCollectionPut**](../Model/\BigCommerce\Api\v3\Model\PriceRecordCollectionPut.md)| A BigCommerce &#x60;Price Record&#x60; request. |
 **x_strict_mode** | **int**| Header that determines whether the Batch API operates in strict mode or not.  Strict mode will reject the entire request if any item in the batch has an error. | [optional] [default to 0]

### Return type

[**\BigCommerce\Api\v3\Model\SuccessBatchResponse**](../Model/SuccessBatchResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updatePriceList**
> \BigCommerce\Api\v3\Model\PriceListResponse updatePriceList($price_list_id, $price_list)



Updates a single `Price List` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PriceListsApi();
$price_list_id = 56; // int | The ID of the `Price List` requested.
$price_list = new \BigCommerce\Api\v3\Model\PriceListPut(); // \BigCommerce\Api\v3\Model\PriceListPut | A BigCommerce `Price List` object.

try {
    $result = $api_instance->updatePriceList($price_list_id, $price_list);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PriceListsApi->updatePriceList: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60; requested. |
 **price_list** | [**\BigCommerce\Api\v3\Model\PriceListPut**](../Model/\BigCommerce\Api\v3\Model\PriceListPut.md)| A BigCommerce &#x60;Price List&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\PriceListResponse**](../Model/PriceListResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

