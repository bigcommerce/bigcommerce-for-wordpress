# ProductVideoPost

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**title** | **string** | The title for the video. If left blank, this will be filled in according to data on a host site. | [optional] 
**description** | **string** | The description for the video. If left blank, this will be filled in according to data on a host site. | [optional] 
**sort_order** | **int** | The order in which the video will be displayed on the product page. Higher integers give the video a lower priority. When updating, if the video is given a lower priority, all videos with a &#x60;sort_order&#x60; the same as or greater than the video&#39;s new &#x60;sort_order&#x60; value will have their &#x60;sort_order&#x60;s reordered. | [optional] 
**type** | **string** | The video type (a short name of a host site). | [optional] 
**video_id** | **string** | The ID of the video on a host site. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


