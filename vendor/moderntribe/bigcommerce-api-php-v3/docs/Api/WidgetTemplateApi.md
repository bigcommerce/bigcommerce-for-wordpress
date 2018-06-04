# BigCommerce\Api\v3\WidgetTemplateApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**createWidgetTemplate**](WidgetTemplateApi.md#createWidgetTemplate) | **POST** /content/widget-templates | Creates a widget template.
[**deleteWidgetTemplate**](WidgetTemplateApi.md#deleteWidgetTemplate) | **DELETE** /content/widget-templates/{uuid} | Deletes a widget template.
[**getWidgetTemplate**](WidgetTemplateApi.md#getWidgetTemplate) | **GET** /content/widget-templates/{uuid} | Gets a widget template.
[**getWidgetTemplates**](WidgetTemplateApi.md#getWidgetTemplates) | **GET** /content/widget-templates | Gets all widget templates.
[**previewWidget**](WidgetTemplateApi.md#previewWidget) | **POST** /content/widget-templates/{uuid}/preview | Render a widget template and return the widget html.
[**updateWidgetTemplate**](WidgetTemplateApi.md#updateWidgetTemplate) | **PUT** /content/widget-templates/{uuid} | Updates a widget template.


# **createWidgetTemplate**
> \BigCommerce\Api\v3\Model\WidgetTemplateResponse createWidgetTemplate($template_body)

Creates a widget template.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetTemplateApi();
$template_body = new \BigCommerce\Api\v3\Model\WidgetTemplatePost(); // \BigCommerce\Api\v3\Model\WidgetTemplatePost | 

try {
    $result = $api_instance->createWidgetTemplate($template_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetTemplateApi->createWidgetTemplate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **template_body** | [**\BigCommerce\Api\v3\Model\WidgetTemplatePost**](../Model/\BigCommerce\Api\v3\Model\WidgetTemplatePost.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetTemplateResponse**](../Model/WidgetTemplateResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteWidgetTemplate**
> deleteWidgetTemplate($uuid)

Deletes a widget template.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetTemplateApi();
$uuid = "uuid_example"; // string | The identifier for a specific template.

try {
    $api_instance->deleteWidgetTemplate($uuid);
} catch (Exception $e) {
    echo 'Exception when calling WidgetTemplateApi->deleteWidgetTemplate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific template. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getWidgetTemplate**
> \BigCommerce\Api\v3\Model\WidgetTemplateResponse getWidgetTemplate($uuid)

Gets a widget template.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetTemplateApi();
$uuid = "uuid_example"; // string | The identifier for a specific template.

try {
    $result = $api_instance->getWidgetTemplate($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetTemplateApi->getWidgetTemplate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific template. |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetTemplateResponse**](../Model/WidgetTemplateResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getWidgetTemplates**
> \BigCommerce\Api\v3\Model\WidgetTemplatesResponse getWidgetTemplates($page, $limit, $widget_template_kind)

Gets all widget templates.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetTemplateApi();
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$widget_template_kind = "widget_template_kind_example"; // string | The kind of widget template.

try {
    $result = $api_instance->getWidgetTemplates($page, $limit, $widget_template_kind);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetTemplateApi->getWidgetTemplates: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **widget_template_kind** | **string**| The kind of widget template. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\WidgetTemplatesResponse**](../Model/WidgetTemplatesResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **previewWidget**
> \BigCommerce\Api\v3\Model\WidgetTemplatePreviewResponse previewWidget($uuid, $template_body)

Render a widget template and return the widget html.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetTemplateApi();
$uuid = "uuid_example"; // string | The identifier for a specific template.
$template_body = new \BigCommerce\Api\v3\Model\WidgetTemplatePreview(); // \BigCommerce\Api\v3\Model\WidgetTemplatePreview | 

try {
    $result = $api_instance->previewWidget($uuid, $template_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetTemplateApi->previewWidget: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific template. |
 **template_body** | [**\BigCommerce\Api\v3\Model\WidgetTemplatePreview**](../Model/\BigCommerce\Api\v3\Model\WidgetTemplatePreview.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetTemplatePreviewResponse**](../Model/WidgetTemplatePreviewResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateWidgetTemplate**
> \BigCommerce\Api\v3\Model\WidgetTemplateResponse updateWidgetTemplate($uuid, $template_body)

Updates a widget template.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\WidgetTemplateApi();
$uuid = "uuid_example"; // string | The identifier for a specific template.
$template_body = new \BigCommerce\Api\v3\Model\WidgetTemplatePut(); // \BigCommerce\Api\v3\Model\WidgetTemplatePut | 

try {
    $result = $api_instance->updateWidgetTemplate($uuid, $template_body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WidgetTemplateApi->updateWidgetTemplate: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **uuid** | **string**| The identifier for a specific template. |
 **template_body** | [**\BigCommerce\Api\v3\Model\WidgetTemplatePut**](../Model/\BigCommerce\Api\v3\Model\WidgetTemplatePut.md)|  |

### Return type

[**\BigCommerce\Api\v3\Model\WidgetTemplateResponse**](../Model/WidgetTemplateResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

