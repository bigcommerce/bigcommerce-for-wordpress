<?php


namespace BigCommerce\Assets\Theme;


class JS_Localization {
	/**
	 * stores all text strings needed in the admin scripts.js file
	 *
	 * The code below is an example of structure. Check the readme js section for more info on how to use.
	 *
	 * @return array
	 */
	public function get_data() {
		$js_i18n_array = [
			'operations' => [
				'query_string_separator' => __( '&', 'bigcommerce' ),
			],
			'cart'       => [
				'items_url_param'         => __( '/items/', 'bigcommerce' ),
				'quantity_param'          => __( 'quantity', 'bigcommerce' ),
				'message_empty'           => __( 'Your cart is empty.', 'bigcommerce' ),
				'continue_shopping_label' => __( 'Take a look around.', 'bigcommerce' ),
				'continue_shopping_url'   => esc_url( home_url() ),
				'cart_error_502'          => __( 'There was an error with your request. Please try again.', 'bigcommerce' ),
				'add_to_cart_error_502'   => __( 'There was an error adding this product to your cart. It might be out of stock or unavailable.', 'bigcommerce' ),
			],
			'account'    => [
				'confirm_delete_message' => __( 'Are you sure you want to delete this address?', 'bigcommerce' ),
				'confirm_delete_address' => __( 'Confirm', 'bigcommerce' ),
				'cancel_delete_address'  => __( 'Cancel', 'bigcommerce' ),
			],
		];

		return apply_filters( 'bigcommerce/js_localization', $js_i18n_array );
	}
}