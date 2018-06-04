# BigCommerce Code Reference

## Requirements

* PHP 5.6+
* Composer
* WP CLI

## Installation

Clone the BigCommerce and WP Parser respositories into your WordPress plugins directory

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

## Building

```
wp plugin activate bigcommerce
wp bigcommerce docs build /path/to/docs.json
wp bigcommerce docs import /path/to/docs.json
wp plugin deactivate bigcommerce
```