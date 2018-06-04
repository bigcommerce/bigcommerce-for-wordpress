# VariantProductPost

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**cost_price** | **double** | The cost price of the variant. Not affected by Price List prices. | [optional] 
**price** | **double** | This variant&#39;s base price on the storefront. If a Price List ID is used, the Price List value will be used. If a Price List ID is not used, and this value is &#x60;null&#x60;, the product&#39;s default price (set in the Product resource&#39;s &#x60;price&#x60; field) will be used as the base price. | [optional] 
**sale_price** | **double** | This variant&#39;s sale price on the storefront. If a Price List ID is used, the Price List value will be used. If a Price List ID is not used, and this value is null, the product&#39;s sale price (set in the Product resource&#39;s &#x60;price&#x60; field) will be used as the sale price. | [optional] 
**retail_price** | **double** | This variant&#39;s retail price on the storefront. If a Price List ID is used, the Price List value will be used. If a Price List ID is not used, and this value is null, the product&#39;s retail price (set in the Product resource&#39;s &#x60;price&#x60; field) will be used as the retail price. | [optional] 
**weight** | **double** | This variant&#39;s base weight on the storefront. If this value is null, the product&#39;s default weight (set in the Product resource&#39;s weight field) will be used as the base weight. | [optional] 
**width** | **double** | Width of the variant, which can be used when calculating shipping costs. If this value is &#x60;null&#x60;, the product&#39;s default width (set in the Product resource&#39;s &#x60;width&#x60; field) will be used as the base width. | [optional] 
**height** | **double** | Height of the variant, which can be used when calculating shipping costs. If this value is &#x60;null&#x60;, the product&#39;s default height (set in the Product resource&#39;s &#x60;height&#x60; field) will be used as the base height. | [optional] 
**depth** | **double** | Depth of the variant, which can be used when calculating shipping costs. If this value is &#x60;null&#x60;, the product&#39;s default depth (set in the Product resource&#39;s &#x60;depth&#x60; field) will be used as the base depth. | [optional] 
**is_free_shipping** | **bool** | Flag used to indicate whether the variant has free shipping. If &#x60;true&#x60;, the shipping cost for the variant will be zero. | [optional] 
**fixed_cost_shipping_price** | **double** | A fixed shipping cost for the variant. If defined, this value will be used during checkout instead of normal shipping-cost calculation. | [optional] 
**purchasing_disabled** | **bool** | If &#x60;true&#x60;, this variant will not be purchasable on the storefront. | [optional] 
**purchasing_disabled_message** | **string** | If &#x60;purchasing_disabled&#x60; is &#x60;true&#x60;, this message should show on the storefront when the variant is selected. | [optional] 
**image_url** | **string** | The image that will be displayed when this variant is selected on the storefront. When updating a SKU image, send the publicly accessible URL. Supported image formats are JPEG, PNG, and GIF. Generic product images not specific to the variant should be stored on the product. | [optional] 
**upc** | **string** | The UPC code used in feeds for shopping comparison sites and external channel integrations. | [optional] 
**inventory_level** | **int** | Inventory level for the variant, which is used when the product&#39;s inventory_tracking is set to &#x60;variant&#x60;. | [optional] 
**inventory_warning_level** | **int** | When the variant hits this inventory level, it is considered low stock. | [optional] 
**bin_picking_number** | **string** | Identifies where in a warehouse the variant is located. | [optional] 
**product_id** | **int** |  | [optional] 
**sku** | **string** |  | [optional] 
**option_values** | [**\BigCommerce\Api\v3\Model\OptionValueProductPost[]**](OptionValueProductPost.md) |  | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


