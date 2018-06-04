# CategoryNode

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | The unique numeric ID of the category; increments sequentially. | [optional] 
**parent_id** | **int** | The unique numeric ID of the category&#39;s parent. This field controls where the category sits in the tree of categories that organize the catalog. | [optional] 
**name** | **string** | The name displayed for the category. Name is unique with respect to the category&#39;s siblings. | [optional] 
**is_visible** | **bool** | Flag to determine whether the product should be displayed to customers browsing the store. If &#x60;true&#x60;, the category will be displayed. If &#x60;false&#x60;, the category will be hidden from view. | [optional] 
**url** | **string** | The custom URL for the category on the storefront. | [optional] 
**children** | [**\BigCommerce\Api\v3\Model\CategoryNode[]**](CategoryNode.md) | The list of children of the category. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


