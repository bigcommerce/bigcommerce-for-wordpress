# BigCommerce\Api\v3\OrdersApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createTransaction**](OrdersApi.md#createTransaction) | **POST** /orders/{order_id}/transactions | 
[**getTransactions**](OrdersApi.md#getTransactions) | **GET** /orders/{order_id}/transactions | 


# **createTransaction**
> \BigCommerce\Api\v3\Model\TransactionResponse createTransaction($order_id, $transaction)



Creates a new `Transaction` related to a BigCommerce Order.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\OrdersApi();
$order_id = 56; // int | The ID of the `Order` to which the transactions belong.
$transaction = new \BigCommerce\Api\v3\Model\TransactionPost(); // \BigCommerce\Api\v3\Model\TransactionPost | A BigCommerce `Transaction` object.

try {
    $result = $api_instance->createTransaction($order_id, $transaction);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling OrdersApi->createTransaction: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **order_id** | **int**| The ID of the &#x60;Order&#x60; to which the transactions belong. |
 **transaction** | [**\BigCommerce\Api\v3\Model\TransactionPost**](../Model/\BigCommerce\Api\v3\Model\TransactionPost.md)| A BigCommerce &#x60;Transaction&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\TransactionResponse**](../Model/TransactionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getTransactions**
> \BigCommerce\Api\v3\Model\TransactionCollectionResponse getTransactions($order_id)



Returns a collection of `Transaction` objects related to a BigCommerce Order.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\OrdersApi();
$order_id = 56; // int | The ID of the `Order` to which the transactions belong.

try {
    $result = $api_instance->getTransactions($order_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling OrdersApi->getTransactions: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **order_id** | **int**| The ID of the &#x60;Order&#x60; to which the transactions belong. |

### Return type

[**\BigCommerce\Api\v3\Model\TransactionCollectionResponse**](../Model/TransactionCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

