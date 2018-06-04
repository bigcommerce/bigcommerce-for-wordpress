# BigCommerce\Api\v3\PlacementApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createPlacement**](PlacementApi.md#createPlacement) | **POST** /content/placements | Creates a placement.
[**deletePlacement**](PlacementApi.md#deletePlacement) | **DELETE** /content/placements/{uuid} | Deletes a placement.
[**getPlacement**](PlacementApi.md#getPlacement) | **GET** /content/placements/{uuid} | Gets a placement.
[**getPlacements**](PlacementApi.md#getPlacements) | **GET** /content/placements | Gets all placements.
[**updatePlacement**](PlacementApi.md#updatePlacement) | **PUT** /content/placements/{uuid} | Updates a placement.


# **createPlacement**
> \BigCommerce\Api\v3\Model\PlacementResponse createPlacement($placement_body)

Creates a placement.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PlacementApi();
$placement_body = new \BigCommerce\Api\v3\Model\PlacementPost(); // \BigCommerce\Api\v3\Model\PlacementPost | 

try {
    $result = $api_instance->createPlacement($placement_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PlacementApi->createPlacement: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **placement_body** | [**\BigCommerce\Api\v3\Model\PlacementPost**](../Model/\BigCommerce\Api\v3\Model\PlacementPost.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\PlacementResponse**](../Model/PlacementResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deletePlacement**
> deletePlacement($uuid)

Deletes a placement.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PlacementApi();
$uuid = "uuid_example"; // string | The identifier for a specific placement.

try {
    $api_instance->deletePlacement($uuid);
} catch (Exception $e) {
    echo 'Exception when calling PlacementApi->deletePlacement: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific placement. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPlacement**
> \BigCommerce\Api\v3\Model\PlacementResponse getPlacement($uuid)

Gets a placement.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PlacementApi();
$uuid = "uuid_example"; // string | The identifier for a specific placement.

try {
    $result = $api_instance->getPlacement($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PlacementApi->getPlacement: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific placement. |

### Return type

[**\BigCommerce\Api\v3\Model\PlacementResponse**](../Model/PlacementResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getPlacements**
> \BigCommerce\Api\v3\Model\PlacementsResponse getPlacements($page, $limit, $widget_template_kind, $template_file, $widget_uuid, $widget_template_uuid)

Gets all placements.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PlacementApi();
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$widget_template_kind = "widget_template_kind_example"; // string | The kind of widget template.
$template_file = "template_file_example"; // string | The template file, for example: `pages/home`.
$widget_uuid = "widget_uuid_example"; // string | The identifier for a specific widget.
$widget_template_uuid = "widget_template_uuid_example"; // string | The identifier for a specific widget template.

try {
    $result = $api_instance->getPlacements($page, $limit, $widget_template_kind, $template_file, $widget_uuid, $widget_template_uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PlacementApi->getPlacements: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **widget_template_kind** | **string**| The kind of widget template. | [optional]
 **template_file** | **string**| The template file, for example: &#x60;pages/home&#x60;. | [optional]
 **widget_uuid** | **string**| The identifier for a specific widget. | [optional]
 **widget_template_uuid** | **string**| The identifier for a specific widget template. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\PlacementsResponse**](../Model/PlacementsResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updatePlacement**
> \BigCommerce\Api\v3\Model\PlacementResponse updatePlacement($uuid, $placement_body)

Updates a placement.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\PlacementApi();
$uuid = "uuid_example"; // string | The identifier for a specific placement.
$placement_body = new \BigCommerce\Api\v3\Model\PlacementPut(); // \BigCommerce\Api\v3\Model\PlacementPut | 

try {
    $result = $api_instance->updatePlacement($uuid, $placement_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PlacementApi->updatePlacement: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific placement. |
 **placement_body** | [**\BigCommerce\Api\v3\Model\PlacementPut**](../Model/\BigCommerce\Api\v3\Model\PlacementPut.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\PlacementResponse**](../Model/PlacementResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

