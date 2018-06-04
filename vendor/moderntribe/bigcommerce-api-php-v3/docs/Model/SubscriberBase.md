# SubscriberBase

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | The unique numeric ID of the subscriber; increments sequentially. | [optional] 
**email** | **string** | The email of the subscriber. Must be unique. | [optional] 
**first_name** | **string** | The first name of the subscriber. | [optional] 
**last_name** | **string** | The last name of the subscriber. | [optional] 
**source** | **string** | The source of the subscriber. Values are: &#x60;storefront&#x60;, &#x60;order&#x60;, or &#x60;custom&#x60;. | [optional] 
**order_id** | **int** | The ID of the source order, if source was an order. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


