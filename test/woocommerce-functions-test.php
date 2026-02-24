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
	/**
	 * Retrieves the WooCommerce facade instance from the container.
	 *
	 * @return Compatibility The WooCommerce facade instance.
	 */
	function wc() {
		$container = bigcommerce()->container();
		return $container[ Compatibility::WC_FACADE ];
	}
}

if ( ! function_exists( 'wc_get_template_part' ) ) {
	/**
	 * Retrieves a template part for WooCommerce.
	 *
	 * @param string $slug The template slug.
	 * @param string $name The template part name (optional).
	 * @return string The rendered template part.
	 */
	function wc_get_template_part( $slug, $name = '' ) {
		$template = "{$slug}.php";
		if ( $name ) {
			$template = "{$slug}-{$name}.php"; 
		}
		return wc_get_template( $template );
	}
}

if ( ! function_exists( 'wc_get_template' ) ) {
	/**
	 * Retrieves and renders a WooCommerce template.
	 *
	 * @param string $template_name The template file name.
	 * @param array $args Arguments to pass to the template.
	 * @param string $template_path The path to the template (optional).
	 * @param string $default_path The default path (optional).
	 * @return void
	 */
	function wc_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
		$container = bigcommerce()->container();
		$container[ Compatibility::THEME ]->render_template( $template_name, $args );
	}
}

if ( ! function_exists( 'wc_get_template_html' ) ) {
	/**
	 * Retrieves the HTML content for a WooCommerce template.
	 *
	 * @param string $template_name The template file name.
	 * @param array $args Arguments to pass to the template.
	 * @param string $template_path The path to the template (optional).
	 * @param string $default_path The default path (optional).
	 * @return string An empty string.
	 */
	function wc_get_template_html( $template_name, $args = [], $template_path = '', $default_path = '' ) {
		return '';
	}
}

if ( ! function_exists( 'wc_locate_template' ) ) {
	/**
	 * Locates a WooCommerce template.
	 *
	 * @param string $template_name The template file name.
	 * @param string $template_path The path to the template (optional).
	 * @param string $default_path The default path (optional).
	 * @return string An empty string.
	 */
	function wc_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		return '';
	}
}

if ( ! function_exists( 'woocommerce_mini_cart' ) ) {
	/**
	 * Displays the mini cart for WooCommerce.
	 *
	 * @return void
	 */
	function woocommerce_mini_cart() {
		printf( '<div data-js="bc-mini-cart"><span class="bc-loading">%s</span></div>', esc_html( __( 'Loading', 'bigcommerce' ) ) );
	}
}

if ( ! function_exists( 'wc_get_cart_url' ) ) {
	/**
	 * Retrieves the URL of the cart page.
	 *
	 * @return string The cart page URL.
	 */
	function wc_get_cart_url() {
		return get_permalink( get_option( Cart_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'wc_get_checkout_url' ) ) {
	/**
	 * Retrieves the URL of the checkout page.
	 *
	 * @return string The checkout page URL.
	 */
	function wc_get_checkout_url() {
		return get_permalink( get_option( Checkout_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_cart' ) ) {
	/**
	 * Checks if the current page is the cart page.
	 *
	 * @return bool True if the current page is the cart page, false otherwise.
	 */
	function is_cart() {
		return is_page( get_option( Cart_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_checkout' ) ) {
	/**
	 * Checks if the current page is the checkout page.
	 *
	 * @return bool True if the current page is the checkout page, false otherwise.
	 */
	function is_checkout() {
		if ( ! (bool) get_option( Cart_Settings::OPTION_EMBEDDED_CHECKOUT, false ) ) {
			return false;
		}
		return is_page( get_option( Checkout_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_account_page' ) ) {
	/**
	 * Checks if the current page is the account page.
	 *
	 * @return bool True if the current page is the account page, false otherwise.
	 */
	function is_account_page() {
		return is_page( get_option( Account_Page::NAME, 0 ) );
	}
}

if ( ! function_exists( 'is_product' ) ) {
	/**
	 * Checks if the current page is a product page.
	 *
	 * @return bool True if the current page is a product page, false otherwise.
	 */
	function is_product() {
		return is_singular( Product::NAME );
	}
}

if ( ! function_exists( 'is_shop' ) ) {
	/**
	 * Checks if the current page is the shop page.
	 *
	 * @return bool True if the current page is the shop page, false otherwise.
	 */
	function is_shop() {
		return is_archive( Product::NAME );
	}
}

if ( ! function_exists( 'is_product_category' ) ) {
	/**
	 * Checks if the current page is a product category page.
	 *
	 * @return bool True if the current page is a product category page, false otherwise.
	 */
	function is_product_category() {
		return is_tax( Product_Category::NAME );
	}
}

if ( ! function_exists( 'is_product_tag' ) ) {
	/**
	 * Checks if the current page is a product tag page.
	 *
	 * @return bool False, as the function always returns false.
	 */
	function is_product_tag() {
		return false;
	}
}

if ( ! function_exists( 'is_product_taxonomy' ) ) {
	/**
	 * Checks if the current page is a product taxonomy page.
	 *
	 * @return bool True if the current page is a product taxonomy page, false otherwise.
	 */
	function is_product_taxonomy() {
		return is_tax( Product_Category::NAME );
	}
}

if ( ! function_exists( 'wc_get_image_size' ) ) {
	/**
	 * Retrieves the WooCommerce image size.
	 *
	 * @return string An empty string.
	 */
	function wc_get_image_size() {
		return '';
	}
}

if ( ! function_exists( 'wc_print_notices' ) ) {
	/**
	 * Prints WooCommerce notices.
	 *
	 * @return string An empty string.
	 */
	function wc_print_notices() {
		return '';
	}
}

if ( ! function_exists( 'woocommerce_reset_loop' ) ) {
	/**
	 * Resets the WooCommerce loop.
	 *
	 * @return string An empty string.
	 */
	function woocommerce_reset_loop() {
		return '';
	}
}

if ( ! function_exists( 'wc_get_page_id' ) ) {
	/**
	 * Retrieves the ID of a WooCommerce page.
	 *
	 * @return int The ID of the WooCommerce page, or -1 if not found.
	 */
	function wc_get_page_id() {
		return -1;
	}
}
