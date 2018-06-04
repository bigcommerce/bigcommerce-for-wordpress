# OptionPut

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | The unique numerical ID of the option, increments sequentially. | [optional] 
**product_id** | **int** | The unique numerical ID of the product to which the option belongs. | [optional] 
**display_name** | **string** | The name of the option shown on the storefront. | [optional] 
**type** | **string** | The type of option, which determines how it will display on the storefront. Acceptable values: &#x60;radio_buttons&#x60;, &#x60;rectangles&#x60;, &#x60;dropdown&#x60;, &#x60;product_list&#x60;, &#x60;product_list_with_images&#x60;, &#x60;swatch&#x60;. For reference, the former v2 API values are: RB &#x3D; radio_buttons, RT &#x3D; rectangles, S &#x3D; dropdown, P &#x3D; product_list, PI &#x3D; product_list_with_images, CS &#x3D; swatch. | [optional] 
**config** | [**\BigCommerce\Api\v3\Model\OptionConfig**](OptionConfig.md) |  | [optional] 
**option_values** | [**\BigCommerce\Api\v3\Model\OptionValue[]**](OptionValue.md) |  | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


