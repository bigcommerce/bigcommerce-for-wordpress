# Changelog

## [4.5.1]

### Fixed

- Fixed product category slug changes when re-importing a term that overlaps
  with a term with a different parent.

## [4.5.0]

### Added
- Added shipping estimation calculator to the cart page as well as
  option to enable/disable this feature in the WordPress Customizer.

### Changed
- Changed how Quick View triggers detect their corresponding modal container.
  This change allows more flexibility in terms of where a Quick View button can be
  placed in a product card.


## [4.4.0]

### Added
- Added a new customizer option to hide search bar from product archive
- Added ability to embed iframe in Product description
- Added option to never sync products
- Added new sort options on Product Archive: by SKU and by Inventory Count


## [4.3.1]

### Fixed
- Fixed product duplication on import and single product front-end visibility when default customer group is not set in BC settings
- Fixed an issue with Quick View buttons/modals not triggering their respective Quick View dialog when multiple product
  grids are on the same page.


## [4.3.0]

### Added
- Added required additional onboarding step for setting a Checkout URL
- Allow img tag in product custom fields

### Fixed
- Fixed add new product component block error
- Fixed HTML in Settings helptext
- Visible on storefront BC setting not reflected in WP
- Fixed front-end exception if channel is not set


## [4.2.0]

### Added
- Added Checkout URL documentation link in Settings

### Changed
- Updated product quick view modal to work after ajax refresh


## [4.1.0]

### Added
- Added in support for variant inventories
- Added version check to the plugin loader so that the plugin is running only if the minimum version is met
- Integrated support for Akismet spam gateway for account sign ups

### Changed
- Updated list styles in catalog pages
- Changed share wishlist functionality to copy wishlist link
- Updated tax label
- Added validation for presence of Shortcode for registered BC pages

### Fixed
- Category description is now properly rendering images uploaded in the BC admin


## [4.0.0]

### Added
- Added support for Matomo analytics plugin utilizing the events for: `setCustomVariable`, `addEcommerceItem`,
  and `setEcommerceView` events.

### Changed
- Minimum supported WordPress version increased to 5.2
- Minimum supported PHP version increased to 7.2
- Reviews will no longer be imported as part of the primary import process.
  A separate cron job will cache a single page of reviews in post meta for
  each product. Subsequent pages will be queried directly from the BigCommerce
  API when visitors request to view them.
- `\BigCommerce\Post_Types\Product\Product::get_reviews()` will only return
  the cached collection of reviews (usually 12 or fewer). Options to sort
  reviews are no longer recognized.
- `\BigCommerce\Import\Importers\Reviews\Review_Builder` has moved to
  `\BigCommerce\Reviews\Review_Builder`.
- `\BigCommerce\Import\Importers\Reviews\Review_Fetcher` has moved to
  `\BigCommerce\Review\Review_Fetcher`. The `$product_id` parameter has moved
  from the constructor to the `fetch()` method.
- Updated templates to handle ajax loading of the initial page of reviews. Affected
  templates are `review-list.php` and `review-list-pagination.php`.

### Removed
- Removed the `bc_reviews` table. All queries that used this table have been
  updated to use post meta or query the BigCommerce API.
- `\BigCommerce\Post_Types\Product\Product::get_review_count()` will return
  the count of approved reviews. The `$status` parameter has been removed.
- Remove the abstract class `\BigCommerce\Import\Importers\Record_Builder`.
  The functionality that was shared by subclasses is available through the
  trait `\BigCommerce\Api\Api_Data_Sanitizer`.
- Deprecated classes, methods, and variables from prior versions of the plugin
  have been removed according to schedule.

### Deprecated
- `\BigCommerce\Schema\Reviews_Table` has been deprecated and will be removed
  in version 5.0. Its only remaining functionality is to remove the now-unused
  table from the database.

## [3.22.0]

### Added
- Segment support for cross-domain analytics with Google Analytics. It enables the
  `autoLinker` plugin feature for GA.
- Enforce BigCommerce password requirements when registering new customers
- Display category description on product category pages

### Changed
- Data Sanitization/Escaping code refactor for WPVIP compliance

## [3.21.0]

### Added
- Added manual site URL sync option in the Diagnostics panel

### Changed
- Modified admin import timeout message
- Update checkout SDK to 1.79.0

### Fixed
- Fixed Best Selling sort on Product archive page
- Fixed category sort not being reflected in the menu


## [3.20.0]

### Changed
- Link to BigCommerce HTTPS settings from Embedded Checkout settings tab.


### Fixed

- Fixed an issue with product grids where Ajax to cart is enabled but no simple products are on the page. This would
  cause the Add to Cart button on Quick View to redirect to the cart page instead of an ajax submission.

- Fixed Google SiteKit plugin breaking Settings page.


## [3.19.0]

### Added

- Added some additional support for Flatsome theme minicart widget on mobile/smaller viewports.

### Fixed

- Featured and regular product sort order reflect order in BC store


## [3.18.1]

### Fixed

- Fixed an issue with the add to cart ajax feature where the mini cart does not update after a successful request.


## [3.18.0]

### Added

- Added support for new larger image size and zoom features on the PDP in multiple
  supported WordPress themes.
- Added version numbers to templates. Diagnostics panel will now check major versions of overridden files.
- Support for Flatsome theme added starting with version 3.10.1 of the theme.
- Added filter to Cart_Mapper map (PR #201).

### Changed

- Changed filtering on Brand and Product Category descriptions to use `wp_kses_post()` instead
  of `wp_filter_kses()`. This allows the use of more HTML tags in those term descriptions.
- Added some additional refined style adjustments to the WordPress Twenty-Twenty theme.
- Made a change to the onboarding page for connecting your channel. Form sections now start
  open so you can edit all fields immediately.
- Both enabled site ssl and Sitewide HTTPS setting in BC store are required to use embedded checkout.

### Fixed

- Empty cart destination link from Customizer is properly linked in the cart.
- Product sync reflects adding/removing products from store channels.


## [3.17.0]

### Added

- Added controls to the theme customizer to edit product category and brand taxonomy slugs.
- Added Customizer option to control image size output for image gallery and
  the featured image component.
  - Modifications were made to the product-gallery.php and product-featured-image.php
    templates to accommodate these new changes. Update your template overrides accordingly.
  - Styles updates were also adjusted to accommodate this new image size
    by adding a new body class: `.bc-gallery-size-bc-xmedium`
- Added Customizer option to allow the user to apply a zoom-on-hover interaction
  for main gallery images on the product detail page.
  - Modifications were made to the product-gallery.php template to accommodate
    these new changes. Update your template overrides accordingly.

### Changed

- Modified the names of the cart cookies for better compatibility with server/host
  restrictions. (i.e. Some hosts would remove BC cookies because they lacked a `wp-` prefix)
- Better error handling around 0 qty field entries on cart items. The ajax response would
  leave the 0 in the field but would continue to show the previous price value. Entering 0
  does not delete an item from your cart so we've updated this to return the field value to
  its previous value.

## [3.16.0]

### Changed

- Removed the notification to finish setting up shipping methods. So long as at least one
  shipping zone exists (and it will always exist), the notification will not be displayed.
- Webhooks can be toggled on or off.

## [3.15.0]

### Added

- Added an option to the customizer to toggle the "Continue Shopping" link for an empty
  cart between the home page and the catalog page.
- Added a filter, `bigcommerce/cart/continue_shopping_url`, to modify the destination
  of the "Continue Shopping" link for an empty cart.
- Added additional info about routes for all connected channels to the plugin diagnostic data.
- Added theme support for the WordPress Twenty-Twenty theme and BC4WP.

### Changed

- Added a new class to the product archive template title: `bc-product-archive__title`.

### Fixed

- Addressed an issue in the WP Admin with field heights on fields making them
  not visible.
- Fixed channel site URL to update based on the "Site Address" setting, not the "WordPress Address" setting.
- Fixed options sorting order on select fields for admin settings sections.


## [3.14.0]

### Added

- Added a missing entry to the 3.0.0 changelog regarding fixing the uninstaller.

### Fixed

- When the site url changes, the connected channel site URL is also updated.

### Changed

- Added a new notification in the Diagnostics panel to call out when template
  overrides are being used.
- Enhanced the template for product cards by adding additional classes related
  to product statuses.
    - bc-\[product-id\]
    - bc-availability-*
    - bc-product-sale
    - bc-product-outofstock
    - bc-product-lowinventory

## [3.13.0]

### Added

- Added category and product view limitations to customers that are members of a
  group with product category visibility limitations.
- Added a filter to modify the cache time of user group category visibility.
  `bigcommerce/product_category/group_filter_terms_user_cache_time` accepts a value,
  in seconds, for how long to cache the local list of terms by user.
- Added a new tab to the Resources section in the plugin admin area. The
  new section, Tutorials, contains video tutorial links from BigCommerce's YouTube channel.

### Changed

- Added a feature that scrolls the browser to the top of the embedded checkout iframe
  upon completion of the order. This resolves an issue on smaller screens where
  the window would be stuck at the bottom of the page.

## [3.12.0]

### Added

- Added the ability to sort products arbitrarily in the products shortcode. By setting
  the `sort` parameter to `post__in`, the order of the IDs in the `id` parameter will
  be maintained in the query results.
- Added body classes to front end templates with product ID and availabilty, and flags for
  sale, out of stock, and low inventory products.

### Changed

- The import progress bar will also appear at the top of the final onboarding screen if an
  import is in progress.
- Redesigned the final onboarding page to list the next steps to configure the merchant's
  BigCommerce store. This final page will appear in the admin menu as "Launch Steps" until
  all required steps have been completed.

### Fixed

- Handled a possible exception in the product update webhook handler
- Fixed an undefined variable in the product update webhook handler
- Fixed the product review form handler to respect the value of the
  `bigcommerce/product/reviews/show_form` filter that is used when
  displaying the form.
- Changed the default value for `bigcommerce/gift_certificates/theme` to `general`.
  The previous value resulted in gift certificate reciepients to receive an email
  with no gift certificate template attached.

## [3.11.0]

### Changed

- Onboarding Screen modifications:
  - When submitting the new account form, a loading spinner and message appear letting you know
    we're working on creating your account.
  - The email confirmation notice upon creating a new account is now more visible.
  - On the Channel Selection screen, a new notice was added altering the user
    to the alternate configuration needed for WordPress multisite setup.
- The first channel will be automatically created when registering a new account via the WordPress plugin.
  This store will be configured to automatically add new products to that channel. Additional channels may
  still be created from the settings screen when multi-channel mode is enabled.
- The item count badge on the cart menu item will be enabled when either embedded checkout or the mini-cart is enbled.
  The item count will be included in the mini-cart ajax request and used to update the item count cookie.
- With the release of WordPress 5.3, there were several updates to accessibility in the WP admin including
  form fields, color contrast, and attribute updates. We've done the same with our plugin and increased
  AA compliance throughout the plugin, including onboarding screens, settings pages, and Product UI.

## [3.10.0]

### Added

- Added an option to enable the mini-cart to the nav menu onboarding screen.
- Added support videos to onboarding screens.

### Changed

- Changed product videos to use oEmbed instead of the YouTube API, thereby supporting IE11.

### Fixed

- Handled an inconsistency in Channels and Catalog API responses to ensure that deleted products
  are removed from WordPress.
- Removed erroneous override of product shortcode/block sorting when a sorting argument is explicitly given.
- Updated BigCommerce API client to version 2.0.3 to handle product option sort order.

### Deprecated

- Product gallery template overrides based off of the short-lived 3.9.0 template should continue to function,
  but should be updated for compatibility with future versions. Templates depending on the YouTube API are deprecated.

## [3.9.0]

### Added

- Added the retail price to the product price template. The retail price will only display
  when it is set on the product in the BigCommerce admin.
- Added support for product videos in the product gallery section. The videos will always be YouTube
  videos. We've implemented the YouTube Player API to assist with play and pause features when
  switching between multiple videos.
    - There is a known issue with this YT Player API on IE 11 and videos are currently not playing
      in that browser. This will be addressed in the next release.

### Changed

- `DELETE` requests to the WP REST API are now camouflaged as `POST` requests to work around
  web hosts that do not allow standard HTTP methods.

### Fixed

- Fixed the missing "Cart" section in the theme customizer when visiting the customizer before
  first saving any settings in the plugin admin.

## [3.8.1]

### Changed

- Updated embedded checkout SDK to version 1.37.1 to better handle failures from the cart handover.

## [3.8.0]

### Added

- Created a mini-cart template. There are two ways to display the mini-cart. (1) Use a
  widget to display the mini-cart in a registered sidebar area. (2) Activate the toggle
  in the theme customizer to display the mini-cart when the cart nav menu icon is clicked.
  The contents of the mini-cart will be loaded asynchronously to avoid deleterious effects
  on page caching plugins. The mini-cart has its own set of templates that can be customized
  independently of the full cart templates.
- Added two more WooCommerce function stubs for compatibility with some themes:
  `wc_get_cart_url()` and `wc_get_checkout_url()`. Each will point to the corresponding
  BigCommerce page for cart or checkout. Props to @mark-netalico.

### Fixed

- Removed an erroneous error message that displayed when updating from version 3.5 or earlier.
  If the site's currency matches the store's default currency, no error message should display
  that says the currency is disabled.

### Changed

- If a product variant has an image, and that variant is purchased, the order history
  page will display that variant's image instead of the default product image.
- Clarified 3.6.0 changelog entry regarding assignment of currencies to channels.

## [3.7.0]

### Added

- When using the WordPress password reset form or updated a user's password from the
  WordPress admin, the new password will be synchronized with the BigCommerce customer
  account for users who's accounts are configured to sync with BigCommerce.
- Added an endpoint to handle abandoned cart recovery. Visitors who abandon their
  carts will receive an email with a link that includes a token to recover the cart.
  On clicking the link, the cart will be restored in the user's new browser session.
- Added new route configurations to point BigCommerce-generated links to pages on the
  WordPress site:
  - `create_account` points to the user registration page
  - `forgot_password` points to the lost password page
  - `account_order_status` points to the order history page
  - `account_new_return` points to the shipping & returns page
  - `recover_abandoned_cart` points to the cart recovery endpoint

### Changed

- If a user account is configured to sync with BigCommerce but the customer ID user
  meta is missing (as may happen if the BigCommerce plugin has been uninstalled and
  reinstalled), a matching customer account may be found by email and used for
  password validation.

## [3.6.0]

### Added

- Added currency selection to the settings screen. Any currencies enabled in the
  BigCommerce admin are available to assign as the currency for the WordPress site.
  When in multi-channel mode, a different currency can be assigned to each channel.
- Added support for custom SEO titles and descriptions. The SEO title set in the
  BigCommerce admin will override the HTML title tag for the product single. The
  SEO description set in the BigCommerce admin will be used to render a meta
  description tag in the page header. Note: If using Yoast SEO, custom descriptions
  set in the Yoast meta box will lead to a duplicate meta tag. To remove the tag
  from this plugin, add the following snipped to a theme or plugin:
  ```
  add_action( 'plugins_loaded', function() {
  	remove_action( 'wp_head', bigcommerce()->post_types->product_page_meta_description, 0 );
  }, 100, 0 );
  ```
- Added support for custom image alt text. The alt text set in the BigCommerce admin will
  be imported into WordPress, where WordPress's standard image rendering functions will
  use it as the value for the `alt` attribute.
- Added a new global JS function to the `bigcommerce_config` global JS object. You can now
  retrieve the current user's cart ID via JS using `bigcommerce_config.cart.getCartID();`

### Fixed

- Fixed several locations where "bigcommerce" was misspelled as "bigcomerce" in
  text domains, HTML classes, and filter names.

### Changed

- On uninstall of the plugin, pages related to the plugin will have "-uninstalled"
  appended to their slugs when they are set to draft. If the plugin is reinstalled
  later and these pages still exist, they will be republished and "-uninstalled"
  will be removed from the slugs.
- If a product variant has an image, and that variant is added to the cart, the
  cart page will display that variant's image instead of the default product image.

### Removed

- Currency formatting settings have been removed. Formatting settings are pulled
  from the BigCommerce currencies API. PHP's intl extension will still be used
  for formatting if it is available.

## [3.5.0]

### Added

- Product pick lists will include a "None" option if that modifier field
  is not required.
- Added filters to modify the price range and calculated price range of
  a product. `bigcommerce/product/price_range/data` affects the data
  that feeds both. The formatted output can be modified with the filters
  `bigcommerce/product/price_range/formatted` and
  `bigcommerce/product/calculated_price_range/formatted`
- Added hooks to the initialization of the product importer. Use
  `bigcommerce/import/task_list` to modify the list of tasks for the import.
  Use `bigcommerce/import/task_manager/init` to perform additional setup
  actions on the task manager (e.g., register more tasks).
- Added a control to the theme customizer to toggle display of product
  inventory. Inventory can be shown for all products that track inventory,
  or only for products with low inventory. Inventory display is filterable
  per product using the hook `bigcommerce/product/inventory/should_display`.
- Added a webhook to listen for completed checkouts. The webhook will trigger
  the action `bigcommerce/webhooks/checkout_complete`.
- Added support for a omitting products from initial Pricing API calls if they have
  a `.preinitialized` class added to the pricing DOM node. This will allow 3rd
  party plugins/themes to add this CSS class based on their needs/conditions.
- Added a toggle in the theme customizer to control display of default
  pricing while waiting for Pricing API responses.

### Fixed

- Fixed the destination URLs for webhooks, which were missing a path component.
- Fixed display of error messaging when attempting to log in with an
  invalid user name.
- When running in multi-channel mode, removing a product from one channel
  could remove its images that were shared with the same product in another
  channel. The images will now stay in place and have their parent post
  adjusted to the surviving product.

### Changed

- The template `components/products/product-price.php` has two new variables
  available: `$price_range` and `$calculated_price_range`. This allows
  filtering of those values beyond what was previously possible. Existing
  templates will continue to work with values pulled from the product
  on render.
- The template `components/products/product-price.php` has a new variable,
  `$visible`, to use in place of the `bc-product__pricing--visible` class.
  This controls whether default pricing can be displayed.
- Default sorting of the Product post type archive and taxonomy archives
  has been changed from "Title A-Z" to "Featured". This can be changed using
  the `bigcommerce/query/default_sort` filter.
- "Featured" sorting now considers the Product's sort order, as set in the
  BigCommerce admin. Featured items will appear first, followed by all other
  items sorted by sort order. If the sort order for a product is not set,
  it is treated as a `10`.
- The template `components/products/inventory-level.php` has two new variables
  available: `$status` and `$label`. This simplifies the template, while
  enabling additional filtering of the values. Existing templates overrides
  will continue to work with values pulled from the product on render.
- If embedded checkout is disabled, the current customer's cart count
  will not display on the cart menu item. The behavior is filterable with
  the `bigcommerce/cart/menu/show_count` hook.
- Pricing API requests for unauthenticated users will use `null` as the
  customer group ID, which uses the default guest group, instead of `0`,
  which uses no group.
- When selecting options and modifiers for a product, we're no longer debouncing
  the clicks and then disabling the fields while a Pricing API call is made.
  Fields remain enabled, and changes will cause previous Pricing API
  calls that might be pending to abort.

## Deprecated

- The unused constants `\BigCommerce\Cart\Add_To_Cart::CART_COOKIE` and
  `\BigCommerce\Cart\Add_To_Cart::COUNT_COOKIE` are deprecated and will
  be removed in a future version.

### Removed

- The filter `bigcommerce/template/product/archive/default_sort` has been removed.
  Use `bigcommerce/query/default_sort` instead.

## [3.4.1]

### Fixed

- The Pricing API request cache did not properly differentiate based on
  request parameters, sometimes leading to incorrect prices displaying.

## [3.4.0]

### Added

- Created a filter, `bigcommerce/api/ttl`, to control the TTL of the API
  response cache. Caches now default to one hour, but can be overridden
  per request. Requests to the pricing API no longer force a cache flush.
- Added support for sort order on product modifiers. The order will be imported
  from BigCommerce and respected when rendering the product form.
- Added a step to the import process to delete categories and brands that
  were deleted in BigCommerce. This also resolves the issue of a re-created
  category getting a numeric suffix on the slug when the new term is created.
- **Wish Lists**
  - BigCommerce Wish Lists are now part of user accounts.
  - You can create, edit and delete a Wish List as well as add and remove items.
  - You and make your list public or private. Private lists cannot be shared
    and can only be seen by the list owner.
  - Shared Wish Lists are presented as a filtered version of the main product
    archive.
  - Deleting a Wish List cannot be undone. You will be given a confirmation
    screen before deleting.
  - Wish Lists are currently product ID based and not quantity based.
    Attempting to add the same product to your list will not increase the
    quantity nor will it duplicate the product in your list. Only one instance
    of a product variant is allowed in a Wish List.

### Fixed

- Product categories and brands will now derive their slugs from the
  "Custom URL" property set in the BigCommerce admin. If a category name
  changes, the WordPress term slug will update to reflect the new name.
- Gutenberg blocks were not able to utilize the drag-and-drop feature
  due to a conflict with a JS library. This lib has been upgraded and
  drag-and-drop functionality has been restored.
- Products options and modifiers that contained only a single option would
  cause the Cart/Buy button to be disabled permanently. This has been been
  fixed.
- The user meta key `bigcommerce_nav_settings_initialized` was not correctly
  removed when running the plugin uninstaller. This has been fixed.
- Removed the product condition from cart and order history when the product
  is not configured to display its condition.
- Disabled WordPress's kses filters when storing data in the import queue.
  This fixes mangled HTML in some circumstances. Post content is still filtered
  later in the import.
- Upgraded Lodash JS library dependency to the latest version.

### Changed
- Refinery template (`components/catalog/refinery.php`) has removed the
  wrapper element in favor of the Controller variables. Update your template
  as needed.
- The Gutenberg Product Component Block now has admin previews that contain
  rendered HTML product content for all component types. When a product ID
  is not found or is no longer valid, a default message will display letting
  you know the product is unavailable on the admin side and the theme side.

### Deprecated

- API factory methods for building widget-related clients are deprecated.
  Calls to `Api_Factory::placement()`, `Api_Factory::themeRegions()`, and
  `Api_Factory::widgetTemplate()` should be replaced with `Api_Factory::widget()`.
  These methods will be removed in a future version.

## [3.3.0]

### Changed
- HTML elements can now be used in all text string filters so long as it
  passes the same `wp_kses` rules used on comments. Previously, HTML inserted
  via filters for text strings was omitted in certain cases.
  For instance, on the ajax add to cart message.


## [3.2.0]

### Added
- Display variant images in the product gallery. Once a specific variant
  is selected, its image will be used as the primary gallery image.
- Added stack traces to the error log when exceptions are thrown during
  the import process
- Added a screen at the end of the onboarding process to guide merchants
  to their next steps.
- Added support for pre-order and non-purchaseable product availability
  settings. Includes two new theme customizer fields to control the text
  for the "Add to Cart"/"Buy Now" buttons when displaying pre-order products.
- Added support for products with hidden prices. They will use the new
  template `components/products/product-hidden-price.php` to not show the price.
- Added a new option for the Product Components block/shortcode for Add To Cart.
  This will output the Add to Cart/Buy Now button and form on any page, including
  variants if applicable. Example Usage: `[bc-component id="431" type="add_to_cart"]`
- Added a new Channel Select field in the product picker UI in the WP Admin. You can
  now search for products from any channel you've connected to your WP Store. Selecting
  a channel will initiate a new query immediately and subsequently only produce product
  search results from that channel.

### Fixed
- Fixed import of product descriptions containing HTML links. WordPress
  core was adding `noopener` attributes prematurely, breaking the JSON
  data stored in the queue.
- Fixed a fatal error when updating to version 3.0+ from 2.2.1 or earlier
  while a product import is in progress.

### Changed
- Pass through custom error messages from the API when Add to Cart fails.
  Previously we were returning `null` and using a generic message. We're still
  using the generic message but only when a more specific message is not
  available from the API.
- The template `components/products/product-gallery.php` has changed to
  accommodate variant images.
- The template `components/products/product-form.php` has a new variable
  `$message` containing the pre-order message to display for the product.
- The `bigcommerce/customer/group_id` filter will be called from
  `\BigCommerce\Accounts\Customer::get_group_id()`, even when the current
  user is logged out or does not have a customer ID.

## [3.1.0]

### Added
- Added a button to reset channel listing overrides, enabling editors to
  reconnect a product to the base product for future updates.
- Added a button to immediately re-sync a product. Editors can import the
  latest changes from the BigCommerce API for that product without running
  a full import on the entire catalog.
- Added a missing entry to the 3.0.0 changelog regarding fixing the uninstaller.
- Added a method to fetch customer group info. `$customer->get_group()->get_info()`
- Created an option to toggle synchronization of analytics settings.
- Added a customizer option to disable Quick View on product cards. When disabled,
  product card images are wrapped in a link to the product single.
- Added a template for the sku component of a product, `components/products/product-sku.php`.
- Added a new shortcode/block that allows selection of distinct product
  components. Example usage: `[bc-component type="description" id="117"]`.
  Valid types are: sku, description, image, title.
- Added an option to disable sync of analytics IDs.

### Fixed
- Fixed missing markup for AMP templates. Some valid tags had been
  erroneously stripped out.

### Changed
- Added a new parameter to the product title template (`components/products/product-title.php`)
  to set the header level. It should set the header appropriately
  for the context in which the component is loaded (h1 for the product
  single, h2 for the shortcode/block single, and h3 for the product card).
- Moved the Quick View markup from `components/products/product-card.php` to
  a new template, `components/products/quick-view-image.php`.
- Removed the wrapper div from the template `components/products/product-quick-view.php`.
  The wrapper will be added by the template controller.
- Changed the `assign_terms` capability for categories and brands to `do_not_allow`.
  Assignments would be overwritten by the next import. This keeps it from
  happening in the first place to avoid confusion.
- Turned off the `autocomplete` attribute on API fields in the settings
  page. This will help to avoid an issue where autocomplete causes a change to
  user credentials and causes the store to disconnect when settings are saved.
- Updated the product quick view template, `components/products/product-quick-view.php`,
  uses the new product sku component.
- Updated the product shortcode single template, `components/products/product-shortcode-single.php`,
  uses the new product sku component.
- Updated the product single template, `components/products/product-single.php`,
  uses the new product sku component.
- Updated the onboarding flow to indicate where the user is in the setup process. Additionally,
  some styles were updated on the content areas, buttons, and start-over feature.
- Optimized the import process to avoid fetching data for products not listed
  in any active channels.

### Deprecated
- Deprecated the `$quickview` and `$attributes` variables in the template
  `components/products/product-card.php`. The variables are now empty, and
  will be removed in a future version.

## [3.0.2]

### Fixed
- Updated API client library to 1.12.1 to fix error in class definition
  for PriceRecord.

## [3.0.1]

### Fixed
- Fixed duplicate posts created in WordPress when importing products
  in draft or pending status.

## [3.0.0]

### Added
- Added support for connecting to multiple BigCommerce channels. Since the
  plugin can't know the particular use case a store has for using multiple
  channels, we provide the base framework for site developers to extend
  in a way that makes sense for their business. Multi-channel support
  requires opt-in using a filter:
  ```
  add_filter( 'bigcommerce/channels/enable-multi-channel', '__return_true' );
  ```
  This will enable an admin to connect to multiple channels on the settings
  screen. The primary channel will still be used for all front-end requests
  unless filtered to use a different channel. Example:
  ```
  add_filter( 'bigcommerce/channel/current', function( $channel ) {
    // do some logic here to determine what channel to use
    return get_term( 697, \BigCommerce\Taxonomies\Channel\Channel::NAME );
  });
  ```
- Created a taxonomy for storing channels. Most stores will only have one,
  but a store with multi-channel enabled may have many. The taxonomy's UI
  is hidden, and it is only exposed during onboarding (when selecting the
  initial channel) and in the Channel Settings section when multi-channel
  is enabled. All products are associated with a channel term on import.

### Changed
- Updated the position and design of the start-over button and confirmation
  message for the on-boarding process.
- Fixed display of tax and total price in order history when an order was
  paid for entirely with store credit. It should no longer show a negative
  subtotal, and the grand total should properly display as $0.00 (formatted
  appropriately for the local currency).
- Currency can now be filtered at any point in the request. Previously,
  the currency would be locked in place when creating the formatter
  in the Currency service provider. The service provider will now use
  a factory method to return a formatter based on the current, possibly
  filtered, value of the `\BigCommerce\Settings\Sections\Currency::CURRENCY_CODE`
  option. Use the `pre_option_bigcommerce_currency_code` filter to adjust
  the currency in use at any given point in the request.
- The import process has changed to more efficiently support imports when
  connected to multiple channels. After fetching listings for each channel,
  products will be fetched from the Catalog API _once_. When fetching products
  in bulk, we now also fetch options and modifiers, taking advantage of
  new capabilities of the API. This limits the bulk import to 10 products
  per request, but saves two additional queries per product later in the import.
  The tasks to fetch listings and initialize channels now have a suffix
  of the channel ID on the string used to trigger the task status.
- The import queue is now stored as hidden posts in the `wp_posts` table,
  using the post type `bigcommerce_task`.
- Method signature for the `\BigCommerce\Import\Importers\Products\Product_Builder`
  constructor has changed. It now expects a \WP_Term representing the channel
  the product belongs to.
- Method signature for the `\BigCommerce\Import\Importers\Products\Product_Importer`
  constructor has changed. It now expects a \WP_Term representing the channel
  the product belongs to instead of a channel ID and an instance of the Channels API.
- Method signature for the `\BigCommerce\Import\Importers\Products\Product_Saver`
  constructor has changed. It now expects a \WP_Term representing the channel
  the product belongs to.
- The order of operations in `\BigCommerce\Import\Importers\Products\Product_Saver`
  has changed to assign terms to a product before setting its post data
  or post meta. This is to support multiple products with the same slug
  but in different channels.
- Method signature for the `\BigCommerce\Import\Importers\Products\Product_Strategy_Factory`
  constructor has changed. It now expects a \WP_Term representing the channel
  the product belongs to.
- Product price ranges are now calculated on import and stored in post meta
  rather than relying on values from the bc_variants table on render.
- Moved `\BigCommerce\Import\Review_Builder` to
  `\BigCommerce\Import\Importers\Reviews\Review_Builder`.
- Moved `\BigCommerce\Import\Review_Fetcher` to
  `\BigCommerce\Import\Importers\Reviews\Review_Fetcher`.
- Method signature for the `\BigCommerce\Import\Processors\Channel_Initializer`
  constructor has changed. It now expects a \WP_Term representing the channel
  to initialize.
- Renamed `\BigCommerce\Import\Processors\Listing_ID_Fetcher` to
  `\BigCommerce\Import\Processors\Listing_Fetcher` and changed its constructor
  signature to require a \WP_Term representing the channel for the listings.
- Renamed `\BigCommerce\Import\Processors\Product_ID_Fetcher` to
  `\BigCommerce\Import\Processors\Product_Data_Fetcher`.
- Changed the method signature for the `\BigCommerce\Import\Processors\Queue_Runner`
  constructor. It no longer requires an instance of the Channels API.
- Moved `\BigCommerce\Merchant\Routes` to `\BigCommerce\Taxonomies\Channels\Routes`

### Fixed
- Updated registration of block editor plugins to work with recent
  versions of Gutenberg.
- Fixed fatal error in the uninstaller from an undefined class constant.

### Removed
- Removed the `bc_products` table. All queries that used this table have been
  updated to use post meta.
- Removed the `bc_variants` table. All queries that used this table have been
  updated to use post meta.
- Removed the `bc_import_queue` table. All import tasks have been moved to
  the `wp_posts` table.
- Removed the `bigcommerce/pricing/channel_id` filter. The pricing API request
  will now derive the value from the current channel.
- Removed the `bigcommerce/pricing/currency_code` filter. The pricing API
  request will now derive the value from the `bigcommerce_currency_code` option.

### Deprecated
- The `post_id` field in the `bc_reviews` table is no longer used and
  will be removed in a future release.

## [2.2.1]

### Changed
- Updated the way cached pricing works while requesting new data from the
  Pricing API. We're now displaying the cached pricing first. We've also
  removed the spinner while loading pricing data and replaced with a simple
  fade in/out of prices.

## [2.2.0]

### Added
- Added a redirect to the Welcome/Settings screen on plugin activation

### Changed
- Updated the BigCommerce Checkout SDK to version 1.18.10

### Fixed
- Fixed a layout issue on the WP Admin BigCommerce Resources page where
  resource cards were misaligned.
- Fixed AMP validation errors from improperly included scripts. Thanks, @westonruter!


## [2.1.0]

### Added
- Uses the new BigCommerce pricing API to retrieve more accurate pricing
  data based on selected product variants, options, and customer groups.
  The original price display feature still exists and is now the fallback
  if unable to fetch live pricing.
- Added a setting to determine whether prices should display with tax
  included or excluded. This duplicates a setting from the BigCommerce
  admin that is not available via the Store API.
- Added the `customer_group_id` property to the customer profile object
  retrieved from the Customer API.
- Added a routine to make BigCommerce nav menu meta boxes visible to users
  by default.
- Added webhooks to listen for product inventory updates. A received webhook
  will schedule an immediate cron job to pull updated product data from
  the Catalog API.

### Changed
- Upgraded Checkout SDK to version 1.18.5
- Reduced the prominence of the checkout requirements notification when on
  admin pages unrelated to the BigCommerce plugin.
- Updated the template `components/products/product-price.php` with markup
  to support Pricing API ajax requests.

## [2.0.1]

### Fixed
- Handled the new firing order of block-editor related hooks in transitioning
  from the Gutenberg plugin to WordPress 5.0+. This fixes a PHP warning
  and some odd behavior from other plugins that register meta areas
  for the block editor.


## [2.0.0]

### Added
- Added new links and buttons to the Products custom post type edit screen
  and the BigCommerce Settings page for managing your products on BigCommerce
  and logging in to your account
- Added a Resources page to the BigCommerce admin section. The Resources page
  contains tab separated content that provides users with a repository of
  themes, plugins, apps, and support links to enhance or extend their
  BigCommerce for WordPress installation.
- Added an option to create a new menu in addition to selecting a preexisting
  menu on the Menu Select screen during on-boarding.
- Added an option to configure channel settings for new products during
  on-boarding. The channel selection screen is always shown now, even
  for new accounts that don't yet have a channel.
- Added an option to the on-boarding process to choose between a
  full-featured store and one directed more towards bloggers. This
  sets default settings depending on your choice.
- Added a filter for customer profile fields fetched from the API:
  `bigcommerce/customer/empty_profile`
- Added a filter to wrap the output of a template. Can be used to prepend
  or append content to the template:
  `bigcommerce/template={$template}/output`
- Added a template for the checkout button on the cart

### Changed
- Updated the error handling and response messages related to the product
  sync feature. We now provide more information to the user based on the
  type of error that has occurred.
- Added the product SKU to post meta, so that catalog searches can use
  WordPress meta queries.
- If the option to automatically add products to the channel is disabled,
  it will be honored even on the initial import when the channel has no
  products.
- Removed product pick list options for products that are out of stock.
- Changed how option and modifier fields are rendered and treated on
  the product single and Quick View modals. Modifiers using select/radio
  fields are now supported, using the same templates as the option fields.

  **NOTE:** Please take note of the changes to the option field templates and
  adjust your custom templates as needed.

  `components/modifier-types/modifier-checkbox.php` → `components/option-types/option-checkbox.php`

  `components/modifier-types/modifier-date.php` → `components/option-types/option-date.php`

  `components/modifier-types/modifier-number.php` → `components/option-types/option-number.php`

  `components/modifier-types/modifier-text.php` → `components/option-types/option-text.php`
- The template `components/cart/cart-actions.php` now takes an array of
  rendered `$actions` that will be echoed into the template.
- The template `components/products/product-card.php` requires a new
  attribute on the Quick View template wrapper: `data-quick-view-script=""`

### Fixed
- Fixed a typo on the Create New Account screen during on-boarding
- Fixed an issue with Quick View modal boxes in product cards where removing
  the quick-view feature would break the JS and the page.
- The nonce for an ajax import request is validated before triggering the
  import cron action.
- Fixed an extra quote rendered in template wrappers.
- Fixed Flatpickr library issue with quick-view modal. **NOTE:** This changes the position
  of the date picker to inline with the date field. Update your CSS as needed.

### Deprecated
- The `modifiers` parameter to the cart REST controller is no longer used
  and will be removed in a future version.
- The `modifiers` variable in the template `components/products/product-form.php`
  is no longer used and will be removed in a future version.
- The template `components/products/product-modifiers.php` is no longer used.


## [1.6.0]

### Added
- Added a Menu Setup screen to the onboarding flow, giving merchants an
  opportunity to quickly add BigCommerce menu items to their navigation menu.
- A "Start Over" button is available in the onboarding screens, enabling
  the merchant to go back to the beginning of the account connection process.
- All option caches are flushed before and after running an import batch
  to avoid cache corruption from longer-running processes.
- The import log records more extensive debugging information. Use the
  `bigcommerce/logger/level` filter to change the logging level.
- Added hooks to render HTML inside the form tags on the plugin settings pages.
- Added child categories to the filter options in the product block/shortcode UI.

### Changed
- The import debug log moved from `uploads/logs/bigcommerce/import.log` to
  `uploads/logs/bigcommerce/debug.log`.

### Fixed
- Cleaned up a small memory leak in the product block/shortcode UI pagination.
- Fixed the check for an expired import lock when running an import via ajax.
- Fixed arguments to Channel Listings API requests to ensure that all products
  are returned even with larger batch sizes.
- Fixed a fatal error when intializing an import on PHP 7.0+.


## [1.5.0]

### Added
- The product selection popup in the admin for the products shortcode/Gutenberg
  block will now load additional pages of products as you scroll past the initially
  loaded products matching your query.
- When completing an embedded checkout, the customer's cart cookie is now
  cleared out so the cart menu item no longer shows items in the cart.
- Product Categories and Brands have new dynamic nav menu items to show top
  level terms in those taxonomies.
- Added synchronization back to BigCommerce when updating the Google Analytics
  tracking ID when GAEE is enabled.
- Added product categories and thumbnails to the admin list table for Products.
- Product Categories and Brands are visible (but not editable) in the WordPress admin.

### Fixed
- Fixed a JavaScript console error when initializing Gutenberg blocks, when
  some of those blocks should be disabled.
- Fixed the broken cancel button when reloading the customer address form
  after validation errors.
- Fixed styles in the products Gutenberg block, because WordPress doesn't
  call it Gutenberg anymore.
- Fixed broken synchronization of Facebook Pixel configuration between WordPress
  and BigCommerce.
- The product sync should no longer show a success message if an import failed.
- Fixed the featured image displayed for gift certificates in a customer's order history.
- Removed the "Required" asterisk from the Company Name field in the address form.
  It is not, in fact, required.

### Changed
- Changed the polling logic for the product import to prevent running multiple
  requests at the same time from a single browser window.
- The Product Category dropdown on the Products archive will now show hierarchical
  terms nested under their parents.


## [1.4.2]

### Fixed

- Fixed inconsistent defaults for ajax cart setting.

## [1.4.1]

### Fixed

- Fixed PHP fatal error that would occur intermittently when adding items
  to the cart.

## [1.4.0]

### Added

- On sites where the official AMP plugin for WordPress is active and SSL is not enabled,
  added an admin notice on the settings page informing users some features won't work
  correctly without HTTPS.
- In AMP, added cart item count indicator to nav menu item linking to Cart page
- Option to use Ajax to add products to the customer's cart
- Added the BigCommerce product ID to the products list in the WordPress admin
- Option to control the import batch size

### Fixed

- Fixed icons not loading correctly in AMP templates.
- Fixed as issue with product variants not working on single product shortcodes.
- Fixed an issue with field labels and IDs colliding when products are duplicated on
  the same page. All clicks would control the first product on the page.
- Fixed click behavior on product galleries
- Fixed bug allowing an import to start (and fail) before setting up a channel
- Fixed 1970 dates showing on order history when orders had not been shipped
- Fixed bug that ignored the minimum quantity requirements when adding a product to the cart
  from a product card.
- Fixed an issue with pagination ajax on shortcode product groups.

### Changed

- Updated BigCommerce v3 API client library to version 1.3.0. Parameters
  have changed on most methods to accept an array of arguments instead of
  a long parameter list.
- Refactored import task registration to use a filterable list. Instances
  of `Task_Definition` should be registered with the `Task_Manager` with an
  appropriate priority to control the order of operations.
- Changed the action that triggers each step of the import. It now uses the
  `bigcommerce/import/run` hook, with the current status as the first parameter,
  replacing the former `bigcommerce/import/run/status={$current_status}`.
- Separated category and brand imports into separate import steps, allowing
  for bulk queries and reducing the number of API requests required to import
  a product.
- Reorganized classes related to the API import process.

## [1.3.0]

### Added

- Added templates, styles, and plugin logic for compatibility with the Official AMP
  Plugin for Wordpress, through version 1.0. Themes still need to be made AMP-compatible
  if not using AMP classic mode.
- Added REST endpoints to proxy several BigCommerce API endpoints, including catalog,
  channels and cart. Most requests are cached for ten minutes by default.
- Added creation and handling of a BigCommerce webhook to bust cached proxy data
  related to a product when the product is updated in BigCommerce.

## [1.2.0]

### Added

- Product prices will update dynamically to reflect the price of the selected variant.
- Reintroduced the ability to set API credentials without using the connector app.
- Added logs for import errors, viewable through the plugin diagnostics section
  on the plugin settings screen.
- Added support for links directly to product variants.
- The import process will continue to run via ajax requests while an admin is
  on the plugin settings screen. This can speed up import processing on sites
  that depend on WordPress cron jobs for the import.

### Changed

- Refactored Gutenberg block registration to re-use code and allow more
  configuration when registering the blocks in PHP.
- Font sizes use relative units instead of pixels.
- Increased quantity field width to accommodate three digits.

### Fixed

- Fixed compatibility with newer (4.4+) versions of Gutenberg.
- Updated the `$_COOKIE` superglobal immediately on setting the cart cookie.
- Fixed a PHP error when the BigCommerce tax class API returns an invalid value.
- Added decimal precision to price sorting queries, fixing sorting for products
  that round to the same integer value.
- Improved accessibility and keyboard navigation on the plugin settings screen.

## [1.1.0]

### Added

- Created a new template tag, shortcode, and block for displaying product reviews.
  Shortcode usage: `[bigcommerce_reviews id={product ID}]`. Template tag
  usage: `echo \BigCommerce\Functions\product_reviews( $product_id );`
- Added a plugin diagnostics section to the settings screen.
- Added a static method to retrieve a Product object by product ID. Usage:
  `$product = \BigCommerce\Posts_Types\Product\Product::by_product_id( $product_id );`

### Changed

- Template may now have a wrapper HTML element that cannot be modified with
  a template override. This wrapper is defined in the template controller
  class associated with the template. Filters `bigcommerce/template/wrapper/tag`,
  `bigcommerce/template/wrapper/classes`, and `bigcommerce/template/wrapper/attributes`
  are available to modify this wrapper. Modification may break JavaScript
  provided by the plugin. We have added comments next to other HTML
  elements that are required to maintain JS functionality.

### Fixed

- Better error handling when the OAuth connector gives unexpected responses.

## [1.0.2]

### Changed

- Added even more sanitization to meet wordpress.org plugin review guidelines

## [1.0.1]

### Changed

- Added an additional layer of sanitization to all user input to meet wordpress.org
  plugin review guidelines
- Replaced bundled jQuery with calls to WordPress's default jQuery

## [0.15.0]

### Added

- Automatically add new products to the BigCommerce channel on next import

### Fixed

- Fixed an error in channel initialization that limited channels to the first 100 products

### Removed

- Removed the missing SSL notice from the admin. Instead, a smaller notice is
  displayed next to the Embedded Checkout option, explaining why it is disabled.

## [0.14.0]

### Added

- Introduced embedded checkout, with an option in the admin to disable it.

### Fixed

- Fixed a typo in the order summary template path. Order history should load correctly now.
- Replaced obsolete information regarding API credentials in the plugin readme.

## [0.13.0]

### Added
- Created account creation and authentication process
- Required creation of a Channel before importing products
- Added two-way sync for Product post status, title, and description with the linked Channel
- Added support for the BigCommerce Sites & Routes API
- Added admin notice when the BigCommerce account is not sufficiently configured to support checkout

### Changed
- Refactored the Cart template into several smaller components
- Moved theme templates from `public-views` to `templates/public`
- Organized theme templates into subdirectories
- Moved admin templates from `admin-views` to `templates/admin`
- Refreshed the list of countries and states used in address forms
- Updated BigCommerce PHP API to version 0.13.0
- Refactored template controller instantiation to add additional filtering for both the path and the controller class.
- Refactored settings sections into the namespace `BigCommerce\Settings\Sections`
- Refactored settings screens into the namespace `BigCommerce\Settings\Screens`
- Changed checkout login token generation to use the OAuth connector API

### Removed
- Removed the API Credentials settings section. All authentication should now go through the OAuth authentication process.
- Removed ability to edit Product post slug. The slug is imported from the Catalog API.
- Removed the Import Settings metabox obviated by the Channels API.

### Fixed
- Fixed the cart tax total to refresh when cart quantities change.

## [0.12.0]
### Added
- Added the Product Sync feature to Product List page.
- Added Welcome screen and Connect Account screen.

### Changed
- Refactored JS code in Gutenberg modules to use ES6 React syntax (removes usage of `wp` global React wrapper).
- Refactored other JS modules for extendability and moved i18n strings to PHP JS_Config.
- Reorganized JS modules and structure for easier readability.
- Added a new indicator in the Gutenberg products block to let the user know if they chose filters that produce no results.
- Added support for displaying estimated tax amounts in the cart.
- Refactored Analytics data tags to utilize Segment Analtyics.js script.
- Improved focus pointer UX elements when editing the product block.
- Rendered redesigned panels in Settings UI.
- Refactored settings screen registration and rendering.
- Prevented editing API credential settings if they are set using constants or environment variables.

### Removed
- Replaced GA/Pixel controller with Segment controller.

### Fixed
- Fixed a bug with the Gutenberg editor where the Featured filter was not showing up when reopening a saved block.
- Fixed a bug with the cart template where product removal was canceled by a missing template node.
- Fixed a bug with the cart where updating product qty was updating the remote cart but the API response changed causing an ajax error.
- Fixed a bug with product pages where  product review body text was not showing. Existing products should be re-imported to show reviews.
- Fixed an issue with the admin Products UI where default settings were not being applied when using the classic editor.

## [0.11.1] - 2018-08-28
### Fixed
- Remove reference to `Id` from the Gutenberg blocks `props` Object which was deprecated in version 3.3. Replaced with new key `clientId`.

## [0.11.0] - 2018-08-27
### Added
- Changelog
- Support for most product modifier field types: checkbox, date, number,
  single line text, multi line text
- Quantity field for the Add to Cart form
- Shim for the function wp_unschedule_hook(), which is not available on
  WordPress versions older than 4.9.0
- Updates to the uninstaller to account for data added in recent releases

### Changed
- Combined the "Product Archive" and "Product Catalog" sections in the theme customizer
- Updated the BigCommerce API SDK to bring it up-to-date with current API behavior.
  API classes formerly in the `BigCommerce\Api\v3` namespace have moved to
  `BigCommerce\Api\v3\Api`.
- When changing the import schedule, the next import is immediately rescheduled,
  instead of waiting until the next import runs.
- When running an import via CLI, reschedule the next cron import after
  the CLI import completes.

### Removed
- The class `BigCommerce\Customizer\Sections\Catalog` is gone. Its constants
  have moved to `BigCommerce\Customizer\Sections\Product_Archive`.

## [0.10.0] - 2018-08-03
### Added
- Rendering of form error messages that do not correspond to specific fields
- Timeout handling for "Load More" buttons
- Fallback image for gift certificates in cart and order history
- Automatically create Shipping & Returns page
- Placeholder graphics for all Gutenberg blocks
- Links to product pages from cart and order history
- Product review pagination
- Display option swatches with images or multiple colors
- Link from WordPress admin to BigCommerce admin to manage product reviews
- Option to disable reviews on a product, tied to WordPress's comment toggle

### Changed
- Product gallery thumbnail images will wrap after four thumbnails

### Fixed
- Render inline content for newly-created product blocks

## [0.9.0] - 2018-07-19
### Added
- Import product reviews and render on product pages
- Product review form
- Gift certificate purchasing
- Automatically create page for purchasing gift certificates, with the
  new shortcode `[bigcommerce_gift_form]`
- Automatically create page for checking gift certificate balances, with
  the new shortcode `[bigcommerce_gift_balance]`
- Settings for Facebook Pixel and Google Analytics tracking IDs
- Automatic import of Facebook Pixel and Google Analytics tracking IDs from
  the BigCommerce store
- Two-way sync of Facebook Pixel tracking code with the BigCommerce store
- Render Facebook Pixel and Google Analytics tracking codes
- Tracking for add to cart and view product events
- Settings for ordering and pagination in the product shortcode/block interface

### Fixed
- Manually reset the global `$post`, because `wp_reset_postdata()` does not,
  in fact, reset postdata, so far as Gutenberg 3.2.0 is concerned.


[4.5.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.5.0...4.5.1
[4.5.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.4.0...4.5.0
[4.4.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.3.1...4.4.0
[4.3.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.3.0...4.3.1
[4.3.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.2.0...4.3.0
[4.2.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.22.0...4.0.0
[3.22.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.21.0...3.22.0
[3.21.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.20.0...3.21.0
[3.20.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.19.0...3.20.0
[3.19.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.18.1...3.19.0
[3.18.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.18.0...3.18.1
[3.18.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.17.0...3.18.0
[3.17.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.16.0...3.17.0
[3.16.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.15.0...3.16.0
[3.15.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.14.0...3.15.0
[3.14.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.13.0...3.14.0
[3.13.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.12.0...3.13.0
[3.12.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.11.0...3.12.0
[3.11.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.10.0...3.11.0
[3.10.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.9.0...3.10.0
[3.9.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.8.1...3.9.0
[3.8.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.8.0...3.8.1
[3.8.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.7.0...3.8.0
[3.7.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.6.0...3.7.0
[3.6.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.5.0...3.6.0
[3.5.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.4.1...3.5.0
[3.4.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.4.0...3.4.1
[3.4.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.3.0...3.4.0
[3.3.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.2.0...3.3.0
[3.2.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.1.0...3.2.0
[3.1.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.0.2...3.1.0
[3.0.2]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/2.2.1...3.0.0
[2.2.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/2.0.1...2.1.0
[2.0.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.6.0...2.0.0
[1.6.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.5.0...1.6.0
[1.5.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.4.2...1.5.0
[1.4.2]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.4.1...1.4.2
[1.4.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.15.0...1.0.1
[0.15.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.14.0...0.15.0
[0.14.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.13.0...0.14.0
[0.13.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.12.0...0.13.0
[0.12.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.11.1...0.12.0
[0.11.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.11.0...0.11.1
[0.11.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.8.0...0.9.0
