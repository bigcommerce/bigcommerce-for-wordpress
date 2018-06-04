# BigCommerce\Api\v3\CustomersApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createSubscriber**](CustomersApi.md#createSubscriber) | **POST** /customers/subscribers | 
[**deleteSubscriberById**](CustomersApi.md#deleteSubscriberById) | **DELETE** /customers/subscribers/{subscriber_id} | 
[**deleteSubscribers**](CustomersApi.md#deleteSubscribers) | **DELETE** /customers/subscribers | 
[**getSubscriberById**](CustomersApi.md#getSubscriberById) | **GET** /customers/subscribers/{subscriber_id} | 
[**getSubscribers**](CustomersApi.md#getSubscribers) | **GET** /customers/subscribers | 
[**updateSubscriber**](CustomersApi.md#updateSubscriber) | **PUT** /customers/subscribers/{subscriber_id} | 


# **createSubscriber**
> \BigCommerce\Api\v3\Model\SubscriberResponse createSubscriber($subscriber)



Creates a `Subscriber` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CustomersApi();
$subscriber = new \BigCommerce\Api\v3\Model\SubscriberPost(); // \BigCommerce\Api\v3\Model\SubscriberPost | `Subscriber` object.

try {
    $result = $api_instance->createSubscriber($subscriber);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->createSubscriber: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscriber** | [**\BigCommerce\Api\v3\Model\SubscriberPost**](../Model/\BigCommerce\Api\v3\Model\SubscriberPost.md)| &#x60;Subscriber&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\SubscriberResponse**](../Model/SubscriberResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteSubscriberById**
> deleteSubscriberById($subscriber_id)



Deletes a `Subscriber` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CustomersApi();
$subscriber_id = 56; // int | The ID of the `Subscriber` requested.

try {
    $api_instance->deleteSubscriberById($subscriber_id);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->deleteSubscriberById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscriber_id** | **int**| The ID of the &#x60;Subscriber&#x60; requested. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteSubscribers**
> deleteSubscribers($email, $first_name, $last_name, $source, $order_id, $date_created, $date_modified)



Deletes a Subscriber or Subscribers from BigCommerce Customers.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CustomersApi();
$email = "email_example"; // string | Filter items by email.
$first_name = "first_name_example"; // string | Filter items by first_name.
$last_name = "last_name_example"; // string | Filter items by last_name.
$source = "source_example"; // string | Filter items by source.
$order_id = 56; // int | Filter items by order_id.
$date_created = new \DateTime(); // \DateTime | Filter items by date_created.
$date_modified = new \DateTime(); // \DateTime | Filter items by date_modified.

try {
    $api_instance->deleteSubscribers($email, $first_name, $last_name, $source, $order_id, $date_created, $date_modified);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->deleteSubscribers: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **email** | **string**| Filter items by email. | [optional]
 **first_name** | **string**| Filter items by first_name. | [optional]
 **last_name** | **string**| Filter items by last_name. | [optional]
 **source** | **string**| Filter items by source. | [optional]
 **order_id** | **int**| Filter items by order_id. | [optional]
 **date_created** | **\DateTime**| Filter items by date_created. | [optional]
 **date_modified** | **\DateTime**| Filter items by date_modified. | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getSubscriberById**
> \BigCommerce\Api\v3\Model\SubscriberResponse getSubscriberById($subscriber_id)



Gets `Subscriber` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CustomersApi();
$subscriber_id = 56; // int | The ID of the `Subscriber` requested.

try {
    $result = $api_instance->getSubscriberById($subscriber_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->getSubscriberById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscriber_id** | **int**| The ID of the &#x60;Subscriber&#x60; requested. |

### Return type

[**\BigCommerce\Api\v3\Model\SubscriberResponse**](../Model/SubscriberResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getSubscribers**
> \BigCommerce\Api\v3\Model\SubscriberCollectionResponse getSubscribers($email, $first_name, $last_name, $source, $order_id, $date_created, $date_modified, $page, $limit)



Returns a paginated Subscribers collection.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CustomersApi();
$email = "email_example"; // string | Filter items by email.
$first_name = "first_name_example"; // string | Filter items by first_name.
$last_name = "last_name_example"; // string | Filter items by last_name.
$source = "source_example"; // string | Filter items by source.
$order_id = 56; // int | Filter items by order_id.
$date_created = new \DateTime(); // \DateTime | Filter items by date_created.
$date_modified = new \DateTime(); // \DateTime | Filter items by date_modified.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->getSubscribers($email, $first_name, $last_name, $source, $order_id, $date_created, $date_modified, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->getSubscribers: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **email** | **string**| Filter items by email. | [optional]
 **first_name** | **string**| Filter items by first_name. | [optional]
 **last_name** | **string**| Filter items by last_name. | [optional]
 **source** | **string**| Filter items by source. | [optional]
 **order_id** | **int**| Filter items by order_id. | [optional]
 **date_created** | **\DateTime**| Filter items by date_created. | [optional]
 **date_modified** | **\DateTime**| Filter items by date_modified. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\SubscriberCollectionResponse**](../Model/SubscriberCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateSubscriber**
> \BigCommerce\Api\v3\Model\SubscriberResponse updateSubscriber($subscriber_id, $subscriber)



Updates a `Subscriber` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CustomersApi();
$subscriber_id = 56; // int | The ID of the `Subscriber` requested.
$subscriber = new \BigCommerce\Api\v3\Model\SubscriberPut(); // \BigCommerce\Api\v3\Model\SubscriberPut | Returns a `Subscriber` object.

try {
    $result = $api_instance->updateSubscriber($subscriber_id, $subscriber);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->updateSubscriber: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscriber_id** | **int**| The ID of the &#x60;Subscriber&#x60; requested. |
 **subscriber** | [**\BigCommerce\Api\v3\Model\SubscriberPut**](../Model/\BigCommerce\Api\v3\Model\SubscriberPut.md)| Returns a &#x60;Subscriber&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\SubscriberResponse**](../Model/SubscriberResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

