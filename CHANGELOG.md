# Changelog

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
[0.11.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/bigcommerce/bigcommerce-for-wordpress/compare/0.8.0...0.9.0