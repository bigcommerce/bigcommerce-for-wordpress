<?php


namespace BigCommerce\Nav_Menu;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Responsible for adding the BigCommerce nav items
 * into the theme customizer
 */
class Nav_Items_Customizer {

	/**
	 * @param array $types
	 *
	 * @return array
	 * @filter customize_nav_menu_available_item_types
	 */
	public function register_item_type( $types ) {
		$types[] = [
			'object'     => 'taxonomy',
			'title'      => __( 'BigCommerce', 'bigcommerce' ),
			'type'       => Dynamic_Menu_Items::TYPE,
			'type_label' => __( 'BigCommerce', 'bigcommerce' ),
		];

		return $types;
	}

	/**
	 * @param array  $items  The array of menu items.
	 * @param string $type   The object type.
	 * @param string $object The object name.
	 * @param int    $page   The current page number.
	 *
	 * @return array
	 * @filter customize_nav_menu_available_items
	 */
	public function register_menu_items( $items, $type, $object, $page ) {
		if ( $type === Dynamic_Menu_Items::TYPE ) {
			$taxonomies = [
				Product_Category::NAME,
				Brand::NAME,
			];
			$items      = array_map( function ( $tax ) {
				$taxonomy = get_taxonomy( $tax );
				return [
					'id' => $tax . '-dynamic',
					'object' => $tax,
					'title' => $taxonomy->label,
					'type' => Dynamic_Menu_Items::TYPE,
					'type_label' => __( 'BigCommerce', 'bigcommerce' ),
					'url' => get_post_type_archive_link( Product::NAME ),
				];

			}, $taxonomies );
		}
		return $items;
	}
}