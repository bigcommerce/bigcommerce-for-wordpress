# Metafield

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**permission_set** | **string** | Determines whether the field is completely private to the app that owns the field (&#x60;app_only&#x60;), or visible to other API consumers (&#x60;read&#x60;), or completely open for reading and writing to other apps (&#x60;write&#x60;). | [optional] 
**namespace** | **string** | Namespace for the metafield, for organizational purposes. | [optional] 
**key** | **string** | The name of the field, for example: &#x60;location_id&#x60;, &#x60;color&#x60;. | [optional] 
**value** | **string** | The value of the field, for example: &#x60;1&#x60;, &#x60;blue&#x60;. | [optional] 
**description** | **string** | Description for the metafields. | [optional] 
**resource_type** | **string** | The type of resource with which the metafield is associated. | [optional] 
**resource_id** | **int** | The unique identifier for the resource with which the metafield is associated. | [optional] 
**id** | **int** | The unique identifier for the metafields. | [optional] 
**created_at** | [**\DateTime**](\DateTime.md) | Date and time of the metafield&#39;s creation. | [optional] 
**updated_at** | [**\DateTime**](\DateTime.md) | Date and time when the metafield was last updated. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


