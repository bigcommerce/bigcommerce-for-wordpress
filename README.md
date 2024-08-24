# BigCommerce for WordPress

## Plugin Setup

As with any WordPress plugin, upload the plugin ZIP file to the
`plugins` directory and activate via the WordPress admin or WP-CLI.

### System Requirements

* PHP: 7.4+
* MySQL: 5.6+ (or MariaDB version 10.1+)
* WordPress: 5.8+
* SSL
* The PHP intl extension will enable better currency formatting
* Support for tmpfile() function in php.ini

### Assets build

* Run `nvm use`
* Run `yarn install`
* Run `grunt build` for production and `grunt` for development

### Settings

Find the BigCommerce settings screen at BigCommerce -> Settings in the WordPress
admin menu.

#### Product Sync

Once authenticated with the BigCommerce API, the plugin will import products.
This will run automatically using WordPress cron, using the schedule
set on the settings page (default: daily).

If you choose to disable the cron job, you can set a server-side cron job to run the sync
using WP-CLI. The command to import products is:

```
wp bigcommerce import products
```

Product titles, descriptions, and post statuses can be edited in the
WordPress admin. Your changes will be automatically synced with your
BigCommerce channel and preserved during future imports.

#### Cart Settings

When the cart is enabled, visitors to your store can add products to carts before checkout.
If it is disabled, the "Add to Cart" button becomes a "Buy Now" button, sending customers
directly to checkout for that product.

When the cart is enabled, the plugin will automatically created a page to host the cart
shortcode, `[bigcommerce_cart]`. This shortcode will show the current visitor's cart. To
change this to a different page, first create a page with the cart shortcode, then come
back to the settings page and select it from the dropdown.

**Important Note:** the cart page should be excluded from any page caching system
enabled for your site.

#### Currency Settings

The store's default currency code will be imported from the BigCommerce API as part of the product
import process.

If the PHP intl extension is available on your server, there is nothing else to configure.
If it is not available, you will be presented with additional fields to control
currency formatting. These will also be populated automatically from the API.

**Important Note:** Currency format settings are for display only and will not affect
price conversion. Prices will be imported according to the default currency (the currency
in which prices were entered).

Currency formatting can be filtered using the `bigcommerce/currency/format` filter.

#### Accounts and Registration

User accounts in WordPress will be connected to customers in BigCommerce. If user
registration is enabled in WordPress (the "Anyone can register" checkbox at
Settings -> General in the WordPress admin), customers will be able to register
accounts and manage their profiles.

The plugin requires several pages to support user account management. These pages will
all be created automatically. Each will contain a shortcode that renders the relevant
content. If any are deleted, they will be automatically re-created for you. If you have
multiple pages with these shortcodes, you may select which will be treated as
canonical using the dropdowns in this settings section.

The "Support Email" field will be used to give customers an address to contact you
with questions about orders.

**Updating Account Information:** In order to update account information, including a users
password and have it syncronize to BigCommerce you will need to ensure you have global 
PHP tmpfile() function enabled. Please not this can be disabled on some hosting providers.

### Theme Customizer

The visual presentation of your store can be customized using the WordPress theme
customizer. Open the theme customizer and find the "BigCommerce" panel, which contains
several sections.

**Buttons:** Control the labels applied to buttons for interacting with products.

**Colors & Themes:** Customize colors to better match your theme.

**Catalog Pages:** Control the presentation of lists of products.

**Product Single:** Control the presentation of individual product pages.

**Product Archive:** Customize labels and filters for the product archive.

### Navigation Menus

This plugin creates several pages, and they can be added to the WordPress navigation menus
using the standard WordPress admin (Appearance -> Menus, or in the theme customizer). Some
of these pages are treated specially when they are added to navigation menus.

**Cart:** The cart menu item will show the number of items in the current user's cart.

**Login:** If the user is logged in, the login page menu item becomes a link to log out.

**Register:** If user registration is disabled, the menu item will be disabled (it won't
render on the front end of the site). If the user is logged in, the menu item will link
to the user's account profile.

**Account Profile, Order History, Addresses:** If the user is not logged in, these menu
items will be disabled (they won't render on the front end of the site).

## Product Import

Products are imported from the BigCommerce API on a WordPress cron job, or using a WP-CLI
command: `wp bigcommerce import products`

The import runs in several stages:

1. If your channel does not currently have any listings, all products from
the store will be added to the channel.
1. A list of all products from the channel is added to a queue for processing.
1. Any products on the site that are no longer available in the BigCommerce channel are
marked for deletion.
1. The queue is processed in chunks (five items at a time), whereby products are imported,
updated, or deleted to match the data in BigCommerce.
1. Currency and other store information from the API updates settings in WordPress.

## Shortcodes

Most of the plugin's functionality is exposed on the front-end of the site through
shortcodes embedded on automatically created pages. The code controlling those shortcodes
can be found in the classes in `src/BigCommerce/Shortcodes`.

### Products Shortcode

`[bigcommerce_product]`

The product shortcode can be used to include one or more products on other pages or
posts on the WordPress site. Click the "Add Products" button above the editor to
select products or build a dynamic query to include in the page.

While the shortcode can be built using the button, it can also be created manually. It
accepts a number of optional attributes:

`id` - A comma delimited list of BigCommerce product IDs

`post_id` - A comma delimited list of WordPress product post IDs

`sku` - A comma delimited list of BigCommerce product SKUs

`category` - A comma delimited list of Product Category slugs

`brand` - A comma delimited list of Brand slugs

`featured` - Set to 1 to limit the query to featured products

`sale` - Set to 1 to limit the query to sale products

`recent` - Set to 1 to limit the query to products imported in the last 2 days (filter
the duration with the `bigcommerce/query/recent_days` filter)

`search` - A search string to match against product titles, BigCommerce product IDs,
or SKUs

`paged` - Set to 0 to disable pagination

`per_page` - The number of products to show per page. Defaults to the value set in the
theme customizer.

`order` - Whether to sort products in "ASC" or "DESC" order

`orderby` - Which field to use for sorting. Accepts any field that WP_Query accepts (e.g.,
title, date)

### Other Shortcodes

`[bigcommerce_signin_form]` - The form for users to log in to the site. If user
registration is enabled, it will also give a link to the registration page.

`[bigcommerce_registration_form]` - A form to register a new customer account.

`[bigcommerce_cart]` - The items currently in the customer's cart.

`[bigcommerce_account_profile]` - A form to update the customer's profile.

`[bigcommerce_order_history]` - A list of the customer's past orders.

`[bigcommerce_shipping_address_list]` - A list of the customer's shipping addresses, and
forms to add, remove, or update addresses.

## Template Overrides

All templates that render on the front end are found in the `templates/public` directory. To
Override any template, create a `bigcommerce` directory in your theme and copy the template
file to that directory. Examples:

Copy `templates/public/single-bigcommerce_product.php` to
`bigcommerce/single-bigcommerce_product.php`

Copy `templates/public/components/page-wrapper.php` to `bigcommerce/components/page-wrapper.php`

Most templates are used for rendering content inside of the content area of your
theme's template. Only a few take over the entire page template. These may need
modifications to match your theme.

**`single-bigcommerce_product.php`:** The template for rendering a single Product post.

**`archive-bigcommerce_product.php`:** The template for rendering the Product post type
archive.

Both of these templates call `get_header()` and `get_footer()` to render your theme's
default header and footer. The page content is rendered inside the wrapper template found
in `components/page-wrapper.php`. By modifying this wrapper template to match the HTML
markup of a template in your theme, you should have consistent styling across your site.

Additional precautions should be taken when editing the contents of a template override. Within
each of the templates there are `data-attributes` and `PHP` calls that are required in order for the
template to work properly. Wherever you see a PHP call, you should assume that it is a necessary
part of that template and should not be removed or altered. Additionally, you will find documentation
in all of these templates denoting where specific classes or data-attributes are required. Omitting or
removing these classes or attributes could potentially break the JS functionality of this plugin and
your site's theme.

## Action and Filter Hooks

### Architectural Guidelines

All actions and filters called by the plugin begin with the `bigcommerce/` prefix (e.g.,
`bigcommerce/init`). If there is a dynamic component to the hook, it should be preceded
by an equal sign (e.g., `bigcommerce/template=' . $template . '/path`).

The entire plugin operates through closures wrapped around calls to classes instantiated
via a dependency injection container. In the event that you need to modify the core
behavior of the plugin, there are several methods to get access to these closures.

**WARNING:** *Modifying core plugin functionality can lead to security vulnerabilities,
data corruption, broken user workflows, and an overall unpleasant experience for you and
your customers. Proceed at your own risk.*

The `bigcommerce/init` action fires after the plugin has completed initializing all of
it service providers and hooked them into WordPress. It passes two arguments: the primary
plugin controller (an instance of the `BigCommerce\Plugin` class) and the dependency
injection container itself. The former is also available at any time after initialization
by calling the function `bigcommerce()`.

An instance of each of the service providers found in the `src/BigCommerce/Container`
directory can be accessed via this plugin controller, using the keys specified in
`\BigCommerce\Plugin::load_service_providers()`. E.g., to get an instance of the
`BigCommerce\Container\Cart` service provider, you would use `bigcommerce()->cart`.

Every action or filter callback created by one of the service providers is given an
identifier so that it can be retrieved and, if appropriate, unhooked from WordPress. E.g.,
to unhook the closure that renders the product archive template and replace it with your
own, you could do:

```
remove_action( 'bigcommerce/template/product/archive', bigcommerce()->templates->product_archive, 10 );
add_action( 'bigcommerce/template/product/archive', 'your_callback_function', 10, 2 );
```

### Hook reference

TODO: A comprehensive list of hooks is available in the code reference.
