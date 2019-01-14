<?php

namespace BigCommerce\Post_Types\Product;

use Pimple\Container;

/**
 * Handles extra columns for the BigCommerce Products post type
 *
 * Class Admin_List
 * @package BigCommerce\Post_Types\Product
 */
class Admin_List {
	/**
	 * Admin_List constructor.
	 *
	 * @param Container $container
	 */
	public function __construct () {
		// Silent
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 * @filter manage_bigcommerce_product_posts_columns
	 */
	function add_bigcommerce_product_id_column($columns) {
		$columns['big_commerce_product_id'] = __('Product ID', 'bigcommerce');
		return $columns;
	}

	/**
	 * @param $column_name
	 * @param $post_ID
	 *
	 * @fil
	 * @param $post_ID
	 * @action manage_bigcommerce_product_posts_custom_column
	 */
	function get_bigcommerce_product_id_value($column_name, $post_ID) {
		if ($column_name == 'big_commerce_product_id') {
			$product_id =  get_post_meta( $post_ID, 'bigcommerce_id', true );
			echo absint($product_id);
		}
	}
}
