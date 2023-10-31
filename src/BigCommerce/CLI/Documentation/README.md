# BigCommerce Code Reference

## Requirements

* PHP 5.6+
* Composer
* WP CLI

## Installation

Clone the BigCommerce and WP Parser repositories into your WordPress plugins directory

```
cd wp-content/plugins/
git clone git@github.com:moderntribe/bigcommerce.git
git clone git@github.com:WordPress/phpdoc-parser.git
```

Install dependencies using composer

```
cd bigcommerce
composer install
cd ../phpdoc-parser
composer install
```

Activate the WP Parser plugin

```
wp plugin activate phpdoc-parser
```

Install the developer.wordpress.org theme

```
cd ../.. # back to the root directory
svn checkout https://meta.svn.wordpress.org/sites/trunk/wordpress.org/public_html/wp-content/themes/pub/wporg-developer/ wp-content/themes/wporg-developer
```

Install and activate the BigCommerce Documentation theme

```
git clone git@github.com:moderntribe/bigcommerce-documentation.git wp-content/themes/bigcommerce-documentation
wp theme activate bigcommerce-documentation
```

Set up the following content:

* Home Page: should use the "Home" template
* Reference Page: should use the "Reference" template and have the slug "reference"

Create the primary nav menu and add links to the post type archives for the reference post types.

And change the following settings:

* Settings -> Reading
  * Set the home page to display a static page
  * Select the home page you created above as the home page
* Settings -> Discussion
  * Check the "Comment must be manually approved" box
  * Check the "Users must be registered and logged in to comment" box
* Settings -> Permalinks
  * Set the permalink structure to anything other than "Plain"

## Building

```
wp plugin activate bigcommerce
wp bigcommerce docs build /path/to/docs.json
wp bigcommerce docs import /path/to/docs.json
wp plugin deactivate bigcommerce
```