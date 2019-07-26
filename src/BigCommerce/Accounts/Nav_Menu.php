<?php


namespace BigCommerce\Accounts;


use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Pages\Wishlist_Page;

class Nav_Menu {
	/**
	 * @param object $menu_item
	 *
	 * @return object
	 * @filter wp_setup_nav_menu_item
	 */
	public function filter_account_menu_items( $menu_item ) {
		if ( $menu_item->object != 'page' ) {
			return $menu_item;
		}
		$page_id = $menu_item->object_id;
		switch ( $page_id ) {
			case get_option( Login_Page::NAME, 0 ):
				return $this->setup_login_menu_item( $menu_item );
			case get_option( Registration_Page::NAME, 0 ):
				return $this->setup_registration_menu_item( $menu_item );
			case get_option( Account_Page::NAME, 0 ):
			case get_option( Orders_Page::NAME, 0 ):
			case get_option( Address_Page::NAME, 0 ):
			case get_option( Wishlist_Page::NAME, 0 ):
				return $this->setup_account_page_menu_item( $menu_item );
			default:
				return $menu_item;
		}

	}

	public function setup_login_menu_item( $menu_item ) {
		if ( is_user_logged_in() && ! is_admin() ) {
			$menu_item->url = wp_logout_url();
			/**
			 * Filter the title of the Sign Out link in the nav menu
			 *
			 * @param string   $title     The menu item title
			 * @param \WP_Post $menu_item The menu item, a \WP_Post that has passed through wp_setup_nav_menu_item()
			 */
			$menu_item->title = apply_filters( 'bigcommerce/nav/logout/title', __( 'Sign Out', 'bigcommerce' ), $menu_item );
		}

		return $menu_item;
	}

	public function setup_registration_menu_item( $menu_item ) {
		if ( is_user_logged_in() && ! is_admin() ) {
			$account_page = get_option( Account_Page::NAME, 0 );
			if ( $account_page ) {
				$menu_item->url = get_permalink( $account_page );
				/**
				 * Filter the title of the My Account link in the nav menu
				 *
				 * @param string   $title     The menu item title
				 * @param \WP_Post $menu_item The menu item, a \WP_Post that has passed through wp_setup_nav_menu_item()
				 */
				$menu_item->title = apply_filters( 'bigcommerce/nav/account/title', __( 'My Account', 'bigcommerce' ), $menu_item );
			} else {
				$menu_item->_invalid = true;
			}
		} elseif ( ! get_option( 'users_can_register' ) ) {
			$menu_item->_invalid = true;
		}

		return $menu_item;
	}

	public function setup_account_page_menu_item( $menu_item ) {
		if ( ! is_user_logged_in() ) {
			$menu_item->_invalid = true;
		}

		return $menu_item;
	}
}