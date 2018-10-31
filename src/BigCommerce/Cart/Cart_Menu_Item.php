<?php


namespace BigCommerce\Cart;

use BigCommerce\Settings;
use BigCommerce\Settings\Sections\Cart;

class Cart_Menu_Item {

	/**
	 * @param object $menu_item
	 *
	 * @return object
	 * @filter wp_setup_nav_menu_item
	 */
	public function add_classes_to_cart_page( $menu_item ) {
		if ( ! get_option( Cart::OPTION_ENABLE_CART, true ) || is_admin() ) {
			return $menu_item;
		}
		if ( ! $this->is_cart_menu_item( $menu_item ) ) {
			return $menu_item;
		}
		$menu_item->classes[] = 'menu-item-bigcommerce-cart';

		$menu_item->title .= ' <span class="bigcommerce-cart__item-count"></span>';

		return $menu_item;
	}

	private function is_cart_menu_item( $menu_item ) {
		if ( $menu_item->type != 'post_type' ) {
			return false;
		}
		$cart_page_id = get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 );

		return $menu_item->object_id == $cart_page_id;
	}
}