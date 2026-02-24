<?php

namespace BigCommerce\Amp;

use BigCommerce\Settings\Sections\Cart as CartSettings;
use BigCommerce\Cart\Cart as Cart;

/**
 * Class Amp_Cart_Menu_Item
 *
 * Enhances the cart menu item with AMP-specific functionality, such as adding classes
 * and dynamic cart item counts using AMP components.
 *
 * @package BigCommerce\Amp
 */
class Amp_Cart_Menu_Item {

    /**
     * Adds AMP-specific classes and cart item count functionality to the cart menu item.
     *
     * Adds a `menu-item-bigcommerce-cart` class to the cart menu item and replaces the
     * menu item's title with dynamic AMP-powered content that displays the current cart item count.
     *
     * @param object $menu_item The menu item object.
     * @param string $proxy_base The base URL or namespace used for proxying API requests.
     * @return object Filtered menu item with added AMP-specific functionality.
     *
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
        <span class="bigcommerce-cart__item-count" data-js="bc-cart-item-count">{{ items_count }}</span>
    </template>
</amp-list>';

        return $menu_item;
    }

    /**
     * Checks whether the given menu item corresponds to the cart page.
     *
     * Verifies that the menu item is of type `post_type` and that its object ID matches
     * the cart page ID stored in the options.
     *
     * @param object $menu_item The menu item object to check.
     * @return bool True if the menu item is the cart menu item; false otherwise.
     */
    private function is_cart_menu_item( $menu_item ) {
        if ( 'post_type' !== $menu_item->type ) {
            return false;
        }

        $cart_page_id = get_option( CartSettings::OPTION_CART_PAGE_ID, 0 );

        return $menu_item->object_id === $cart_page_id;
    }
}