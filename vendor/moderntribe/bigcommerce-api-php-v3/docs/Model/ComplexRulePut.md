# ComplexRulePut

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**product_id** | **int** | The unique numeric ID of the product with which the rule is associated; increments sequentially. | [optional] 
**sort_order** | **int** | The priority to give this rule when making adjustments to the product properties. | [optional] 
**enabled** | **bool** | Flag for determining whether the rule is to be used when adjusting a product&#39;s price, weight, image, or availabilty. | [optional] 
**stop** | **bool** | Flag for determining whether other rules should not be applied after this rule has been applied. | [optional] 
**purchasing_disabled** | **bool** | Flag for determining whether the rule should disable purchasing of a product when the conditions are applied. | [optional] 
**purchasing_disabled_message** | **string** | Message displayed on the storefront when a rule disables the purchasing of a product. | [optional] 
**purchasing_hidden** | **bool** | Flag for determining whether the rule should hide purchasing of a product when the conditions are applied. | [optional] 
**price_adjuster** | [**\BigCommerce\Api\v3\Model\Adjuster**](Adjuster.md) |  | [optional] 
**weight_adjuster** | [**\BigCommerce\Api\v3\Model\Adjuster**](Adjuster.md) |  | [optional] 
**id** | **int** | The unique numeric ID of the rule; increments sequentially. | [optional] 
**conditions** | [**\BigCommerce\Api\v3\Model\ComplexRuleConditionPut[]**](ComplexRuleConditionPut.md) |  | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


