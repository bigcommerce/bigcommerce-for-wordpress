# ModifierPut

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **string** | BigCommerce API, which determines how it will display on the storefront. Acceptable values: &#x60;date&#x60;, &#x60;checkbox&#x60;, &#x60;file&#x60;, &#x60;text&#x60;, &#x60;multi_line_text&#x60;, &#x60;numbers_only_text&#x60;, &#x60;radio_buttons&#x60;, &#x60;rectangles&#x60;, &#x60;dropdown&#x60;, &#x60;product_list&#x60;, &#x60;product_list_with_images&#x60;, &#x60;swatch&#x60;. For reference, the former v2 API values are: D &#x3D; date, C &#x3D; checkbox, F &#x3D; file, T &#x3D; text, MT &#x3D; multi_line_text, N &#x3D; numbers_only_text, RB &#x3D; radio_buttons, RT &#x3D; rectangles, S &#x3D; dropdown, P &#x3D; product_list, PI &#x3D; product_list_with_images, CS &#x3D; swatch. | [optional] 
**required** | **bool** | Whether or not this modifer is required or not at checkout. | [optional] 
**config** | [**\BigCommerce\Api\v3\Model\OptionConfig**](OptionConfig.md) |  | [optional] 
**option_values** | [**\BigCommerce\Api\v3\Model\ModifierValue[]**](ModifierValue.md) |  | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


