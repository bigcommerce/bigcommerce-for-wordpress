<?php

namespace BigCommerce\Post_Types\Product;

use BigCommerce\Assets\Theme\Image_Sizes;
use Pimple\Container;
use Symfony\Component\DomCrawler\Image;

/**
 * Handles extra columns for the BigCommerce Products post type
 *
 * Class Admin_List
 * @package BigCommerce\Post_Types\Product
 */
class Admin_List {

	const COLUMN_PRODUCT_ID    = 'bigcommerce_product_id';
	const COLUMN_PRODUCT_THUMB = 'bigcommerce_product_thumbnail';

	/**
	 * Admin_List constructor.
	 *
	 * @param Container $container
	 */
	public function __construct() {
		// Silent
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function add_product_list_columns( $columns ) {
		$columns[ self::COLUMN_PRODUCT_ID ]    = __( 'Product ID', 'bigcommerce' );
		$columns[ self::COLUMN_PRODUCT_THUMB ] = __( 'Thumbnail', 'bigcommerce' );
		return $columns;
	}

	/**
	 * @param $column_name
	 * @param $post_ID
	 *
	 * @fil
	 *
	 * @param $post_ID
	 *
	 * @action manage_bigcommerce_product_posts_custom_column for BC product ID
	 */
	public function get_bigcommerce_product_id_value( $column_name, $post_ID ) {
		if ( $column_name == self::COLUMN_PRODUCT_ID ) {
			$product_id = get_post_meta( $post_ID, 'bigcommerce_id', true );
			echo absint( $product_id );
		}
	}

	/**
	 * @param $column_name
	 * @param $post_ID
	 *
	 * @fil
	 *
	 * @param $post_ID
	 *
	 * @action manage_bigcommerce_product_posts_custom_column for BC product ID
	 */
	public function get_bigcommerce_product_thumbnail_value( $column_name, $post_ID ) {
		if ( $column_name == self::COLUMN_PRODUCT_THUMB ) {
			$product_thumbnail = get_the_post_thumbnail( $post_ID, Image_Sizes::BC_THUMB);
			echo $product_thumbnail ;
		}
	}
}
