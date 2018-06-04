# BigCommerce\Api\v3\CatalogApi

All URIs are relative to *https://api.bigcommerce.com/stores/{{store_id}}/v3*

Method | HTTP request | Description
------------- | ------------- | -------------
[**catalogSummaryGet**](CatalogApi.md#catalogSummaryGet) | **GET** /catalog/summary | 
[**createBrand**](CatalogApi.md#createBrand) | **POST** /catalog/brands | 
[**createBrandImage**](CatalogApi.md#createBrandImage) | **POST** /catalog/brands/{brand_id}/image | 
[**createBrandMetafield**](CatalogApi.md#createBrandMetafield) | **POST** /catalog/brands/{brand_id}/metafields | 
[**createBulkPricingRule**](CatalogApi.md#createBulkPricingRule) | **POST** /catalog/products/{product_id}/bulk-pricing-rules | 
[**createCategory**](CatalogApi.md#createCategory) | **POST** /catalog/categories | 
[**createCategoryImage**](CatalogApi.md#createCategoryImage) | **POST** /catalog/categories/{category_id}/image | 
[**createCategoryMetafield**](CatalogApi.md#createCategoryMetafield) | **POST** /catalog/categories/{category_id}/metafields | 
[**createComplexRule**](CatalogApi.md#createComplexRule) | **POST** /catalog/products/{product_id}/complex-rules | 
[**createConfigurableField**](CatalogApi.md#createConfigurableField) | **POST** /catalog/products/{product_id}/configurable-fields | 
[**createCustomField**](CatalogApi.md#createCustomField) | **POST** /catalog/products/{product_id}/custom-fields | 
[**createModifier**](CatalogApi.md#createModifier) | **POST** /catalog/products/{product_id}/modifiers | 
[**createModifierImage**](CatalogApi.md#createModifierImage) | **POST** /catalog/products/{product_id}/modifiers/{modifier_id}/values/{value_id}/image | 
[**createModifierValue**](CatalogApi.md#createModifierValue) | **POST** /catalog/products/{product_id}/modifiers/{modifier_id}/values | 
[**createOption**](CatalogApi.md#createOption) | **POST** /catalog/products/{product_id}/options | 
[**createOptionValue**](CatalogApi.md#createOptionValue) | **POST** /catalog/products/{product_id}/options/{option_id}/values | 
[**createProduct**](CatalogApi.md#createProduct) | **POST** /catalog/products | 
[**createProductImage**](CatalogApi.md#createProductImage) | **POST** /catalog/products/{product_id}/images | 
[**createProductMetafield**](CatalogApi.md#createProductMetafield) | **POST** /catalog/products/{product_id}/metafields | 
[**createProductReview**](CatalogApi.md#createProductReview) | **POST** /catalog/products/{product_id}/reviews | 
[**createProductVideo**](CatalogApi.md#createProductVideo) | **POST** /catalog/products/{product_id}/videos | 
[**createVariant**](CatalogApi.md#createVariant) | **POST** /catalog/products/{product_id}/variants | 
[**createVariantImage**](CatalogApi.md#createVariantImage) | **POST** /catalog/products/{product_id}/variants/{variant_id}/image | 
[**createVariantMetafield**](CatalogApi.md#createVariantMetafield) | **POST** /catalog/products/{product_id}/variants/{variant_id}/metafields | 
[**deleteBrandById**](CatalogApi.md#deleteBrandById) | **DELETE** /catalog/brands/{brand_id} | 
[**deleteBrandImage**](CatalogApi.md#deleteBrandImage) | **DELETE** /catalog/brands/{brand_id}/image | 
[**deleteBrandMetafieldById**](CatalogApi.md#deleteBrandMetafieldById) | **DELETE** /catalog/brands/{brand_id}/metafields/{metafield_id} | 
[**deleteBrands**](CatalogApi.md#deleteBrands) | **DELETE** /catalog/brands | 
[**deleteBulkPricingRuleById**](CatalogApi.md#deleteBulkPricingRuleById) | **DELETE** /catalog/products/{product_id}/bulk-pricing-rules/{bulk_pricing_rule_id} | 
[**deleteCategories**](CatalogApi.md#deleteCategories) | **DELETE** /catalog/categories | 
[**deleteCategoryById**](CatalogApi.md#deleteCategoryById) | **DELETE** /catalog/categories/{category_id} | 
[**deleteCategoryImage**](CatalogApi.md#deleteCategoryImage) | **DELETE** /catalog/categories/{category_id}/image | 
[**deleteCategoryMetafieldById**](CatalogApi.md#deleteCategoryMetafieldById) | **DELETE** /catalog/categories/{category_id}/metafields/{metafield_id} | 
[**deleteComplexRuleById**](CatalogApi.md#deleteComplexRuleById) | **DELETE** /catalog/products/{product_id}/complex-rules/{complex_rule_id} | 
[**deleteConfigurableFieldById**](CatalogApi.md#deleteConfigurableFieldById) | **DELETE** /catalog/products/{product_id}/configurable-fields/{configurable_field_id} | 
[**deleteCustomFieldById**](CatalogApi.md#deleteCustomFieldById) | **DELETE** /catalog/products/{product_id}/custom-fields/{custom_field_id} | 
[**deleteModifierById**](CatalogApi.md#deleteModifierById) | **DELETE** /catalog/products/{product_id}/modifiers/{modifier_id} | 
[**deleteModifierImage**](CatalogApi.md#deleteModifierImage) | **DELETE** /catalog/products/{product_id}/modifiers/{modifier_id}/values/{value_id}/image | 
[**deleteModifierValueById**](CatalogApi.md#deleteModifierValueById) | **DELETE** /catalog/products/{product_id}/modifiers/{modifier_id}/values/{value_id} | 
[**deleteOptionById**](CatalogApi.md#deleteOptionById) | **DELETE** /catalog/products/{product_id}/options/{option_id} | 
[**deleteOptionValueById**](CatalogApi.md#deleteOptionValueById) | **DELETE** /catalog/products/{product_id}/options/{option_id}/values/{value_id} | 
[**deleteProductById**](CatalogApi.md#deleteProductById) | **DELETE** /catalog/products/{product_id} | 
[**deleteProductImage**](CatalogApi.md#deleteProductImage) | **DELETE** /catalog/products/{product_id}/images/{image_id} | 
[**deleteProductMetafieldById**](CatalogApi.md#deleteProductMetafieldById) | **DELETE** /catalog/products/{product_id}/metafields/{metafield_id} | 
[**deleteProductReview**](CatalogApi.md#deleteProductReview) | **DELETE** /catalog/products/{product_id}/reviews/{review_id} | 
[**deleteProductVideo**](CatalogApi.md#deleteProductVideo) | **DELETE** /catalog/products/{product_id}/videos/{video_id} | 
[**deleteProducts**](CatalogApi.md#deleteProducts) | **DELETE** /catalog/products | 
[**deleteVariantById**](CatalogApi.md#deleteVariantById) | **DELETE** /catalog/products/{product_id}/variants/{variant_id} | 
[**deleteVariantMetafieldById**](CatalogApi.md#deleteVariantMetafieldById) | **DELETE** /catalog/products/{product_id}/variants/{variant_id}/metafields/{metafield_id} | 
[**getBrandById**](CatalogApi.md#getBrandById) | **GET** /catalog/brands/{brand_id} | 
[**getBrandMetafieldByBrandId**](CatalogApi.md#getBrandMetafieldByBrandId) | **GET** /catalog/brands/{brand_id}/metafields/{metafield_id} | 
[**getBrandMetafieldsByBrandId**](CatalogApi.md#getBrandMetafieldsByBrandId) | **GET** /catalog/brands/{brand_id}/metafields | 
[**getBrands**](CatalogApi.md#getBrands) | **GET** /catalog/brands | 
[**getBulkPricingRuleById**](CatalogApi.md#getBulkPricingRuleById) | **GET** /catalog/products/{product_id}/bulk-pricing-rules/{bulk_pricing_rule_id} | 
[**getBulkPricingRules**](CatalogApi.md#getBulkPricingRules) | **GET** /catalog/products/{product_id}/bulk-pricing-rules | 
[**getCategories**](CatalogApi.md#getCategories) | **GET** /catalog/categories | 
[**getCategoryById**](CatalogApi.md#getCategoryById) | **GET** /catalog/categories/{category_id} | 
[**getCategoryMetafieldByCategoryId**](CatalogApi.md#getCategoryMetafieldByCategoryId) | **GET** /catalog/categories/{category_id}/metafields/{metafield_id} | 
[**getCategoryMetafieldsByCategoryId**](CatalogApi.md#getCategoryMetafieldsByCategoryId) | **GET** /catalog/categories/{category_id}/metafields | 
[**getCategoryTree**](CatalogApi.md#getCategoryTree) | **GET** /catalog/categories/tree | 
[**getComplexRuleById**](CatalogApi.md#getComplexRuleById) | **GET** /catalog/products/{product_id}/complex-rules/{complex_rule_id} | 
[**getComplexRules**](CatalogApi.md#getComplexRules) | **GET** /catalog/products/{product_id}/complex-rules | 
[**getConfigurableFieldById**](CatalogApi.md#getConfigurableFieldById) | **GET** /catalog/products/{product_id}/configurable-fields/{configurable_field_id} | 
[**getConfigurableFields**](CatalogApi.md#getConfigurableFields) | **GET** /catalog/products/{product_id}/configurable-fields | 
[**getCustomFieldById**](CatalogApi.md#getCustomFieldById) | **GET** /catalog/products/{product_id}/custom-fields/{custom_field_id} | 
[**getCustomFields**](CatalogApi.md#getCustomFields) | **GET** /catalog/products/{product_id}/custom-fields | 
[**getModifierById**](CatalogApi.md#getModifierById) | **GET** /catalog/products/{product_id}/modifiers/{modifier_id} | 
[**getModifierValueById**](CatalogApi.md#getModifierValueById) | **GET** /catalog/products/{product_id}/modifiers/{modifier_id}/values/{value_id} | 
[**getModifierValues**](CatalogApi.md#getModifierValues) | **GET** /catalog/products/{product_id}/modifiers/{modifier_id}/values | 
[**getModifiers**](CatalogApi.md#getModifiers) | **GET** /catalog/products/{product_id}/modifiers | 
[**getOptionById**](CatalogApi.md#getOptionById) | **GET** /catalog/products/{product_id}/options/{option_id} | 
[**getOptionValueById**](CatalogApi.md#getOptionValueById) | **GET** /catalog/products/{product_id}/options/{option_id}/values/{value_id} | 
[**getOptionValues**](CatalogApi.md#getOptionValues) | **GET** /catalog/products/{product_id}/options/{option_id}/values | 
[**getOptions**](CatalogApi.md#getOptions) | **GET** /catalog/products/{product_id}/options | 
[**getProductById**](CatalogApi.md#getProductById) | **GET** /catalog/products/{product_id} | 
[**getProductImageById**](CatalogApi.md#getProductImageById) | **GET** /catalog/products/{product_id}/images/{image_id} | 
[**getProductImages**](CatalogApi.md#getProductImages) | **GET** /catalog/products/{product_id}/images | 
[**getProductMetafieldByProductId**](CatalogApi.md#getProductMetafieldByProductId) | **GET** /catalog/products/{product_id}/metafields/{metafield_id} | 
[**getProductMetafieldsByProductId**](CatalogApi.md#getProductMetafieldsByProductId) | **GET** /catalog/products/{product_id}/metafields | 
[**getProductReviewById**](CatalogApi.md#getProductReviewById) | **GET** /catalog/products/{product_id}/reviews/{review_id} | 
[**getProductReviews**](CatalogApi.md#getProductReviews) | **GET** /catalog/products/{product_id}/reviews | 
[**getProductVideoById**](CatalogApi.md#getProductVideoById) | **GET** /catalog/products/{product_id}/videos/{video_id} | 
[**getProductVideos**](CatalogApi.md#getProductVideos) | **GET** /catalog/products/{product_id}/videos | 
[**getProducts**](CatalogApi.md#getProducts) | **GET** /catalog/products | 
[**getVariantById**](CatalogApi.md#getVariantById) | **GET** /catalog/products/{product_id}/variants/{variant_id} | 
[**getVariantMetafieldByProductIdAndVariantId**](CatalogApi.md#getVariantMetafieldByProductIdAndVariantId) | **GET** /catalog/products/{product_id}/variants/{variant_id}/metafields/{metafield_id} | 
[**getVariantMetafieldsByProductIdAndVariantId**](CatalogApi.md#getVariantMetafieldsByProductIdAndVariantId) | **GET** /catalog/products/{product_id}/variants/{variant_id}/metafields | 
[**getVariants**](CatalogApi.md#getVariants) | **GET** /catalog/variants | 
[**getVariantsByProductId**](CatalogApi.md#getVariantsByProductId) | **GET** /catalog/products/{product_id}/variants | 
[**updateBrand**](CatalogApi.md#updateBrand) | **PUT** /catalog/brands/{brand_id} | 
[**updateBrandMetafield**](CatalogApi.md#updateBrandMetafield) | **PUT** /catalog/brands/{brand_id}/metafields/{metafield_id} | 
[**updateBulkPricingRule**](CatalogApi.md#updateBulkPricingRule) | **PUT** /catalog/products/{product_id}/bulk-pricing-rules/{bulk_pricing_rule_id} | 
[**updateCategory**](CatalogApi.md#updateCategory) | **PUT** /catalog/categories/{category_id} | 
[**updateCategoryMetafield**](CatalogApi.md#updateCategoryMetafield) | **PUT** /catalog/categories/{category_id}/metafields/{metafield_id} | 
[**updateComplexRule**](CatalogApi.md#updateComplexRule) | **PUT** /catalog/products/{product_id}/complex-rules/{complex_rule_id} | 
[**updateConfigurableField**](CatalogApi.md#updateConfigurableField) | **PUT** /catalog/products/{product_id}/configurable-fields/{configurable_field_id} | 
[**updateCustomField**](CatalogApi.md#updateCustomField) | **PUT** /catalog/products/{product_id}/custom-fields/{custom_field_id} | 
[**updateModifier**](CatalogApi.md#updateModifier) | **PUT** /catalog/products/{product_id}/modifiers/{modifier_id} | 
[**updateModifierValue**](CatalogApi.md#updateModifierValue) | **PUT** /catalog/products/{product_id}/modifiers/{modifier_id}/values/{value_id} | 
[**updateOption**](CatalogApi.md#updateOption) | **PUT** /catalog/products/{product_id}/options/{option_id} | 
[**updateOptionValue**](CatalogApi.md#updateOptionValue) | **PUT** /catalog/products/{product_id}/options/{option_id}/values/{value_id} | 
[**updateProduct**](CatalogApi.md#updateProduct) | **PUT** /catalog/products/{product_id} | 
[**updateProductImage**](CatalogApi.md#updateProductImage) | **PUT** /catalog/products/{product_id}/images/{image_id} | 
[**updateProductMetafield**](CatalogApi.md#updateProductMetafield) | **PUT** /catalog/products/{product_id}/metafields/{metafield_id} | 
[**updateProductReview**](CatalogApi.md#updateProductReview) | **PUT** /catalog/products/{product_id}/reviews/{review_id} | 
[**updateProductVideo**](CatalogApi.md#updateProductVideo) | **PUT** /catalog/products/{product_id}/videos/{video_id} | 
[**updateVariant**](CatalogApi.md#updateVariant) | **PUT** /catalog/products/{product_id}/variants/{variant_id} | 
[**updateVariantMetafield**](CatalogApi.md#updateVariantMetafield) | **PUT** /catalog/products/{product_id}/variants/{variant_id}/metafields/{metafield_id} | 


# **catalogSummaryGet**
> \BigCommerce\Api\v3\Model\CatalogSummaryResponse catalogSummaryGet()



Returns a lightweight inventory summary from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();

try {
    $result = $api_instance->catalogSummaryGet();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->catalogSummaryGet: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\BigCommerce\Api\v3\Model\CatalogSummaryResponse**](../Model/CatalogSummaryResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createBrand**
> \BigCommerce\Api\v3\Model\BrandResponse createBrand($brand)



Creates a `Brand` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand = new \BigCommerce\Api\v3\Model\BrandPost(); // \BigCommerce\Api\v3\Model\BrandPost | A `Brand` object.

try {
    $result = $api_instance->createBrand($brand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createBrand: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand** | [**\BigCommerce\Api\v3\Model\BrandPost**](../Model/\BigCommerce\Api\v3\Model\BrandPost.md)| A &#x60;Brand&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\BrandResponse**](../Model/BrandResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createBrandImage**
> \BigCommerce\Api\v3\Model\ImageResponse createBrandImage($brand_id, $image_file)



Creates an image on a `Brand`. Publicly accessible URLs and files (form post) are valid parameters.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$image_file = "/path/to/file.txt"; // \SplFileObject | An image file. Supported MIME types include GIF, JPEG, and PNG.

try {
    $result = $api_instance->createBrandImage($brand_id, $image_file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createBrandImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **image_file** | **\SplFileObject**| An image file. Supported MIME types include GIF, JPEG, and PNG. |

### Return type

[**\BigCommerce\Api\v3\Model\ImageResponse**](../Model/ImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: multipart/form-data
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createBrandMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse createBrandMetafield($brand_id, $metafield)



Creates a product `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPost(); // \BigCommerce\Api\v3\Model\MetafieldPost | A `Metafield` object.

try {
    $result = $api_instance->createBrandMetafield($brand_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createBrandMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPost**](../Model/\BigCommerce\Api\v3\Model\MetafieldPost.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createBulkPricingRule**
> \BigCommerce\Api\v3\Model\BulkPricingRuleResponse createBulkPricingRule($product_id, $bulk_pricing_rule, $page, $limit)



Creates a `BulkPricingRule`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$bulk_pricing_rule = new \BigCommerce\Api\v3\Model\BulkPricingRulePost(); // \BigCommerce\Api\v3\Model\BulkPricingRulePost | `BulkPricingRule` object.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->createBulkPricingRule($product_id, $bulk_pricing_rule, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createBulkPricingRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **bulk_pricing_rule** | [**\BigCommerce\Api\v3\Model\BulkPricingRulePost**](../Model/\BigCommerce\Api\v3\Model\BulkPricingRulePost.md)| &#x60;BulkPricingRule&#x60; object. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\BulkPricingRuleResponse**](../Model/BulkPricingRuleResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createCategory**
> \BigCommerce\Api\v3\Model\CategoryResponse createCategory($category)



Creates a `Category` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category = new \BigCommerce\Api\v3\Model\CategoryPost(); // \BigCommerce\Api\v3\Model\CategoryPost | A BigCommerce `Category` object.

try {
    $result = $api_instance->createCategory($category);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createCategory: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category** | [**\BigCommerce\Api\v3\Model\CategoryPost**](../Model/\BigCommerce\Api\v3\Model\CategoryPost.md)| A BigCommerce &#x60;Category&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\CategoryResponse**](../Model/CategoryResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createCategoryImage**
> \BigCommerce\Api\v3\Model\ImageResponse createCategoryImage($category_id, $image_file)



Creates an image on a category. Publicly accessible URLs and files (form post) are valid parameters.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$image_file = "/path/to/file.txt"; // \SplFileObject | An image file. Supported MIME types include GIF, JPEG, and PNG.

try {
    $result = $api_instance->createCategoryImage($category_id, $image_file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createCategoryImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **image_file** | **\SplFileObject**| An image file. Supported MIME types include GIF, JPEG, and PNG. |

### Return type

[**\BigCommerce\Api\v3\Model\ImageResponse**](../Model/ImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: multipart/form-data
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createCategoryMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse createCategoryMetafield($category_id, $metafield)



Creates a product `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPost(); // \BigCommerce\Api\v3\Model\MetafieldPost | A `Metafield` object.

try {
    $result = $api_instance->createCategoryMetafield($category_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createCategoryMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPost**](../Model/\BigCommerce\Api\v3\Model\MetafieldPost.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createComplexRule**
> \BigCommerce\Api\v3\Model\ComplexRuleResponse createComplexRule($product_id, $complex_rule)



Creates a `ComplexRule`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$complex_rule = new \BigCommerce\Api\v3\Model\ComplexRulePost(); // \BigCommerce\Api\v3\Model\ComplexRulePost | `ComplexRule` object.

try {
    $result = $api_instance->createComplexRule($product_id, $complex_rule);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createComplexRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **complex_rule** | [**\BigCommerce\Api\v3\Model\ComplexRulePost**](../Model/\BigCommerce\Api\v3\Model\ComplexRulePost.md)| &#x60;ComplexRule&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ComplexRuleResponse**](../Model/ComplexRuleResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createConfigurableField**
> \BigCommerce\Api\v3\Model\ConfigurableFieldResponse createConfigurableField($product_id, $configurable_field)



Creates a `ConfigurableField`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$configurable_field = new \BigCommerce\Api\v3\Model\ConfigurableFieldPost(); // \BigCommerce\Api\v3\Model\ConfigurableFieldPost | `ConfigurableField` object.

try {
    $result = $api_instance->createConfigurableField($product_id, $configurable_field);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createConfigurableField: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **configurable_field** | [**\BigCommerce\Api\v3\Model\ConfigurableFieldPost**](../Model/\BigCommerce\Api\v3\Model\ConfigurableFieldPost.md)| &#x60;ConfigurableField&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ConfigurableFieldResponse**](../Model/ConfigurableFieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createCustomField**
> \BigCommerce\Api\v3\Model\CustomFieldResponse createCustomField($product_id, $custom_field)



Creates a `CustomField`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$custom_field = new \BigCommerce\Api\v3\Model\CustomFieldPost(); // \BigCommerce\Api\v3\Model\CustomFieldPost | `CustomField` object.

try {
    $result = $api_instance->createCustomField($product_id, $custom_field);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createCustomField: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **custom_field** | [**\BigCommerce\Api\v3\Model\CustomFieldPost**](../Model/\BigCommerce\Api\v3\Model\CustomFieldPost.md)| &#x60;CustomField&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\CustomFieldResponse**](../Model/CustomFieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createModifier**
> \BigCommerce\Api\v3\Model\ModifierResponse createModifier($product_id, $modifier)



Creates a `Modifier`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier = new \BigCommerce\Api\v3\Model\ModifierPost(); // \BigCommerce\Api\v3\Model\ModifierPost | A `Modifier` object.

try {
    $result = $api_instance->createModifier($product_id, $modifier);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createModifier: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier** | [**\BigCommerce\Api\v3\Model\ModifierPost**](../Model/\BigCommerce\Api\v3\Model\ModifierPost.md)| A &#x60;Modifier&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ModifierResponse**](../Model/ModifierResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createModifierImage**
> \BigCommerce\Api\v3\Model\ImageResponse createModifierImage($product_id, $modifier_id, $value_id, $image_file)



Adds an image to a modifier value; the image will show on the storefront when the value is selected.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$value_id = 56; // int | The ID of the `Modifier`.
$image_file = "/path/to/file.txt"; // \SplFileObject | An image file. Supported MIME types include GIF, JPEG, and PNG.

try {
    $result = $api_instance->createModifierImage($product_id, $modifier_id, $value_id, $image_file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createModifierImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **image_file** | **\SplFileObject**| An image file. Supported MIME types include GIF, JPEG, and PNG. |

### Return type

[**\BigCommerce\Api\v3\Model\ImageResponse**](../Model/ImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: multipart/form-data
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createModifierValue**
> \BigCommerce\Api\v3\Model\ModifierValueResponse createModifierValue($product_id, $modifier_id, $modifier_value)



Creates a `ModifierValue`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$modifier_value = new \BigCommerce\Api\v3\Model\ModifierValuePost(); // \BigCommerce\Api\v3\Model\ModifierValuePost | A `ModifierValue` object.

try {
    $result = $api_instance->createModifierValue($product_id, $modifier_id, $modifier_value);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createModifierValue: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **modifier_value** | [**\BigCommerce\Api\v3\Model\ModifierValuePost**](../Model/\BigCommerce\Api\v3\Model\ModifierValuePost.md)| A &#x60;ModifierValue&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ModifierValueResponse**](../Model/ModifierValueResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createOption**
> \BigCommerce\Api\v3\Model\OptionResponse createOption($product_id, $option)



Creates an `Option`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option = new \BigCommerce\Api\v3\Model\OptionPost(); // \BigCommerce\Api\v3\Model\OptionPost | An `Option` object.

try {
    $result = $api_instance->createOption($product_id, $option);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createOption: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option** | [**\BigCommerce\Api\v3\Model\OptionPost**](../Model/\BigCommerce\Api\v3\Model\OptionPost.md)| An &#x60;Option&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\OptionResponse**](../Model/OptionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createOptionValue**
> \BigCommerce\Api\v3\Model\OptionValueResponse createOptionValue($product_id, $option_id, $option_value)



Creates a `OptionValue`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$option_value = new \BigCommerce\Api\v3\Model\OptionValuePost(); // \BigCommerce\Api\v3\Model\OptionValuePost | A `OptionValue` object.

try {
    $result = $api_instance->createOptionValue($product_id, $option_id, $option_value);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createOptionValue: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **option_value** | [**\BigCommerce\Api\v3\Model\OptionValuePost**](../Model/\BigCommerce\Api\v3\Model\OptionValuePost.md)| A &#x60;OptionValue&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\OptionValueResponse**](../Model/OptionValueResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createProduct**
> \BigCommerce\Api\v3\Model\ProductResponse createProduct($product)



Creates a `Product` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product = new \BigCommerce\Api\v3\Model\ProductPost(); // \BigCommerce\Api\v3\Model\ProductPost | A BigCommerce `Product` object.

try {
    $result = $api_instance->createProduct($product);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createProduct: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product** | [**\BigCommerce\Api\v3\Model\ProductPost**](../Model/\BigCommerce\Api\v3\Model\ProductPost.md)| A BigCommerce &#x60;Product&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductResponse**](../Model/ProductResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createProductImage**
> \BigCommerce\Api\v3\Model\ProductImageResponse createProductImage($product_id, $product_image)



Creates an image on a product. Publicly accessible URLs and files (form post) are valid parameters.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$product_image = new \BigCommerce\Api\v3\Model\ProductImagePost(); // \BigCommerce\Api\v3\Model\ProductImagePost | A BigCommerce `ProductImage` object.

try {
    $result = $api_instance->createProductImage($product_id, $product_image);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createProductImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **product_image** | [**\BigCommerce\Api\v3\Model\ProductImagePost**](../Model/\BigCommerce\Api\v3\Model\ProductImagePost.md)| A BigCommerce &#x60;ProductImage&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductImageResponse**](../Model/ProductImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createProductMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse createProductMetafield($product_id, $metafield)



Creates a product `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPost(); // \BigCommerce\Api\v3\Model\MetafieldPost | A `Metafield` object.

try {
    $result = $api_instance->createProductMetafield($product_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createProductMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPost**](../Model/\BigCommerce\Api\v3\Model\MetafieldPost.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createProductReview**
> \BigCommerce\Api\v3\Model\ProductReviewResponse createProductReview($product_id, $product_review)



Creates a product review.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$product_review = new \BigCommerce\Api\v3\Model\ProductReviewPost(); // \BigCommerce\Api\v3\Model\ProductReviewPost | A BigCommerce `ProductReview` object.

try {
    $result = $api_instance->createProductReview($product_id, $product_review);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createProductReview: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **product_review** | [**\BigCommerce\Api\v3\Model\ProductReviewPost**](../Model/\BigCommerce\Api\v3\Model\ProductReviewPost.md)| A BigCommerce &#x60;ProductReview&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductReviewResponse**](../Model/ProductReviewResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createProductVideo**
> \BigCommerce\Api\v3\Model\ProductVideoResponse createProductVideo($product_id, $product_video)



Creates a video on a product, using a video ID from a host site.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$product_video = new \BigCommerce\Api\v3\Model\ProductVideoPost(); // \BigCommerce\Api\v3\Model\ProductVideoPost | A BigCommerce `ProductVideo` object.

try {
    $result = $api_instance->createProductVideo($product_id, $product_video);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createProductVideo: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **product_video** | [**\BigCommerce\Api\v3\Model\ProductVideoPost**](../Model/\BigCommerce\Api\v3\Model\ProductVideoPost.md)| A BigCommerce &#x60;ProductVideo&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductVideoResponse**](../Model/ProductVideoResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createVariant**
> \BigCommerce\Api\v3\Model\VariantResponse createVariant($product_id, $variant)



Creates a `Variant` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant = new \BigCommerce\Api\v3\Model\VariantPost(); // \BigCommerce\Api\v3\Model\VariantPost | `Variant` object.

try {
    $result = $api_instance->createVariant($product_id, $variant);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createVariant: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant** | [**\BigCommerce\Api\v3\Model\VariantPost**](../Model/\BigCommerce\Api\v3\Model\VariantPost.md)| &#x60;Variant&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\VariantResponse**](../Model/VariantResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createVariantImage**
> \BigCommerce\Api\v3\Model\ImageResponse createVariantImage($product_id, $variant_id, $image_file)



### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$image_file = "/path/to/file.txt"; // \SplFileObject | An image file. Supported MIME types include GIF, JPEG, and PNG.

try {
    $result = $api_instance->createVariantImage($product_id, $variant_id, $image_file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createVariantImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **image_file** | **\SplFileObject**| An image file. Supported MIME types include GIF, JPEG, and PNG. |

### Return type

[**\BigCommerce\Api\v3\Model\ImageResponse**](../Model/ImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: multipart/form-data
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **createVariantMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse createVariantMetafield($product_id, $variant_id, $metafield)



Creates a variant `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPost(); // \BigCommerce\Api\v3\Model\MetafieldPost | A `Metafield` object.

try {
    $result = $api_instance->createVariantMetafield($product_id, $variant_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->createVariantMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPost**](../Model/\BigCommerce\Api\v3\Model\MetafieldPost.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteBrandById**
> deleteBrandById($brand_id)



Deletes a `Brand` from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.

try {
    $api_instance->deleteBrandById($brand_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteBrandById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteBrandImage**
> deleteBrandImage($brand_id)



Deletes a `Brand` image from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.

try {
    $api_instance->deleteBrandImage($brand_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteBrandImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteBrandMetafieldById**
> deleteBrandMetafieldById($metafield_id, $brand_id)



Deletes a `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.

try {
    $api_instance->deleteBrandMetafieldById($metafield_id, $brand_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteBrandMetafieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteBrands**
> deleteBrands($name, $page_title)



Deletes one or more `Brand` objects from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$name = "name_example"; // string | Filter items by name.
$page_title = "page_title_example"; // string | Filter items by page_title.

try {
    $api_instance->deleteBrands($name, $page_title);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteBrands: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Filter items by name. | [optional]
 **page_title** | **string**| Filter items by page_title. | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteBulkPricingRuleById**
> deleteBulkPricingRuleById($product_id, $bulk_pricing_rule_id)



Deletes a Product's `BulkPricingRule`, based on the `product_id` and `bulk_pricing_rule_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$bulk_pricing_rule_id = 56; // int | The ID of the `BulkPricingRule`.

try {
    $api_instance->deleteBulkPricingRuleById($product_id, $bulk_pricing_rule_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteBulkPricingRuleById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **bulk_pricing_rule_id** | **int**| The ID of the &#x60;BulkPricingRule&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteCategories**
> deleteCategories($name, $parent_id, $page_title, $keyword, $is_visible)



Deletes one or more `Category` objects from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$name = "name_example"; // string | Filter items by name.
$parent_id = 56; // int | Filter items by parent_id.
$page_title = "page_title_example"; // string | Filter items by page_title.
$keyword = "keyword_example"; // string | Filter items by keywords.
$is_visible = 56; // int | Filter items by is_visible.

try {
    $api_instance->deleteCategories($name, $parent_id, $page_title, $keyword, $is_visible);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteCategories: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Filter items by name. | [optional]
 **parent_id** | **int**| Filter items by parent_id. | [optional]
 **page_title** | **string**| Filter items by page_title. | [optional]
 **keyword** | **string**| Filter items by keywords. | [optional]
 **is_visible** | **int**| Filter items by is_visible. | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteCategoryById**
> deleteCategoryById($category_id)



Deletes one or more `Category` objects from the BigCommerce catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.

try {
    $api_instance->deleteCategoryById($category_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteCategoryById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteCategoryImage**
> deleteCategoryImage($category_id)



Deletes a `Category` image from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.

try {
    $api_instance->deleteCategoryImage($category_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteCategoryImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteCategoryMetafieldById**
> deleteCategoryMetafieldById($metafield_id, $category_id)



Deletes a `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.

try {
    $api_instance->deleteCategoryMetafieldById($metafield_id, $category_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteCategoryMetafieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteComplexRuleById**
> deleteComplexRuleById($product_id, $complex_rule_id)



Deletes a Product's `ComplexRule`, based on the `product_id` and `complex_rule_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$complex_rule_id = 56; // int | The ID of the `ComplexRule`.

try {
    $api_instance->deleteComplexRuleById($product_id, $complex_rule_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteComplexRuleById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **complex_rule_id** | **int**| The ID of the &#x60;ComplexRule&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteConfigurableFieldById**
> deleteConfigurableFieldById($product_id, $configurable_field_id)



Deletes a Product's `ConfigurableField`, based on the `product_id` and `configurable_field_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$configurable_field_id = 56; // int | The ID of the `ConfigurableField`.

try {
    $api_instance->deleteConfigurableFieldById($product_id, $configurable_field_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteConfigurableFieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **configurable_field_id** | **int**| The ID of the &#x60;ConfigurableField&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteCustomFieldById**
> deleteCustomFieldById($product_id, $custom_field_id)



Deletes a Product's `CustomField`, based on the `product_id` and `custom_field_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$custom_field_id = 56; // int | The ID of the `CustomField`.

try {
    $api_instance->deleteCustomFieldById($product_id, $custom_field_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteCustomFieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **custom_field_id** | **int**| The ID of the &#x60;CustomField&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteModifierById**
> deleteModifierById($product_id, $modifier_id)



Deletes a Product's `Modifier` based on the `product_id` and `modifier_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.

try {
    $api_instance->deleteModifierById($product_id, $modifier_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteModifierById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteModifierImage**
> deleteModifierImage($product_id, $modifier_id, $value_id)



Deletes the image that was set to show when the modifier value is selected.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$value_id = 56; // int | The ID of the `Modifier`.

try {
    $api_instance->deleteModifierImage($product_id, $modifier_id, $value_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteModifierImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteModifierValueById**
> deleteModifierValueById($product_id, $modifier_id, $value_id)



Deletes a Product's `ModifierValue` based on the `product_id`, `modifier_id`, and `value_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$value_id = 56; // int | The ID of the `Modifier/Option Value`.

try {
    $api_instance->deleteModifierValueById($product_id, $modifier_id, $value_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteModifierValueById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier/Option Value&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteOptionById**
> deleteOptionById($product_id, $option_id)



Deletes a Product's `Option`, based on the `product_id` and `option_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.

try {
    $api_instance->deleteOptionById($product_id, $option_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteOptionById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteOptionValueById**
> deleteOptionValueById($product_id, $option_id, $value_id)



Deletes a Product's `OptionValue` based on the `product_id`, `option_id`, and `value_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$value_id = 56; // int | The ID of the `Modifier/Option Value`.

try {
    $api_instance->deleteOptionValueById($product_id, $option_id, $value_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteOptionValueById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier/Option Value&#x60;. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteProductById**
> deleteProductById($product_id)



Deletes a `Product` object from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.

try {
    $api_instance->deleteProductById($product_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteProductById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteProductImage**
> deleteProductImage($product_id, $image_id)



Deletes a `ProductImage` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$image_id = 56; // int | The ID of the `Image` that is being operated on.

try {
    $api_instance->deleteProductImage($product_id, $image_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteProductImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **image_id** | **int**| The ID of the &#x60;Image&#x60; that is being operated on. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteProductMetafieldById**
> deleteProductMetafieldById($metafield_id, $product_id)



Deletes a `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.

try {
    $api_instance->deleteProductMetafieldById($metafield_id, $product_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteProductMetafieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteProductReview**
> deleteProductReview($product_id, $review_id)



Deletes a `ProductReview` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$review_id = 56; // int | The ID of the `review` that is being operated on.

try {
    $api_instance->deleteProductReview($product_id, $review_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteProductReview: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **review_id** | **int**| The ID of the &#x60;review&#x60; that is being operated on. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteProductVideo**
> deleteProductVideo($product_id, $video_id)



Deletes a `ProductVideo` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$video_id = "video_id_example"; // string | The ID of the `Video` that is being operated on.

try {
    $api_instance->deleteProductVideo($product_id, $video_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteProductVideo: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **video_id** | **string**| The ID of the &#x60;Video&#x60; that is being operated on. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteProducts**
> deleteProducts($name, $sku, $price, $weight, $condition, $brand_id, $date_modified, $date_last_imported, $is_visible, $is_featured, $inventory_level, $total_sold, $type, $categories, $keyword)



Deletes one or more `Product` objects from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$name = "name_example"; // string | Filter items by name.
$sku = "sku_example"; // string | Filter items by sku.
$price = 3.4; // float | Filter items by price.
$weight = 3.4; // float | Filter items by weight.
$condition = 56; // int | Filter items by condition.
$brand_id = 56; // int | Filter items by brand_id.
$date_modified = new \DateTime(); // \DateTime | Filter items by date_modified.
$date_last_imported = new \DateTime(); // \DateTime | Filter items by date_last_imported.
$is_visible = 56; // int | Filter items by is_visible.
$is_featured = 56; // int | Filter items by is_featured.
$inventory_level = 56; // int | Filter items by inventory_level.
$total_sold = 56; // int | Filter items by total_sold.
$type = "type_example"; // string | Filter items by type: `physical` or `digital`.
$categories = 56; // int | Filter items by categories.
$keyword = "keyword_example"; // string | Filter items by keywords found in the `name`, `description`, or `sku` fields, or in the brand name.

try {
    $api_instance->deleteProducts($name, $sku, $price, $weight, $condition, $brand_id, $date_modified, $date_last_imported, $is_visible, $is_featured, $inventory_level, $total_sold, $type, $categories, $keyword);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteProducts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Filter items by name. | [optional]
 **sku** | **string**| Filter items by sku. | [optional]
 **price** | **float**| Filter items by price. | [optional]
 **weight** | **float**| Filter items by weight. | [optional]
 **condition** | **int**| Filter items by condition. | [optional]
 **brand_id** | **int**| Filter items by brand_id. | [optional]
 **date_modified** | **\DateTime**| Filter items by date_modified. | [optional]
 **date_last_imported** | **\DateTime**| Filter items by date_last_imported. | [optional]
 **is_visible** | **int**| Filter items by is_visible. | [optional]
 **is_featured** | **int**| Filter items by is_featured. | [optional]
 **inventory_level** | **int**| Filter items by inventory_level. | [optional]
 **total_sold** | **int**| Filter items by total_sold. | [optional]
 **type** | **string**| Filter items by type: &#x60;physical&#x60; or &#x60;digital&#x60;. | [optional]
 **categories** | **int**| Filter items by categories. | [optional]
 **keyword** | **string**| Filter items by keywords found in the &#x60;name&#x60;, &#x60;description&#x60;, or &#x60;sku&#x60; fields, or in the brand name. | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteVariantById**
> deleteVariantById($product_id, $variant_id)



Deletes a `Variant`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.

try {
    $api_instance->deleteVariantById($product_id, $variant_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteVariantById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **deleteVariantMetafieldById**
> deleteVariantMetafieldById($metafield_id, $product_id, $variant_id)



Deletes a `Metafield`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.

try {
    $api_instance->deleteVariantMetafieldById($metafield_id, $product_id, $variant_id);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->deleteVariantMetafieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBrandById**
> \BigCommerce\Api\v3\Model\BrandResponse getBrandById($brand_id, $include_fields, $exclude_fields)



Gets a `Brand` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getBrandById($brand_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getBrandById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\BrandResponse**](../Model/BrandResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBrandMetafieldByBrandId**
> \BigCommerce\Api\v3\Model\MetafieldResponse getBrandMetafieldByBrandId($metafield_id, $brand_id, $include_fields, $exclude_fields)



Gets a `Metafield`, by `category_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getBrandMetafieldByBrandId($metafield_id, $brand_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getBrandMetafieldByBrandId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBrandMetafieldsByBrandId**
> \BigCommerce\Api\v3\Model\MetaFieldCollectionResponse getBrandMetafieldsByBrandId($brand_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields)



Gets a `Metafield` object list, by `brand_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$key = "key_example"; // string | Filter based on a metafield's key.
$namespace = "namespace_example"; // string | Filter based on a metafield's key.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getBrandMetafieldsByBrandId($brand_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getBrandMetafieldsByBrandId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **key** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **namespace** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetaFieldCollectionResponse**](../Model/MetaFieldCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBrands**
> \BigCommerce\Api\v3\Model\BrandCollectionResponse getBrands($name, $page_title, $page, $limit, $include_fields, $exclude_fields)



Gets `Brand` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$name = "name_example"; // string | Filter items by name.
$page_title = "page_title_example"; // string | Filter items by page_title.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getBrands($name, $page_title, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getBrands: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Filter items by name. | [optional]
 **page_title** | **string**| Filter items by page_title. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\BrandCollectionResponse**](../Model/BrandCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBulkPricingRuleById**
> \BigCommerce\Api\v3\Model\BulkPricingRuleResponse getBulkPricingRuleById($product_id, $bulk_pricing_rule_id, $include_fields, $exclude_fields)



Gets a `BulkPricingRule` by `product_id` and `bulk_pricing_rule_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$bulk_pricing_rule_id = 56; // int | The ID of the `BulkPricingRule`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getBulkPricingRuleById($product_id, $bulk_pricing_rule_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getBulkPricingRuleById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **bulk_pricing_rule_id** | **int**| The ID of the &#x60;BulkPricingRule&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\BulkPricingRuleResponse**](../Model/BulkPricingRuleResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getBulkPricingRules**
> \BigCommerce\Api\v3\Model\BulkPricingRuleCollectionResponse getBulkPricingRules($product_id, $page, $limit, $include_fields, $exclude_fields)



Gets an array of `BulkPricingRule` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getBulkPricingRules($product_id, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getBulkPricingRules: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\BulkPricingRuleCollectionResponse**](../Model/BulkPricingRuleCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCategories**
> \BigCommerce\Api\v3\Model\CategoryCollectionResponse getCategories($name, $parent_id, $page_title, $keyword, $is_visible, $page, $limit, $include_fields, $exclude_fields)



Returns a paginated categories collection from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$name = "name_example"; // string | Filter items by name.
$parent_id = 56; // int | Filter items by parent_id.
$page_title = "page_title_example"; // string | Filter items by page_title.
$keyword = "keyword_example"; // string | Filter items by keywords.
$is_visible = 56; // int | Filter items by is_visible.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getCategories($name, $parent_id, $page_title, $keyword, $is_visible, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCategories: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Filter items by name. | [optional]
 **parent_id** | **int**| Filter items by parent_id. | [optional]
 **page_title** | **string**| Filter items by page_title. | [optional]
 **keyword** | **string**| Filter items by keywords. | [optional]
 **is_visible** | **int**| Filter items by is_visible. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\CategoryCollectionResponse**](../Model/CategoryCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCategoryById**
> \BigCommerce\Api\v3\Model\CategoryResponse getCategoryById($category_id, $include_fields, $exclude_fields)



Returns a `Category` from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getCategoryById($category_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCategoryById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\CategoryResponse**](../Model/CategoryResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCategoryMetafieldByCategoryId**
> \BigCommerce\Api\v3\Model\MetafieldResponse getCategoryMetafieldByCategoryId($metafield_id, $category_id, $include_fields, $exclude_fields)



Gets a `Metafield` by category_id.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getCategoryMetafieldByCategoryId($metafield_id, $category_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCategoryMetafieldByCategoryId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCategoryMetafieldsByCategoryId**
> \BigCommerce\Api\v3\Model\MetaFieldCollectionResponse getCategoryMetafieldsByCategoryId($category_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields)



Gets a `Metafield` object list, by category_id.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$key = "key_example"; // string | Filter based on a metafield's key.
$namespace = "namespace_example"; // string | Filter based on a metafield's key.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getCategoryMetafieldsByCategoryId($category_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCategoryMetafieldsByCategoryId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **key** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **namespace** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetaFieldCollectionResponse**](../Model/MetaFieldCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCategoryTree**
> \BigCommerce\Api\v3\Model\CategoryTreeCollectionResponse getCategoryTree()



Returns the categories tree, a nested lineage of the categories with parent->child relationship. The `Category` objects returned are simplified versions of the category objects returned in the rest of this API.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();

try {
    $result = $api_instance->getCategoryTree();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCategoryTree: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**\BigCommerce\Api\v3\Model\CategoryTreeCollectionResponse**](../Model/CategoryTreeCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getComplexRuleById**
> \BigCommerce\Api\v3\Model\ComplexRuleResponse getComplexRuleById($product_id, $complex_rule_id, $include_fields, $exclude_fields)



Gets a `ComplexRule` by product_id.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$complex_rule_id = 56; // int | The ID of the `ComplexRule`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getComplexRuleById($product_id, $complex_rule_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getComplexRuleById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **complex_rule_id** | **int**| The ID of the &#x60;ComplexRule&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ComplexRuleResponse**](../Model/ComplexRuleResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getComplexRules**
> \BigCommerce\Api\v3\Model\ComplexRuleCollectionResponse getComplexRules($product_id, $include_fields, $exclude_fields)



Gets an array of `ComplexRule` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getComplexRules($product_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getComplexRules: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ComplexRuleCollectionResponse**](../Model/ComplexRuleCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getConfigurableFieldById**
> \BigCommerce\Api\v3\Model\ConfigurableFieldResponse getConfigurableFieldById($product_id, $configurable_field_id, $include_fields, $exclude_fields)



Gets a `ConfigurableField` by `product_id` and `configurable_field_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$configurable_field_id = 56; // int | The ID of the `ConfigurableField`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getConfigurableFieldById($product_id, $configurable_field_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getConfigurableFieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **configurable_field_id** | **int**| The ID of the &#x60;ConfigurableField&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ConfigurableFieldResponse**](../Model/ConfigurableFieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getConfigurableFields**
> \BigCommerce\Api\v3\Model\ConfigurableFieldCollectionResponse getConfigurableFields($product_id, $include_fields, $exclude_fields, $page, $limit)



Gets an array of `ConfigurableField` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->getConfigurableFields($product_id, $include_fields, $exclude_fields, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getConfigurableFields: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ConfigurableFieldCollectionResponse**](../Model/ConfigurableFieldCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCustomFieldById**
> \BigCommerce\Api\v3\Model\CustomFieldResponse getCustomFieldById($product_id, $custom_field_id, $include_fields, $exclude_fields)



Gets a `CustomField` by `product_id` and `custom_field_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$custom_field_id = 56; // int | The ID of the `CustomField`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getCustomFieldById($product_id, $custom_field_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCustomFieldById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **custom_field_id** | **int**| The ID of the &#x60;CustomField&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\CustomFieldResponse**](../Model/CustomFieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCustomFields**
> \BigCommerce\Api\v3\Model\CustomFieldCollectionResponse getCustomFields($product_id, $include_fields, $exclude_fields, $page, $limit)



Gets an array of `CustomField` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->getCustomFields($product_id, $include_fields, $exclude_fields, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getCustomFields: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\CustomFieldCollectionResponse**](../Model/CustomFieldCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getModifierById**
> \BigCommerce\Api\v3\Model\ModifierResponse getModifierById($product_id, $modifier_id, $include_fields, $exclude_fields)



Gets a `Modifier` by product_id and modifier_id.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getModifierById($product_id, $modifier_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getModifierById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ModifierResponse**](../Model/ModifierResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getModifierValueById**
> \BigCommerce\Api\v3\Model\ModifierValueResponse getModifierValueById($product_id, $modifier_id, $value_id, $include_fields, $exclude_fields)



Gets a `ModifierValue` by `product_id`, `modifier_id`, and `value_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$value_id = 56; // int | The ID of the `Modifier/Option Value`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getModifierValueById($product_id, $modifier_id, $value_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getModifierValueById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier/Option Value&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ModifierValueResponse**](../Model/ModifierValueResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getModifierValues**
> \BigCommerce\Api\v3\Model\ModifierValueCollectionResponse getModifierValues($product_id, $modifier_id, $include_fields, $exclude_fields)



Gets an array of `ModifierValue` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getModifierValues($product_id, $modifier_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getModifierValues: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ModifierValueCollectionResponse**](../Model/ModifierValueCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getModifiers**
> \BigCommerce\Api\v3\Model\ModifierCollectionResponse getModifiers($product_id, $page, $limit, $include_fields, $exclude_fields)



Gets an array of `Modifier` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getModifiers($product_id, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getModifiers: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ModifierCollectionResponse**](../Model/ModifierCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getOptionById**
> \BigCommerce\Api\v3\Model\OptionResponse getOptionById($product_id, $option_id, $include_fields, $exclude_fields)



Gets `Option` object by product ID and option ID.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getOptionById($product_id, $option_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getOptionById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\OptionResponse**](../Model/OptionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getOptionValueById**
> \BigCommerce\Api\v3\Model\OptionValueResponse getOptionValueById($product_id, $option_id, $value_id, $include_fields, $exclude_fields)



Gets a `OptionValue` by `product_id`, `option_id`, and `value_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$value_id = 56; // int | The ID of the `Modifier/Option Value`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getOptionValueById($product_id, $option_id, $value_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getOptionValueById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier/Option Value&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\OptionValueResponse**](../Model/OptionValueResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getOptionValues**
> \BigCommerce\Api\v3\Model\OptionValueCollectionResponse getOptionValues($product_id, $option_id, $include_fields, $exclude_fields)



Gets an array of `OptionValue` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getOptionValues($product_id, $option_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getOptionValues: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\OptionValueCollectionResponse**](../Model/OptionValueCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getOptions**
> \BigCommerce\Api\v3\Model\OptionCollectionResponse getOptions($product_id, $page, $limit, $include_fields, $exclude_fields)



Gets an array of `Option` objects.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getOptions($product_id, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getOptions: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\OptionCollectionResponse**](../Model/OptionCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductById**
> \BigCommerce\Api\v3\Model\ProductResponse getProductById($product_id, $include, $include_fields, $exclude_fields, $price_list_id)



Returns a `Product` from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include = "include_example"; // string | Sub-resources to include on a product, in a comma-separated list. Valid expansions currently include `variants`, `images`, 'primary_image`, `custom_fields`, and `bulk_pricing_rules`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.
$price_list_id = 56; // int | The ID of the `Price List`.

try {
    $result = $api_instance->getProductById($product_id, $include, $include_fields, $exclude_fields, $price_list_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include** | **string**| Sub-resources to include on a product, in a comma-separated list. Valid expansions currently include &#x60;variants&#x60;, &#x60;images&#x60;, &#39;primary_image&#x60;, &#x60;custom_fields&#x60;, and &#x60;bulk_pricing_rules&#x60;. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60;. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductResponse**](../Model/ProductResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductImageById**
> \BigCommerce\Api\v3\Model\ProductImageResponse getProductImageById($product_id, $image_id, $include_fields, $exclude_fields)



Gets image on a product.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$image_id = 56; // int | The ID of the `Image` that is being operated on.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductImageById($product_id, $image_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductImageById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **image_id** | **int**| The ID of the &#x60;Image&#x60; that is being operated on. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductImageResponse**](../Model/ProductImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductImages**
> \BigCommerce\Api\v3\Model\ProductImageCollectionResponse getProductImages($product_id, $page, $limit, $include_fields, $exclude_fields)



Gets all images on a product.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductImages($product_id, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductImages: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductImageCollectionResponse**](../Model/ProductImageCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductMetafieldByProductId**
> \BigCommerce\Api\v3\Model\MetafieldResponse getProductMetafieldByProductId($metafield_id, $product_id, $include_fields, $exclude_fields)



Gets a `Metafield`, by `product_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductMetafieldByProductId($metafield_id, $product_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductMetafieldByProductId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductMetafieldsByProductId**
> \BigCommerce\Api\v3\Model\MetaFieldCollectionResponse getProductMetafieldsByProductId($product_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields)



Gets a `Metafield` object list, by `product_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$key = "key_example"; // string | Filter based on a metafield's key.
$namespace = "namespace_example"; // string | Filter based on a metafield's key.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductMetafieldsByProductId($product_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductMetafieldsByProductId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **key** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **namespace** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetaFieldCollectionResponse**](../Model/MetaFieldCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductReviewById**
> \BigCommerce\Api\v3\Model\ProductReviewResponse getProductReviewById($product_id, $review_id, $include_fields, $exclude_fields)



Gets a product review.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$review_id = 56; // int | The ID of the `review` that is being operated on.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductReviewById($product_id, $review_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductReviewById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **review_id** | **int**| The ID of the &#x60;review&#x60; that is being operated on. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductReviewResponse**](../Model/ProductReviewResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductReviews**
> \BigCommerce\Api\v3\Model\ProductReviewCollectionResponse getProductReviews($product_id, $include_fields, $exclude_fields, $page, $limit)



Gets all reviews on a product.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.

try {
    $result = $api_instance->getProductReviews($product_id, $include_fields, $exclude_fields, $page, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductReviews: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductReviewCollectionResponse**](../Model/ProductReviewCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductVideoById**
> \BigCommerce\Api\v3\Model\ProductVideoResponse getProductVideoById($product_id, $video_id, $include_fields, $exclude_fields)



Gets video on a product.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$video_id = "video_id_example"; // string | The ID of the `Video` that is being operated on.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductVideoById($product_id, $video_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductVideoById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **video_id** | **string**| The ID of the &#x60;Video&#x60; that is being operated on. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductVideoResponse**](../Model/ProductVideoResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProductVideos**
> \BigCommerce\Api\v3\Model\ProductVideoCollectionResponse getProductVideos($product_id, $include_fields, $exclude_fields)



Gets all videos on a product.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getProductVideos($product_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProductVideos: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductVideoCollectionResponse**](../Model/ProductVideoCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getProducts**
> \BigCommerce\Api\v3\Model\ProductCollectionResponse getProducts($id, $name, $sku, $upc, $price, $weight, $condition, $brand_id, $date_modified, $date_last_imported, $is_visible, $is_featured, $is_free_shipping, $inventory_level, $inventory_low, $out_of_stock, $total_sold, $type, $categories, $keyword, $keyword_context, $status, $include, $include_fields, $exclude_fields, $availability, $price_list_id, $page, $limit, $direction, $sort)



Returns a paginated collection of `Products` objects from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$id = 56; // int | Filter items by id.
$name = "name_example"; // string | Filter items by name.
$sku = "sku_example"; // string | Filter items by sku.
$upc = "upc_example"; // string | Filter items by upc.
$price = 3.4; // float | Filter items by price.
$weight = 3.4; // float | Filter items by weight.
$condition = 56; // int | Filter items by condition.
$brand_id = 56; // int | Filter items by brand_id.
$date_modified = new \DateTime(); // \DateTime | Filter items by date_modified.
$date_last_imported = new \DateTime(); // \DateTime | Filter items by date_last_imported.
$is_visible = 56; // int | Filter items by is_visible.
$is_featured = 56; // int | Filter items by is_featured.
$is_free_shipping = 56; // int | Filter items by is_free_shipping.
$inventory_level = 56; // int | Filter items by inventory_level.
$inventory_low = 56; // int | Filter items by inventory_low. Values: 1, 0.
$out_of_stock = 56; // int | Filter items by out_of_stock. To enable the filter, pass `out_of_stock`=`1`.
$total_sold = 56; // int | Filter items by total_sold.
$type = "type_example"; // string | Filter items by type: `physical` or `digital`.
$categories = 56; // int | Filter items by categories.
$keyword = "keyword_example"; // string | Filter items by keywords found in the `name`, `description`, or `sku` fields, or in the brand name.
$keyword_context = "keyword_context_example"; // string | Set context for a product search.
$status = 56; // int | Filter items by status.
$include = "include_example"; // string | Sub-resources to include on a product, in a comma-separated list. Valid expansions currently include `variants`, `images`, 'primary_image`, `custom_fields`, and `bulk_pricing_rules`.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.
$availability = "availability_example"; // string | Filter items by availability. Values are: available, disabled, preorder.
$price_list_id = 56; // int | The ID of the `Price List`.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$direction = "direction_example"; // string | Sort direction. Acceptable values are: `asc`, `desc`.
$sort = "sort_example"; // string | Field name to sort by.

try {
    $result = $api_instance->getProducts($id, $name, $sku, $upc, $price, $weight, $condition, $brand_id, $date_modified, $date_last_imported, $is_visible, $is_featured, $is_free_shipping, $inventory_level, $inventory_low, $out_of_stock, $total_sold, $type, $categories, $keyword, $keyword_context, $status, $include, $include_fields, $exclude_fields, $availability, $price_list_id, $page, $limit, $direction, $sort);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getProducts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Filter items by id. | [optional]
 **name** | **string**| Filter items by name. | [optional]
 **sku** | **string**| Filter items by sku. | [optional]
 **upc** | **string**| Filter items by upc. | [optional]
 **price** | **float**| Filter items by price. | [optional]
 **weight** | **float**| Filter items by weight. | [optional]
 **condition** | **int**| Filter items by condition. | [optional]
 **brand_id** | **int**| Filter items by brand_id. | [optional]
 **date_modified** | **\DateTime**| Filter items by date_modified. | [optional]
 **date_last_imported** | **\DateTime**| Filter items by date_last_imported. | [optional]
 **is_visible** | **int**| Filter items by is_visible. | [optional]
 **is_featured** | **int**| Filter items by is_featured. | [optional]
 **is_free_shipping** | **int**| Filter items by is_free_shipping. | [optional]
 **inventory_level** | **int**| Filter items by inventory_level. | [optional]
 **inventory_low** | **int**| Filter items by inventory_low. Values: 1, 0. | [optional]
 **out_of_stock** | **int**| Filter items by out_of_stock. To enable the filter, pass &#x60;out_of_stock&#x60;&#x3D;&#x60;1&#x60;. | [optional]
 **total_sold** | **int**| Filter items by total_sold. | [optional]
 **type** | **string**| Filter items by type: &#x60;physical&#x60; or &#x60;digital&#x60;. | [optional]
 **categories** | **int**| Filter items by categories. | [optional]
 **keyword** | **string**| Filter items by keywords found in the &#x60;name&#x60;, &#x60;description&#x60;, or &#x60;sku&#x60; fields, or in the brand name. | [optional]
 **keyword_context** | **string**| Set context for a product search. | [optional]
 **status** | **int**| Filter items by status. | [optional]
 **include** | **string**| Sub-resources to include on a product, in a comma-separated list. Valid expansions currently include &#x60;variants&#x60;, &#x60;images&#x60;, &#39;primary_image&#x60;, &#x60;custom_fields&#x60;, and &#x60;bulk_pricing_rules&#x60;. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]
 **availability** | **string**| Filter items by availability. Values are: available, disabled, preorder. | [optional]
 **price_list_id** | **int**| The ID of the &#x60;Price List&#x60;. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **direction** | **string**| Sort direction. Acceptable values are: &#x60;asc&#x60;, &#x60;desc&#x60;. | [optional]
 **sort** | **string**| Field name to sort by. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\ProductCollectionResponse**](../Model/ProductCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getVariantById**
> \BigCommerce\Api\v3\Model\VariantResponse getVariantById($product_id, $variant_id, $include_fields, $exclude_fields)



Gets a `Variant` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getVariantById($product_id, $variant_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getVariantById: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\VariantResponse**](../Model/VariantResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getVariantMetafieldByProductIdAndVariantId**
> \BigCommerce\Api\v3\Model\MetafieldResponse getVariantMetafieldByProductIdAndVariantId($metafield_id, $product_id, $variant_id, $include_fields, $exclude_fields)



Gets a `Metafield`, by `product_id` and `variant_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getVariantMetafieldByProductIdAndVariantId($metafield_id, $product_id, $variant_id, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getVariantMetafieldByProductIdAndVariantId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getVariantMetafieldsByProductIdAndVariantId**
> \BigCommerce\Api\v3\Model\MetaFieldCollectionResponse getVariantMetafieldsByProductIdAndVariantId($product_id, $variant_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields)



Gets a `Metafield` object list, by `product_id` and `variant_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$key = "key_example"; // string | Filter based on a metafield's key.
$namespace = "namespace_example"; // string | Filter based on a metafield's key.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getVariantMetafieldsByProductIdAndVariantId($product_id, $variant_id, $page, $limit, $key, $namespace, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getVariantMetafieldsByProductIdAndVariantId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **key** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **namespace** | **string**| Filter based on a metafield&#39;s key. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\MetaFieldCollectionResponse**](../Model/MetaFieldCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getVariants**
> \BigCommerce\Api\v3\Model\VariantCollectionResponse getVariants($id, $sku, $page, $limit, $include_fields, $exclude_fields)



Returns a `Variant` object list from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$id = 56; // int | Filter items by id.
$sku = "sku_example"; // string | Filter items by sku.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getVariants($id, $sku, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getVariants: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **int**| Filter items by id. | [optional]
 **sku** | **string**| Filter items by sku. | [optional]
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\VariantCollectionResponse**](../Model/VariantCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getVariantsByProductId**
> \BigCommerce\Api\v3\Model\VariantCollectionResponse getVariantsByProductId($product_id, $page, $limit, $include_fields, $exclude_fields)



Returns a `Variant` object list from the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$page = 56; // int | Specifies the page number in a limited (paginated) list of products.
$limit = 56; // int | Controls the number of items per page in a limited (paginated) list of products.
$include_fields = "include_fields_example"; // string | Fields to include, in a comma-separated list. The ID and the specified fields will be returned.
$exclude_fields = "exclude_fields_example"; // string | Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded.

try {
    $result = $api_instance->getVariantsByProductId($product_id, $page, $limit, $include_fields, $exclude_fields);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->getVariantsByProductId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **page** | **int**| Specifies the page number in a limited (paginated) list of products. | [optional]
 **limit** | **int**| Controls the number of items per page in a limited (paginated) list of products. | [optional]
 **include_fields** | **string**| Fields to include, in a comma-separated list. The ID and the specified fields will be returned. | [optional]
 **exclude_fields** | **string**| Fields to exclude, in a comma-separated list. The specified fields will be excluded from a response. The ID cannot be excluded. | [optional]

### Return type

[**\BigCommerce\Api\v3\Model\VariantCollectionResponse**](../Model/VariantCollectionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateBrand**
> \BigCommerce\Api\v3\Model\BrandResponse updateBrand($brand_id, $brand)



Updates a `Brand` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$brand = new \BigCommerce\Api\v3\Model\BrandPut(); // \BigCommerce\Api\v3\Model\BrandPut | Returns a `Brand` from the BigCommerce Catalog.

try {
    $result = $api_instance->updateBrand($brand_id, $brand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateBrand: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **brand** | [**\BigCommerce\Api\v3\Model\BrandPut**](../Model/\BigCommerce\Api\v3\Model\BrandPut.md)| Returns a &#x60;Brand&#x60; from the BigCommerce Catalog. |

### Return type

[**\BigCommerce\Api\v3\Model\BrandResponse**](../Model/BrandResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateBrandMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse updateBrandMetafield($metafield_id, $brand_id, $metafield)



Updates a `Metafield` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$brand_id = 56; // int | The ID of the `Brand` to which the resource belongs.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPut(); // \BigCommerce\Api\v3\Model\MetafieldPut | A `Metafield` object.

try {
    $result = $api_instance->updateBrandMetafield($metafield_id, $brand_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateBrandMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **brand_id** | **int**| The ID of the &#x60;Brand&#x60; to which the resource belongs. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPut**](../Model/\BigCommerce\Api\v3\Model\MetafieldPut.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateBulkPricingRule**
> \BigCommerce\Api\v3\Model\BulkPricingRuleResponse updateBulkPricingRule($product_id, $bulk_pricing_rule_id, $bulk_pricing_rule)



Updates a Product's `BulkPricingRule`, based on the `product_id` and `bulk_pricing_rule_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$bulk_pricing_rule_id = 56; // int | The ID of the `BulkPricingRule`.
$bulk_pricing_rule = new \BigCommerce\Api\v3\Model\BulkPricingRulePut(); // \BigCommerce\Api\v3\Model\BulkPricingRulePut | `BulkPricingRule` object.

try {
    $result = $api_instance->updateBulkPricingRule($product_id, $bulk_pricing_rule_id, $bulk_pricing_rule);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateBulkPricingRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **bulk_pricing_rule_id** | **int**| The ID of the &#x60;BulkPricingRule&#x60;. |
 **bulk_pricing_rule** | [**\BigCommerce\Api\v3\Model\BulkPricingRulePut**](../Model/\BigCommerce\Api\v3\Model\BulkPricingRulePut.md)| &#x60;BulkPricingRule&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\BulkPricingRuleResponse**](../Model/BulkPricingRuleResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateCategory**
> \BigCommerce\Api\v3\Model\CategoryResponse updateCategory($category_id, $category)



Updates a `Category` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$category = new \BigCommerce\Api\v3\Model\CategoryPut(); // \BigCommerce\Api\v3\Model\CategoryPut | A BigCommerce `Category` object.

try {
    $result = $api_instance->updateCategory($category_id, $category);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateCategory: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **category** | [**\BigCommerce\Api\v3\Model\CategoryPut**](../Model/\BigCommerce\Api\v3\Model\CategoryPut.md)| A BigCommerce &#x60;Category&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\CategoryResponse**](../Model/CategoryResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateCategoryMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse updateCategoryMetafield($metafield_id, $category_id, $metafield)



Updates a `Metafield` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$category_id = 56; // int | The ID of the `Category` to which the resource belongs.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPut(); // \BigCommerce\Api\v3\Model\MetafieldPut | A `Metafield` object.

try {
    $result = $api_instance->updateCategoryMetafield($metafield_id, $category_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateCategoryMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **category_id** | **int**| The ID of the &#x60;Category&#x60; to which the resource belongs. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPut**](../Model/\BigCommerce\Api\v3\Model\MetafieldPut.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateComplexRule**
> \BigCommerce\Api\v3\Model\ComplexRuleResponse updateComplexRule($product_id, $complex_rule_id, $complex_rule)



Updates a Product's `ComplexRule`, based on the `product_id` and `complex_rule_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$complex_rule_id = 56; // int | The ID of the `ComplexRule`.
$complex_rule = new \BigCommerce\Api\v3\Model\ComplexRulePut(); // \BigCommerce\Api\v3\Model\ComplexRulePut | `ComplexRule` object.

try {
    $result = $api_instance->updateComplexRule($product_id, $complex_rule_id, $complex_rule);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateComplexRule: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **complex_rule_id** | **int**| The ID of the &#x60;ComplexRule&#x60;. |
 **complex_rule** | [**\BigCommerce\Api\v3\Model\ComplexRulePut**](../Model/\BigCommerce\Api\v3\Model\ComplexRulePut.md)| &#x60;ComplexRule&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ComplexRuleResponse**](../Model/ComplexRuleResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateConfigurableField**
> \BigCommerce\Api\v3\Model\ConfigurableFieldResponse updateConfigurableField($product_id, $configurable_field_id, $configurable_field)



Updates a Product's `ConfigurableField`, based on the `product_id` and `configurable_field_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$configurable_field_id = 56; // int | The ID of the `ConfigurableField`.
$configurable_field = new \BigCommerce\Api\v3\Model\ConfigurableFieldPut(); // \BigCommerce\Api\v3\Model\ConfigurableFieldPut | `ConfigurableField` object.

try {
    $result = $api_instance->updateConfigurableField($product_id, $configurable_field_id, $configurable_field);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateConfigurableField: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **configurable_field_id** | **int**| The ID of the &#x60;ConfigurableField&#x60;. |
 **configurable_field** | [**\BigCommerce\Api\v3\Model\ConfigurableFieldPut**](../Model/\BigCommerce\Api\v3\Model\ConfigurableFieldPut.md)| &#x60;ConfigurableField&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ConfigurableFieldResponse**](../Model/ConfigurableFieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateCustomField**
> \BigCommerce\Api\v3\Model\CustomFieldResponse updateCustomField($product_id, $custom_field_id, $custom_field)



Updates a Product's `CustomField`, based on the `product_id` and `custom_field_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$custom_field_id = 56; // int | The ID of the `CustomField`.
$custom_field = new \BigCommerce\Api\v3\Model\CustomFieldPut(); // \BigCommerce\Api\v3\Model\CustomFieldPut | `CustomField` object.

try {
    $result = $api_instance->updateCustomField($product_id, $custom_field_id, $custom_field);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateCustomField: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **custom_field_id** | **int**| The ID of the &#x60;CustomField&#x60;. |
 **custom_field** | [**\BigCommerce\Api\v3\Model\CustomFieldPut**](../Model/\BigCommerce\Api\v3\Model\CustomFieldPut.md)| &#x60;CustomField&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\CustomFieldResponse**](../Model/CustomFieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateModifier**
> \BigCommerce\Api\v3\Model\ModifierResponse updateModifier($product_id, $modifier_id, $modifier)



Updates a Product's `Modifier` based on the `product_id` and `modifier_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$modifier = new \BigCommerce\Api\v3\Model\ModifierPut(); // \BigCommerce\Api\v3\Model\ModifierPut | A BigCommerce `Modifier` object.

try {
    $result = $api_instance->updateModifier($product_id, $modifier_id, $modifier);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateModifier: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **modifier** | [**\BigCommerce\Api\v3\Model\ModifierPut**](../Model/\BigCommerce\Api\v3\Model\ModifierPut.md)| A BigCommerce &#x60;Modifier&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ModifierResponse**](../Model/ModifierResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateModifierValue**
> \BigCommerce\Api\v3\Model\ModifierValueResponse updateModifierValue($product_id, $modifier_id, $value_id, $modifier_value)



Updates a Product's `ModifierValue` based on the `product_id`, `modifier_id`, and `value_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$modifier_id = 56; // int | The ID of the `Modifier`.
$value_id = 56; // int | The ID of the `Modifier/Option Value`.
$modifier_value = new \BigCommerce\Api\v3\Model\ModifierValuePut(); // \BigCommerce\Api\v3\Model\ModifierValuePut | A BigCommerce `ModifierValue` object.

try {
    $result = $api_instance->updateModifierValue($product_id, $modifier_id, $value_id, $modifier_value);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateModifierValue: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **modifier_id** | **int**| The ID of the &#x60;Modifier&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier/Option Value&#x60;. |
 **modifier_value** | [**\BigCommerce\Api\v3\Model\ModifierValuePut**](../Model/\BigCommerce\Api\v3\Model\ModifierValuePut.md)| A BigCommerce &#x60;ModifierValue&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ModifierValueResponse**](../Model/ModifierValueResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateOption**
> \BigCommerce\Api\v3\Model\OptionResponse updateOption($product_id, $option_id, $option)



Updates a Product's `Option`, based on the `product_id` and `option_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$option = new \BigCommerce\Api\v3\Model\OptionPut(); // \BigCommerce\Api\v3\Model\OptionPut | A BigCommerce `Option` object.

try {
    $result = $api_instance->updateOption($product_id, $option_id, $option);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateOption: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **option** | [**\BigCommerce\Api\v3\Model\OptionPut**](../Model/\BigCommerce\Api\v3\Model\OptionPut.md)| A BigCommerce &#x60;Option&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\OptionResponse**](../Model/OptionResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateOptionValue**
> \BigCommerce\Api\v3\Model\OptionValueResponse updateOptionValue($product_id, $option_id, $value_id, $option_value)



Updates a Product's `OptionValue` based on the `product_id`, `option_id`, and `value_id`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$option_id = 56; // int | The ID of the `Option`.
$value_id = 56; // int | The ID of the `Modifier/Option Value`.
$option_value = new \BigCommerce\Api\v3\Model\OptionValuePut(); // \BigCommerce\Api\v3\Model\OptionValuePut | A BigCommerce `OptionValue` object.

try {
    $result = $api_instance->updateOptionValue($product_id, $option_id, $value_id, $option_value);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateOptionValue: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **option_id** | **int**| The ID of the &#x60;Option&#x60;. |
 **value_id** | **int**| The ID of the &#x60;Modifier/Option Value&#x60;. |
 **option_value** | [**\BigCommerce\Api\v3\Model\OptionValuePut**](../Model/\BigCommerce\Api\v3\Model\OptionValuePut.md)| A BigCommerce &#x60;OptionValue&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\OptionValueResponse**](../Model/OptionValueResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateProduct**
> \BigCommerce\Api\v3\Model\ProductResponse updateProduct($product_id, $product)



Updates a `Product` in the BigCommerce Catalog.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$product = new \BigCommerce\Api\v3\Model\ProductPut(); // \BigCommerce\Api\v3\Model\ProductPut | A BigCommerce `Product` object.

try {
    $result = $api_instance->updateProduct($product_id, $product);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateProduct: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **product** | [**\BigCommerce\Api\v3\Model\ProductPut**](../Model/\BigCommerce\Api\v3\Model\ProductPut.md)| A BigCommerce &#x60;Product&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductResponse**](../Model/ProductResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateProductImage**
> \BigCommerce\Api\v3\Model\ProductImageResponse updateProductImage($product_id, $image_id, $product_image)



Updates an image on a product. Publicly accessible URLs and files (form post) are valid parameters.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$image_id = 56; // int | The ID of the `Image` that is being operated on.
$product_image = new \BigCommerce\Api\v3\Model\ProductImagePut(); // \BigCommerce\Api\v3\Model\ProductImagePut | A BigCommerce `ProductImage` object.

try {
    $result = $api_instance->updateProductImage($product_id, $image_id, $product_image);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateProductImage: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **image_id** | **int**| The ID of the &#x60;Image&#x60; that is being operated on. |
 **product_image** | [**\BigCommerce\Api\v3\Model\ProductImagePut**](../Model/\BigCommerce\Api\v3\Model\ProductImagePut.md)| A BigCommerce &#x60;ProductImage&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductImageResponse**](../Model/ProductImageResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateProductMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse updateProductMetafield($metafield_id, $product_id, $metafield)



Updates a `Metafield` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPut(); // \BigCommerce\Api\v3\Model\MetafieldPut | A `Metafield` object.

try {
    $result = $api_instance->updateProductMetafield($metafield_id, $product_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateProductMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPut**](../Model/\BigCommerce\Api\v3\Model\MetafieldPut.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateProductReview**
> \BigCommerce\Api\v3\Model\ProductReviewResponse updateProductReview($product_id, $review_id, $product_review)



Updates a product review.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$review_id = 56; // int | The ID of the `review` that is being operated on.
$product_review = new \BigCommerce\Api\v3\Model\ProductReviewPut(); // \BigCommerce\Api\v3\Model\ProductReviewPut | A BigCommerce `ProductReview` object.

try {
    $result = $api_instance->updateProductReview($product_id, $review_id, $product_review);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateProductReview: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **review_id** | **int**| The ID of the &#x60;review&#x60; that is being operated on. |
 **product_review** | [**\BigCommerce\Api\v3\Model\ProductReviewPut**](../Model/\BigCommerce\Api\v3\Model\ProductReviewPut.md)| A BigCommerce &#x60;ProductReview&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductReviewResponse**](../Model/ProductReviewResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateProductVideo**
> \BigCommerce\Api\v3\Model\ProductVideoResponse updateProductVideo($product_id, $video_id, $product_video)



Updates a video on a product.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$video_id = "video_id_example"; // string | The ID of the `Video` that is being operated on.
$product_video = new \BigCommerce\Api\v3\Model\ProductVideoPut(); // \BigCommerce\Api\v3\Model\ProductVideoPut | A BigCommerce `ProductVideo` object.

try {
    $result = $api_instance->updateProductVideo($product_id, $video_id, $product_video);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateProductVideo: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **video_id** | **string**| The ID of the &#x60;Video&#x60; that is being operated on. |
 **product_video** | [**\BigCommerce\Api\v3\Model\ProductVideoPut**](../Model/\BigCommerce\Api\v3\Model\ProductVideoPut.md)| A BigCommerce &#x60;ProductVideo&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\ProductVideoResponse**](../Model/ProductVideoResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateVariant**
> \BigCommerce\Api\v3\Model\VariantResponse updateVariant($product_id, $variant_id, $variant)



Updates a `Variant` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$variant = new \BigCommerce\Api\v3\Model\VariantPut(); // \BigCommerce\Api\v3\Model\VariantPut | A `Variant` object.

try {
    $result = $api_instance->updateVariant($product_id, $variant_id, $variant);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateVariant: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **variant** | [**\BigCommerce\Api\v3\Model\VariantPut**](../Model/\BigCommerce\Api\v3\Model\VariantPut.md)| A &#x60;Variant&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\VariantResponse**](../Model/VariantResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **updateVariantMetafield**
> \BigCommerce\Api\v3\Model\MetafieldResponse updateVariantMetafield($metafield_id, $product_id, $variant_id, $metafield)



Updates a `Metafield` object.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$clientConfig = new BigCommerce\Api\v3\Api\Configuration();
$clientConfig->setHost({STORE_API_URL});
$clientConfig->addDefaultHeader('X-Auth-Client', {CLIENT_ID});
$clientConfig->addDefaultHeader('X-Auth-Token', {API_TOKEN});
$httpClient = new ApiClient($clientConfig);
$api_instance = new BigCommerce\Api\v3\Api\CatalogApi();
$metafield_id = 56; // int | The ID of the `Metafield`.
$product_id = 56; // int | The ID of the `Product` to which the resource belongs.
$variant_id = 56; // int | ID of the variant on a product, or on an associated Price List Record.
$metafield = new \BigCommerce\Api\v3\Model\MetafieldPut(); // \BigCommerce\Api\v3\Model\MetafieldPut | A `Metafield` object.

try {
    $result = $api_instance->updateVariantMetafield($metafield_id, $product_id, $variant_id, $metafield);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->updateVariantMetafield: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **metafield_id** | **int**| The ID of the &#x60;Metafield&#x60;. |
 **product_id** | **int**| The ID of the &#x60;Product&#x60; to which the resource belongs. |
 **variant_id** | **int**| ID of the variant on a product, or on an associated Price List Record. |
 **metafield** | [**\BigCommerce\Api\v3\Model\MetafieldPut**](../Model/\BigCommerce\Api\v3\Model\MetafieldPut.md)| A &#x60;Metafield&#x60; object. |

### Return type

[**\BigCommerce\Api\v3\Model\MetafieldResponse**](../Model/MetafieldResponse.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

