# BigCommerce\Api\v3\ThemesApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**activateStoreTheme**](ThemesApi.md#activateStoreTheme) | **POST** /themes/actions/activate | Activates a store theme.
[**deleteStoreTheme**](ThemesApi.md#deleteStoreTheme) | **DELETE** /themes/{theme_id} | Deletes a specified store theme.
[**downloadTheme**](ThemesApi.md#downloadTheme) | **POST** /themes/{theme_id}/actions/download | Downloads a specified store theme.
[**getJob**](ThemesApi.md#getJob) | **GET** /themes/jobs/{job_id} | Gets a specified job.
[**getStoreTheme**](ThemesApi.md#getStoreTheme) | **GET** /themes/{theme_id} | Gets a specified store theme.
[**getStoreThemes**](ThemesApi.md#getStoreThemes) | **GET** /themes | Gets all store themes.
[**uploadTheme**](ThemesApi.md#uploadTheme) | **POST** /themes | Uploads a new theme to a BigCommerce store.


# **activateStoreTheme**
> \BigCommerce\Api\v3\Model\NoContent activateStoreTheme($body)

Activates a store theme.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();
$body = new \BigCommerce\Api\v3\Model\Activate(); // \BigCommerce\Api\v3\Model\Activate | Request parameters.

try {
    $result = $api_instance->activateStoreTheme($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->activateStoreTheme: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **body** | [**\BigCommerce\Api\v3\Model\Activate**](../Model/\BigCommerce\Api\v3\Model\Activate.md)| Request parameters. |

### Return type

[**\BigCommerce\Api\v3\Model\NoContent**](../Model/NoContent.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteStoreTheme**
> \BigCommerce\Api\v3\Model\NoContent deleteStoreTheme($theme_id)

Deletes a specified store theme.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();
$theme_id = "theme_id_example"; // string | The theme identifier.

try {
    $result = $api_instance->deleteStoreTheme($theme_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->deleteStoreTheme: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **theme_id** | **string**| The theme identifier. |

### Return type

[**\BigCommerce\Api\v3\Model\NoContent**](../Model/NoContent.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **downloadTheme**
> \BigCommerce\Api\v3\Model\JobId downloadTheme($theme_id, $which)

Downloads a specified store theme.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();
$theme_id = "theme_id_example"; // string | The theme identifier.
$which = new \BigCommerce\Api\v3\Model\WhichThemeToDownload(); // \BigCommerce\Api\v3\Model\WhichThemeToDownload | A BigCommerce object specifying which theme to download. One of: `original`: the original Marketplace or uploaded custom theme; `last_activated`: the theme version most recently applied to the store; `last_created`: the theme version most recently created. If `which` is missing or invalid in the request, its value will default to `last_activated`.

try {
    $result = $api_instance->downloadTheme($theme_id, $which);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->downloadTheme: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **theme_id** | **string**| The theme identifier. |
 **which** | [**\BigCommerce\Api\v3\Model\WhichThemeToDownload**](../Model/\BigCommerce\Api\v3\Model\WhichThemeToDownload.md)| A BigCommerce object specifying which theme to download. One of: &#x60;original&#x60;: the original Marketplace or uploaded custom theme; &#x60;last_activated&#x60;: the theme version most recently applied to the store; &#x60;last_created&#x60;: the theme version most recently created. If &#x60;which&#x60; is missing or invalid in the request, its value will default to &#x60;last_activated&#x60;. |

### Return type

[**\BigCommerce\Api\v3\Model\JobId**](../Model/JobId.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getJob**
> \BigCommerce\Api\v3\Model\JobResponse getJob($job_id)

Gets a specified job.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();
$job_id = "job_id_example"; // string | The job identifier.

try {
    $result = $api_instance->getJob($job_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->getJob: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **job_id** | **string**| The job identifier. |

### Return type

[**\BigCommerce\Api\v3\Model\JobResponse**](../Model/JobResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getStoreTheme**
> \BigCommerce\Api\v3\Model\ThemeResponse getStoreTheme($theme_id)

Gets a specified store theme.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();
$theme_id = "theme_id_example"; // string | The theme identifier.

try {
    $result = $api_instance->getStoreTheme($theme_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->getStoreTheme: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **theme_id** | **string**| The theme identifier. |

### Return type

[**\BigCommerce\Api\v3\Model\ThemeResponse**](../Model/ThemeResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getStoreThemes**
> \BigCommerce\Api\v3\Model\ThemesCollectionResponse getStoreThemes()

Gets all store themes.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();

try {
    $result = $api_instance->getStoreThemes();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->getStoreThemes: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\BigCommerce\Api\v3\Model\ThemesCollectionResponse**](../Model/ThemesCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **uploadTheme**
> \BigCommerce\Api\v3\Model\JobId uploadTheme($file)

Uploads a new theme to a BigCommerce store.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\ThemesApi();
$file = "/path/to/file.txt"; // \SplFileObject | The file.

try {
    $result = $api_instance->uploadTheme($file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ThemesApi->uploadTheme: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **file** | **\SplFileObject**| The file. |

### Return type

[**\BigCommerce\Api\v3\Model\JobId**](../Model/JobId.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: multipart/form-data
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

