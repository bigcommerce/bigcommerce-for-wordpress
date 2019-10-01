<?php

/**
 * Add functions that WooCommerce-compatible themes might expect to see
 */


use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Checkout_Page;

if ( ! function_exists( 'wc' ) ) {
	function wc() {
		return null;
	}
}

if ( ! function_exists( 'wc_get_template_part' ) ) {
	function wc_get_template_part( $slug, $name = '' ) {
		return;
	}
}

if ( ! function_exists( 'wc_get_template' ) ) {
	function wc_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
		return;
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
		return;
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
