<?php


namespace BigCommerce\Amp;

use BigCommerce\Settings\Sections\Cart as CartSettings;
use BigCommerce\Cart\Cart as Cart;

class Amp_Cart_Menu_Item {

	/**
	 * @param object $menu_item
	 *
	 * @return object
	 * @filter wp_setup_nav_menu_item
	 */
	public function add_classes_to_cart_page( $menu_item, $proxy_base ) {
		if ( ! get_option( CartSettings::OPTION_ENABLE_CART, true ) || is_admin() ) {
			return $menu_item;
		}
		if ( ! $this->is_cart_menu_item( $menu_item ) ) {
			return $menu_item;
		}
		$menu_item->classes[] = 'menu-item-bigcommerce-cart';
		$menu_item->title     = str_replace( ' <span class="bigcommerce-cart__item-count"></span>', '', $menu_item->title );
		$amp_cart_rest_url    = rest_url( sprintf( '/%s/amp-cart?cart_id=CLIENT_ID(%s)', $proxy_base, Cart::CART_COOKIE ) );
		$menu_item->title    .= '<amp-list
	id="cart-items-count"
	layout="fixed"
	height="25"
	width="25"
	src="' . esc_url( $amp_cart_rest_url ) . '"
	single-item
	items="."
	class="bc-cart-items-count bc-cart-items-count--amp"
	reset-on-refresh
	>
	<template type="amp-mustache">
		<span class="bigcommerce-cart__item-count">{{ items_count }}</span>
	</template>
</amp-list>';

		return $menu_item;
	}

	private function is_cart_menu_item( $menu_item ) {
		if ( 'post_type' !== $menu_item->type ) {
			return false;
		}
		$cart_page_id = get_option( CartSettings::OPTION_CART_PAGE_ID, 0 );

		return $menu_item->object_id === $cart_page_id;
	}
}