# Changelog

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


[Unreleased]: https://github.com/moderntribe/bigcommerce/compare/master...develop
[0.15.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.14.0...0.15.0
[0.14.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.13.0...0.14.0
[0.13.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.12.0...0.13.0
[0.12.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.11.1...0.12.0
[0.11.1]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.11.0...0.11.1
[0.11.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.8.0...0.9.0
