# BulkPricingRulePut

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**quantity_min** | **int** | The minimum inclusive quantity of a product to satisfy this rule. Must be greater than or equal to zero. | [optional] 
**quantity_max** | **int** | The maximum inclusive quantity of a product to satisfy this rule. Must be greater than the &#x60;quantity_min&#x60; value – unless this field has a value of 0 (zero), in which case there will be no maximum bound for this rule. | [optional] 
**type** | **string** | The type of adjustment that is made. Values: &#x60;price&#x60; - the adjustment amount per product; &#x60;percent&#x60; - the adjustment as a percentage of the original price; &#x60;fixed&#x60; - the adjusted absolute price of the product. | [optional] 
**amount** | **double** | The value of the adjustment by the bulk pricing rule. | [optional] 
**id** | **int** | The ID of the bulk pricing rule. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


