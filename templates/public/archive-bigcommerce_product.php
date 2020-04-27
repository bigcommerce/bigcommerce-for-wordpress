<?php
/**
 * Default Product Archive Template
 * Override this template in your own theme by creating a file at
 * [your-theme]/bigcommerce/archive-bigcommerce_product.php
 * 
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header();
echo apply_filters( 'bigcommerce/template/product/archive', '' );
get_footer();
