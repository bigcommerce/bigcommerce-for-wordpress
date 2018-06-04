# ProductImage

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**is_thumbnail** | **bool** | Flag for identifying whether the image is used as the product&#39;s thumbnail. | [optional] 
**sort_order** | **int** | The order in which the image will be displayed on the product page. Higher integers give the image a lower priority. When updating, if the image is given a lower priority, all images with a &#x60;sort_order&#x60; the same as or greater than the image&#39;s new &#x60;sort_order&#x60; value will have their &#x60;sort_order&#x60;s reordered. | [optional] 
**description** | **string** | The description for the image. | [optional] 
**id** | **int** | The unique numeric ID of the image; increments sequentially. | [optional] 
**product_id** | **int** | The unique numeric identifier for the product with which the image is associated. | [optional] 
**image_file** | **string** | The local path to the original image file uploaded to BigCommerce. | [optional] 
**url_zoom** | **string** | The zoom URL for this image. By default, this is used as the zoom image on product pages when zoom images are enabled. | [optional] 
**url_standard** | **string** | The standard URL for this image. By default, this is used for product-page images. | [optional] 
**url_thumbnail** | **string** | The thumbnail URL for this image. By default, this is the image size used on the category page and in side panels. | [optional] 
**url_tiny** | **string** | The tiny URL for this image. By default, this is the image size used for thumbnails beneath the product image on a product page. | [optional] 
**date_modified** | [**\DateTime**](\DateTime.md) | The date on which the product image was modified. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


