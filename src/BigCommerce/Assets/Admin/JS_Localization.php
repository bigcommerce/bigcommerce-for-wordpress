<?php


namespace BigCommerce\Assets\Admin;


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
			'buttons'    => [
				'add_product'     => __( 'Add Product', 'bigcommerce' ),
				'remove_product'  => __( 'Remove Product', 'bigcommerce' ),
				'remove_selected' => __( '(Remove)', 'bigcommerce' ),
			],
			'messages'   => [
				'ajax_error'  => __( 'There was and error submitting or retrieving your request. Please try again.', 'bigcommerce' ),
				'no_results'  => __( 'We could not find any products that matched your query. Please clear your search and try again.', 'bigcommerce' ),
				'no_products' => __( 'We could not find any products that matched your query. Please edit this block and try again.', 'bigcommerce' ),
			],
			'operations' => [
				'query_string_separator' => __( '&', 'bigcommerce' ),
			],
			'text'       => [
				'id_prefix' => __( 'ID:', 'bigcommerce' ),
			],
		];

		return apply_filters( 'bigcommerce/admin/js_localization', $js_i18n_array );
	}
}