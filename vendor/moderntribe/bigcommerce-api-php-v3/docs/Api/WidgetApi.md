# BigCommerce\Api\v3\WidgetApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createWidget**](WidgetApi.md#createWidget) | **POST** /content/widgets | Creates a widget.
[**deleteWidget**](WidgetApi.md#deleteWidget) | **DELETE** /content/widgets/{uuid} | Deletes a widget.
[**getWidget**](WidgetApi.md#getWidget) | **GET** /content/widgets/{uuid} | Gets a widget.
[**getWidgets**](WidgetApi.md#getWidgets) | **GET** /content/widgets | Gets all widgets.
[**searchWidgets**](WidgetApi.md#searchWidgets) | **GET** /content/widgets/search | Gets all widgets by search.
[**updateWidget**](WidgetApi.md#updateWidget) | **PUT** /content/widgets/{uuid} | Updates a widget.


# **createWidget**
> \BigCommerce\Api\v3\Model\WidgetResponse createWidget($widget_body)

Creates a widget.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetApi();
$widget_body = new \BigCommerce\Api\v3\Model\WidgetPost(); // \BigCommerce\Api\v3\Model\WidgetPost | 

try {
    $result = $api_instance->createWidget($widget_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetApi->createWidget: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **widget_body** | [**\BigCommerce\Api\v3\Model\WidgetPost**](../Model/\BigCommerce\Api\v3\Model\WidgetPost.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetResponse**](../Model/WidgetResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteWidget**
> deleteWidget($uuid)

Deletes a widget.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetApi();
$uuid = "uuid_example"; // string | The identifier for a specific widget.

try {
    $api_instance->deleteWidget($uuid);
} catch (Exception $e) {
    echo 'Exception when calling WidgetApi->deleteWidget: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific widget. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getWidget**
> \BigCommerce\Api\v3\Model\WidgetResponse getWidget($uuid)

Gets a widget.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetApi();
$uuid = "uuid_example"; // string | The identifier for a specific widget.

try {
    $result = $api_instance->getWidget($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetApi->getWidget: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific widget. |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetResponse**](../Model/WidgetResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getWidgets**
> \BigCommerce\Api\v3\Model\WidgetsResponse getWidgets($page, $limit, $widget_template_kind, $widget_template_uuid)

Gets all widgets.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetApi();
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$widget_template_kind = "widget_template_kind_example"; // string | The kind of widget template.
$widget_template_uuid = "widget_template_uuid_example"; // string | The identifier for a specific widget template.

try {
    $result = $api_instance->getWidgets($page, $limit, $widget_template_kind, $widget_template_uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetApi->getWidgets: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **widget_template_kind** | **string**| The kind of widget template. | [optional]
 **widget_template_uuid** | **string**| The identifier for a specific widget template. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\WidgetsResponse**](../Model/WidgetsResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **searchWidgets**
> \BigCommerce\Api\v3\Model\WidgetsResponse searchWidgets($page, $limit, $query)

Gets all widgets by search.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetApi();
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$query = "query_example"; // string | The query string associated with a widget's name and description.

try {
    $result = $api_instance->searchWidgets($page, $limit, $query);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetApi->searchWidgets: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **query** | **string**| The query string associated with a widget&#39;s name and description. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\WidgetsResponse**](../Model/WidgetsResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateWidget**
> \BigCommerce\Api\v3\Model\WidgetResponse updateWidget($uuid, $widget_body)

Updates a widget.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetApi();
$uuid = "uuid_example"; // string | The identifier for a specific widget.
$widget_body = new \BigCommerce\Api\v3\Model\WidgetPut(); // \BigCommerce\Api\v3\Model\WidgetPut | 

try {
    $result = $api_instance->updateWidget($uuid, $widget_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetApi->updateWidget: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific widget. |
 **widget_body** | [**\BigCommerce\Api\v3\Model\WidgetPut**](../Model/\BigCommerce\Api\v3\Model\WidgetPut.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetResponse**](../Model/WidgetResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

