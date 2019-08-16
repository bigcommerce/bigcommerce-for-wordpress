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
		if ( ! $this->should_show_cart_count() ) {
			return $menu_item;
		}
		if ( ! $this->is_cart_menu_item( $menu_item ) ) {
			return $menu_item;
		}
		$menu_item->classes[] = 'menu-item-bigcommerce-cart';

		$menu_item->title .= ' <span class="bigcommerce-cart__item-count"></span>';

		return $menu_item;
	}

	private function should_show_cart_count() {
		$show = true;

		if ( is_admin() ) {
			$show = false;
		}

		if ( ! get_option( Cart::OPTION_ENABLE_CART, true ) ) {
			$show = false;
		}

		/*
		 * Due to difficulties around accurately syncing the count
		 * with BigCommerce when checkout happens off site, the
		 * cart count is disabled when using redirected checkout.
		 */
		if ( ! get_option( Cart::OPTION_EMBEDDED_CHECKOUT, true ) ) {
			$show = false;
		}

		/**
		 * Filter whether the site should show the cart count on the menu
		 * item for the cart page.
		 *
		 * @param bool $show Whether the cart count will be displayed
		 */
		return apply_filters( 'bigcommerce/cart/menu/show_count', $show );
	}

	private function is_cart_menu_item( $menu_item ) {
		if ( $menu_item->type !== 'post_type' ) {
			return false;
		}
		$cart_page_id = get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 );

		return $menu_item->object_id == $cart_page_id;
	}
}
