# ModifierValue

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**is_default** | **bool** | The flag for preselecting a value as the default on the storefront. This field is not supported for swatch options/modifiers. | [optional] 
**label** | **string** | The text display identifying the value on the storefront. | [optional] 
**sort_order** | **int** | The order in which the value will be displayed on the product page. | [optional] 
**value_data** | **object** | Extra data describing the value, based on the type of option or modifier with which the value is associated. The &#x60;swatch&#x60; type option can accept an array of &#x60;colors&#x60;, with up to three hexidecimal color keys; or an &#x60;image_url&#x60;, which is a full image URL path including protocol. The &#x60;product list&#x60; type option requires a &#x60;product_id&#x60;. The &#x60;checkbox&#x60; type option requires a boolean flag, called &#x60;checked_value&#x60;, to determine which value is considered to be the checked state. | [optional] 
**adjusters** | [**\BigCommerce\Api\v3\Model\ModifierValueBaseAdjusters**](ModifierValueBaseAdjusters.md) |  | [optional] 
**id** | **int** | The unique numeric ID of the value; increments sequentially. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


