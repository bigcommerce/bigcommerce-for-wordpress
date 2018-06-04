# OptionConfig

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**default_value** | **string** | (date, text, multi_line_text, numbers_only_text) The default value. Shown on a date option as an ISO-8601–formatted string, or on a text option as a string. | [optional] 
**checked_by_default** | **bool** | (checkbox) Flag for setting the checkbox to be checked by default. | [optional] 
**checkbox_label** | **string** | (checkbox) Label displayed for the checkbox option. | [optional] 
**date_limited** | **bool** | (date) Flag to limit the dates allowed to be entered on a date option. | [optional] 
**date_limit_mode** | **string** | (date) The type of limit that is allowed to be entered on a date option. | [optional] 
**date_earliest_value** | [**\DateTime**](Date.md) | (date) The earliest date allowed to be entered on the date option, as an ISO-8601 formatted string. | [optional] 
**date_latest_value** | [**\DateTime**](Date.md) | (date) The latest date allowed to be entered on the date option, as an ISO-8601 formatted string. | [optional] 
**file_types_mode** | **string** | (file) The kind of restriction on the file types that can be uploaded with a file upload option. Values: &#x60;specific&#x60; - restricts uploads to particular file types; &#x60;all&#x60; - allows all file types. | [optional] 
**file_types_supported** | **string[]** | (file) The type of files allowed to be uploaded if the &#x60;file_type_option&#x60; is set to &#x60;specific&#x60;. Values:   &#x60;images&#x60; - Allows upload of image MIME types (&#x60;bmp&#x60;, &#x60;gif&#x60;, &#x60;jpg&#x60;, &#x60;jpeg&#x60;, &#x60;jpe&#x60;, &#x60;jif&#x60;, &#x60;jfif&#x60;, &#x60;jfi&#x60;, &#x60;png&#x60;, &#x60;wbmp&#x60;, &#x60;xbm&#x60;, &#x60;tiff&#x60;). &#x60;documents&#x60; - Allows upload of document MIME types (&#x60;txt&#x60;, &#x60;pdf&#x60;, &#x60;rtf&#x60;, &#x60;doc&#x60;, &#x60;docx&#x60;, &#x60;xls&#x60;, &#x60;xlsx&#x60;, &#x60;accdb&#x60;, &#x60;mdb&#x60;, &#x60;one&#x60;, &#x60;pps&#x60;, &#x60;ppsx&#x60;, &#x60;ppt&#x60;, &#x60;pptx&#x60;, &#x60;pub&#x60;, &#x60;odt&#x60;, &#x60;ods&#x60;, &#x60;odp&#x60;, &#x60;odg&#x60;, &#x60;odf&#x60;).   &#x60;other&#x60; - Allows file types defined in the &#x60;file_types_other&#x60; array. | [optional] 
**file_types_other** | **string[]** | (file) A list of other file types allowed with the file upload option. | [optional] 
**file_max_size** | **int** | (file) The maximum size for a file that can be used with the file upload option. | [optional] 
**text_characters_limited** | **bool** | (text, multi_line_text) Flag to validate the length of a text or multi-line text input. | [optional] 
**text_min_length** | **int** | (text, multi_line_text) The minimum length allowed for a text or multi-line text option. | [optional] 
**text_max_length** | **int** | (text, multi_line_text) The maximum length allowed for a text or multi line text option. | [optional] 
**text_lines_limited** | **bool** | (multi_line_text) Flag to validate the maximum number of lines allowed on a multi-line text input. | [optional] 
**text_max_lines** | **int** | (multi_line_text) The maximum number of lines allowed on a multi-line text input. | [optional] 
**number_limited** | **bool** | (numbers_only_text) Flag to limit the value of a number option. | [optional] 
**number_limit_mode** | **string** | (numbers_only_text) The type of limit on values entered for a number option. | [optional] 
**number_lowest_value** | **float** | (numbers_only_text) The lowest allowed value for a number option if &#x60;number_limited&#x60; is true. | [optional] 
**number_highest_value** | **float** | (numbers_only_text) The highest allowed value for a number option if &#x60;number_limited&#x60; is true. | [optional] 
**number_integers_only** | **bool** | (numbers_only_text) Flag to limit the input on a number option to whole numbers only. | [optional] 
**product_list_adjusts_inventory** | **bool** | (product_list, product_list_with_images) Flag for automatically adjusting inventory on a product included in the list. | [optional] 
**product_list_adjusts_pricing** | **bool** | (product_list, product_list_with_images) Flag to add the optional product&#39;s price to the main product&#39;s price. | [optional] 
**product_list_shipping_calc** | **string** | (product_list, product_list_with_images) How to factor the optional product&#39;s weight and package dimensions into the shipping quote. Values: &#x60;none&#x60; - don&#39;t adjust; &#x60;weight&#x60; - use shipping weight only; &#x60;package&#x60; - use weight and dimensions. | [optional] 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


