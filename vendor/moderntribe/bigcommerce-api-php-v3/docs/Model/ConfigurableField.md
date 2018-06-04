# ConfigurableField

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **string** | The name for the configurable field. Will display on the storefront and can be used as a reference point in the Orders API. | [optional] 
**type** | **string** | The type of the configurable field, which determines what sort of information the field is intended to collect on the storefront. Supported types in include a text input, a multi-line textarea, a checkbox, a file upload, and a dropdown selection. | [optional] 
**file_allowed_types** | **string[]** | For fields of \&quot;file\&quot; type, this controls the allowed file types for upload. | [optional] 
**file_max_size** | **int[]** | For fields of \&quot;file\&quot; type, this controls the maximum file size. The platform has a maximum file size of 512MB for all uploads regardless of this setting. | [optional] 
**select_options** | **string[]** | For fields of \&quot;select\&quot; type, this is an array of the options which should be presented in the dropdown. | [optional] 
**required** | **bool** | Controls whether the field is required to have some input before a product may be added to cart. | [optional] 
**sort_order** | **int** | Controls the sort order of this field relative to other configurable fields on the product, for purposes of ordering them on the storefront. | [optional] 
**id** | **int** | The unique numeric ID of the configurable field; increments sequentially. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


