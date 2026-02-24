<?php


namespace BigCommerce\Assets\Theme;

use BigCommerce\Templates\Cart_Empty;

/**
 * Handles the localization of JavaScript strings for the theme. This class generates an array of text
 * strings used in JavaScript, which can be filtered and sanitized before being passed to the frontend.
 *
 * @package BigCommerce\Assets\Theme
 */
class JS_Localization {
	/**
	 * Generates the localization data for JavaScript.
	 *
	 * This method returns an array of text strings that are used in various parts of the frontend,
	 * including cart messages, account actions, and error handling. The strings are internationalized
	 * and can be filtered using WordPress hooks.
	 *
	 * @return array The localization data for JavaScript.
	 */
	public function get_data() {
		$empty_cart_data = Cart_Empty::factory()->get_data();

		$js_i18n_array = [
			'operations' => [
				'loading' => __( 'Loading', 'bigcommerce' ),
				'query_string_separator' => __( '&', 'bigcommerce' ),
			],
			'cart'       => [
				'items_url_param'          => '/items/',
				'mini_url_param'           => '/mini/',
				'quantity_param'           => 'quantity',
				'message_empty'            => __( 'Your cart is empty.', 'bigcommerce' ),
				'continue_shopping_label'  => esc_html( $empty_cart_data[ Cart_Empty::LINK_TEXT ] ),
				'continue_shopping_url'    => esc_url( $empty_cart_data[ Cart_Empty::LINK ] ),
				'cart_error_502'           => __( 'There was an error with your request. Please try again.', 'bigcommerce' ),
				'add_to_cart_error_502'    => __( 'There was an error adding this product to your cart. It might be out of stock or unavailable.', 'bigcommerce' ),
				'ajax_add_to_cart_error'   => __( 'There was an error adding this product to your cart.', 'bigcommerce' ),
				'ajax_add_to_cart_success' => __( 'Product successfully added to your cart.', 'bigcommerce' ),
				'mini_cart_loading'        => __( 'Loading', 'bigcommerce' ),
				'shipping_calc_error'      => __( 'There was an error calculating your shipping cost. Please try again.', 'bigcommerce' ),
				'coupon_discount'          => __( 'Discount', 'bigcommerce' ),
				'coupon_error'             => __( 'Your coupon could not be applied to the cart.', 'bigcommerce' ),
				'coupon_removal_error'     => __( 'There was an error removing your coupon from the cart.', 'bigcommerce' ),
				'coupon_success'           => __( 'Your coupon was successfully applied to the cart.', 'bigcommerce' ),
			],
			'account'    => [
				'confirm_delete_message' => __( 'Are you sure you want to delete this address?', 'bigcommerce' ),
				'confirm_delete_address' => __( 'Confirm', 'bigcommerce' ),
				'cancel_delete_address'  => __( 'Cancel', 'bigcommerce' ),
			],
			'errors'     => [
				'pagination_error'         => __( 'There was an error processing your request. Please try again.', 'bigcommerce' ),
				'pagination_timeout_error' => __( 'The server took too long to complete this request. Please try again.', 'bigcommerce' ),
			],
			'pricing'    => [
				'loading_prices' => __( 'Retrieving current pricing data...', 'bigcommerce' ),
			],
			'inventory'    => [
 				'in_stock' => __( 'in Stock', 'bigcommerce' ),
 				'out_of_stock' => __( 'Out of Stock', 'bigcommerce' ),
			],
			'wish_lists' => [
				'copied' => __( 'Copied!', 'bigcommerce' ),
				'copy_link' => __( 'Copy link', 'bigcommerce' ),
				'copy_success' => __( 'Wish List URL copied to clipboard.', 'bigcommerce' ),
			],
		];

		/**
		 * Filter the localization strings passed to front end scripts
		 *
		 * @param array $js_i18n_array The localization strings
		 */
		$js_i18n_array = apply_filters( 'bigcommerce/js_localization', $js_i18n_array );

		$js_i18n_array = $this->kses_strings( $js_i18n_array );

		return $js_i18n_array;
	}

	/**
	 * Recursively sanitize all the strings with wp_kses
	 *
	 * @param string[]|string $strings
	 *
	 * @return array|string
	 */
	private function kses_strings( $strings ) {
		if ( is_array( $strings ) ) {
			return array_map( [ $this, 'kses_strings' ], $strings );
		}
		if ( is_string( $strings ) ) {
			return wp_kses( $strings, 'data' );
		}

		return $strings;
	}
}
