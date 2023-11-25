<?php

/**
 * Add functions that WooCommerce-compatible themes might expect to see
 */


use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Container\Compatibility;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;

if ( ! function_exists( 'wc' ) ) {
	function wc() {
		$container = bigcommerce()->container();
		return $container[ Compatibility::WC_FACADE ];
	}
}

if ( ! function_exists( 'wc_get_template_part' ) ) {
	function wc_get_template_part( $slug, $name = '' ) {
		$template = "{$slug}.php";
		if ( $name ) {
			$template = "{$slug}-{$name}.php"; 
		}
		return wc_get_template( $template );
	}
}

if ( ! function_exists( 'wc_get_template' ) ) {
	function wc_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
		$container = bigcommerce()->container();
		$container[ Compatibility::THEME ]->render_template( $template_name, $args );
	}
}

if ( ! function_exists( 'wc_get_template_html' ) ) {
	function wc_get_template_html( $template_name, $args = [], $template_path = '', $default_path = '' ) {
		return '';
	}
}

if ( ! function_exists( 'wc_locate_template' ) ) {
	function wc_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		return '';
	}
}

if ( ! function_exists( 'woocommerce_mini_cart' ) ) {
	function woocommerce_mini_cart() {
		printf( '<div data-js="bc-mini-cart"><span class="bc-loading">%s</span></div>', esc_html( __( 'Loading', 'bigcommerce' ) ) );
	}
}

if ( ! function_exists( 'wc_get_cart_url' ) ) {
	function wc_get_cart_url() {
		return get_permalink( get_option( Cart_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'wc_get_checkout_url' ) ) {
	function wc_get_checkout_url() {
		return get_permalink( get_option( Checkout_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_cart' ) ) {
	function is_cart() {
		return is_page( get_option( Cart_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_checkout' ) ) {
	function is_checkout() {
		if ( ! (bool) get_option( Cart_Settings::OPTION_EMBEDDED_CHECKOUT, false ) ) {
			return false;
		}
		return is_page( get_option( Checkout_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_account_page' ) ) {
	function is_account_page() {
		return is_page( get_option( Account_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_product' ) ) {
	function is_product() {
		return is_singular( Product::NAME );
	}
}

if ( ! function_exists( 'is_shop' ) ) {
	function is_shop() {
		return is_archive( Product::NAME );
	}
}

if ( ! function_exists( 'is_product_category' ) ) {
	function is_product_category() {
		return is_tax( Product_Category::NAME );
	}
}

if ( ! function_exists( 'is_product_tag' ) ) {
	function is_product_tag() {
		return false;
	}
}

if ( ! function_exists( 'is_product_taxonomy' ) ) {
	function is_product_taxonomy() {
		return is_tax( Product_Category::NAME );
	}
}

if ( ! function_exists( 'wc_get_image_size' ) ) {
	function wc_get_image_size() {
		return '';
	}
}

if ( ! function_exists( 'wc_print_notices' ) ) {
	function wc_print_notices() {
		return '';
	}
}

if ( ! function_exists( 'woocommerce_reset_loop' ) ) {
	function woocommerce_reset_loop() {
		return '';
	}
}

if ( ! function_exists( 'wc_get_page_id' ) ) {
	function wc_get_page_id() {
		return -1;
	}
}
