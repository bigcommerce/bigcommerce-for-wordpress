<?php


namespace BigCommerce\Settings\Screens;

use BigCommerce\Container\Settings;
use BigCommerce\Nav_Menu\Dynamic_Menu_Items;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Nav_Menu_Options;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Nav_Menu_Screen extends Abstract_Screen {
	const NAME          = 'bigcommerce_nav_setup';
	const COMPLETE_FLAG = 'bigcommerce_nav_setup_complete';

	protected function get_page_title() {
		return __( 'Setup Navigation Menus', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Menu Setup', 'bigcommerce' );
	}

	protected function before_form() {
		printf( '<p>%s</p>', __( 'Select options below to add items to your navigation menu. If you choose to skip this step, you can also add items through the Customizer or at Appearance ➔ Menus.', 'bigcommerce' ) );
		parent::before_form();
	}

	protected function submit_button() {
		echo '<div class="bc-settings-save">';
		submit_button( __( 'Add to Menu', 'bigcommerce' ) );

		$skip_url = add_query_arg( [
			'action' => self::NAME,
			'skip'   => 1,
		], $this->form_action_url() );
		$skip_url = wp_nonce_url( $skip_url, self::NAME );
		printf( '<p class="bc-welcome-skip-menu-setup"><a href="%s" class="bc-admin-btn bc-admin-btn--outline">%s</a></p>', esc_url( $skip_url ), __( 'Skip Menu Setup', 'bigcommerce' ) );
		echo '</div>';
	}

	/**
	 * Submit to admin-post.php, as this is not data saved as options
	 *
	 * @return string
	 */
	protected function form_action_url() {
		return admin_url( 'admin-post.php' );
	}

	/**
	 * Set action and nonce for processing with admin-post.php
	 *
	 * @return void
	 */
	protected function settings_fields() {
		printf( '<input type="hidden" name="action" value="%s" />', esc_attr( self::NAME ) );
		wp_nonce_field( self::NAME );
	}

	/**
	 * @return void
	 * @action admin_post_ . self::NAME
	 */
	public function handle_submission() {
		if ( ! check_admin_referer( self::NAME ) ) {
			return;
		}

		// The user has clicked the button to skip this step
		if ( ! empty( $_REQUEST[ 'skip' ] ) ) {
			$this->mark_complete();
			$this->do_redirect();

			return;
		}

		$submission = $_POST;
		unset( $submission[ 'action' ], $submission[ '_wpnonce' ] );

		$menu_id = absint( $submission[ Nav_Menu_Options::MENU_SELECT ] );
		$items   = $submission[ Nav_Menu_Options::ITEMS_SELECT ];

		// there has to be a nav menu to add the menu items to
		if ( empty( $menu_id ) || ! wp_get_nav_menu_object( $menu_id ) ) {
			add_settings_error( Nav_Menu_Options::MENU_SELECT, 'no-menu', __( 'Please select a navigation menu', 'bigcommerce' ), 'error' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			$this->do_redirect();

			return;
		}

		if ( in_array( 'categories', $items, true ) ) {
			$this->create_categories_menu_item( $menu_id );
		}

		if ( in_array( 'brands', $items, true ) ) {
			$this->create_brands_menu_item( $menu_id );
		}

		$profile = in_array( 'profile', $items, true );
		$orders  = in_array( 'orders', $items, true );
		$address = in_array( 'address', $items, true );
		if ( count( array_filter( [ $profile, $orders, $address ] ) ) > 1 ) {
			$register = $this->create_register_menu_item( $menu_id );
		} else {
			$register = 0;
		}

		if ( $profile ) {
			$this->create_profile_menu_item( $menu_id, $register );
		}
		if ( $orders ) {
			$this->create_orders_menu_item( $menu_id, $register );
		}
		if ( $address ) {
			$this->create_address_menu_item( $menu_id, $register );
		}

		if ( in_array( 'cart', $items, true ) ) {
			$this->create_cart_menu_item( $menu_id );
		}

		if ( in_array( 'products', $items, true ) ) {
			$this->create_products_menu_item( $menu_id );
		}

		$this->mark_complete();
		$this->do_redirect();
	}

	/**
	 * Set a flag so that the screen does not load again
	 *
	 * @return void
	 */
	private function mark_complete() {
		update_option( self::COMPLETE_FLAG, 1 );
	}

	/**
	 * Handle the redirect after processing the form.
	 * Reloads the same admin screen, but will most likely
	 * be redirected on reload.
	 *
	 * @return void
	 */
	private function do_redirect() {
		wp_safe_redirect( esc_url_raw( $this->get_url() ) );
		exit();
	}

	/**
	 * Only register the screen if onboarding is complete,
	 * but the menu setup form has not yet been submitted
	 *
	 * @return bool
	 */
	public function should_register() {
		if ( $this->configuration_status < Settings::STATUS_CHANNEL_CONNECTED ) {
			return false;
		}

		$complete = get_option( self::COMPLETE_FLAG, 0 );
		if ( $complete ) {
			return false;
		}

		$menus = wp_get_nav_menus();
		if ( empty( $menus ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds the menu item to the given menu. Defaults
	 * to creating published menu items.
	 * 
	 * @param int $menu_id
	 * @param array $item_args
	 *
	 * @return int|\WP_Error
	 */
	private function create_menu_item( $menu_id, $item_args ) {
		$data = wp_parse_args( $item_args, [
			'menu-item-title'  => '',
			'menu-item-type'   => '',
			'menu-item-object' => '',
			'menu-item-url'    => '',
			'menu-item-status' => 'publish',
		] );

		return wp_update_nav_menu_item( $menu_id, 0, $data );
	}

	private function create_categories_menu_item( $menu_id ) {
		return $this->create_menu_item( $menu_id, [
			'menu-item-title'  => __( 'Categories', 'bigcommerce' ),
			'menu-item-type'   => Dynamic_Menu_Items::TYPE,
			'menu-item-object' => Product_Category::NAME,
		] );
	}

	private function create_brands_menu_item( $menu_id ) {
		return $this->create_menu_item( $menu_id, [
			'menu-item-title'  => __( 'Brands', 'bigcommerce' ),
			'menu-item-type'   => Dynamic_Menu_Items::TYPE,
			'menu-item-object' => Brand::NAME,
		] );
	}

	private function create_cart_menu_item( $menu_id ) {
		$page = get_option( Cart_Page::NAME, 0 );
		if ( ! $page ) {
			return 0;
		}

		return $this->create_menu_item( $menu_id, [
			'menu-item-type'      => 'post_type',
			'menu-item-object-id' => $page,
			'menu-item-object'    => 'page',
		] );
	}

	private function create_products_menu_item( $menu_id ) {
		return $this->create_menu_item( $menu_id, [
			'menu-item-title'  => __( 'All Products', 'bigcommerce' ),
			'menu-item-type'   => 'post_type_archive',
			'menu-item-object' => Product::NAME,
		] );
	}

	private function create_register_menu_item( $menu_id ) {
		$page = get_option( Registration_Page::NAME, 0 );
		if ( ! $page ) {
			return 0;
		}

		return $this->create_menu_item( $menu_id, [
			'menu-item-type'      => 'post_type',
			'menu-item-object-id' => $page,
			'menu-item-object'    => 'page',
		] );
	}

	private function create_profile_menu_item( $menu_id, $register_page ) {
		$page = get_option( Account_Page::NAME, 0 );
		if ( ! $page ) {
			return 0;
		}

		return $this->create_menu_item( $menu_id, [
			'menu-item-type'      => 'post_type',
			'menu-item-object-id' => $page,
			'menu-item-object'    => 'page',
			'menu-item-parent-id' => $register_page,
		] );
	}

	private function create_orders_menu_item( $menu_id, $register_page ) {
		$page = get_option( Orders_Page::NAME, 0 );
		if ( ! $page ) {
			return 0;
		}

		return $this->create_menu_item( $menu_id, [
			'menu-item-type'      => 'post_type',
			'menu-item-object-id' => $page,
			'menu-item-object'    => 'page',
			'menu-item-parent-id' => $register_page,
		] );
	}

	private function create_address_menu_item( $menu_id, $register_page ) {
		$page = get_option( Address_Page::NAME, 0 );
		if ( ! $page ) {
			return 0;
		}

		return $this->create_menu_item( $menu_id, [
			'menu-item-type'      => 'post_type',
			'menu-item-object-id' => $page,
			'menu-item-object'    => 'page',
			'menu-item-parent-id' => $register_page,
		] );
	}


}