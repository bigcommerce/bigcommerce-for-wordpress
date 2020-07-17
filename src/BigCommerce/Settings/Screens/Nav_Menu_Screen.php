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

class Nav_Menu_Screen extends Onboarding_Screen {
	const NAME          = 'bigcommerce_nav_setup';
	const COMPLETE_FLAG = 'bigcommerce_nav_setup_complete';

	protected function get_page_title() {
		return __( 'Setup Navigation Menus', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	/**
	 * @return string
	 */
	private function get_description() {
		return sprintf( '<p>%s</p>', __( 'Select options below to add items to your navigation menu. If you choose to skip this step, you can also add items through the Customizer or at Appearance ➔ Menus.', 'bigcommerce' ) );
	}

	protected function get_header() {
		return sprintf(
			'%s<header class="bc-connect__header"><h1 class="bc-settings-connect__title">%s</h1>%s</header>',
			$this->before_title(),
			$this->get_page_title(),
			$this->get_description()
		);
	}

	protected function submit_button() {
		echo '<div class="bc-settings-save">';
		$this->onboarding_submit_button( 'bc-settings-menu-submit', 'bc-onboarding-arrow', __( 'Add to Menu', 'bigcommerce' ), true );

		$skip_url = add_query_arg( [
			'action' => self::NAME,
			'skip'   => 1,
		], $this->form_action_url() );
		$skip_url = wp_nonce_url( $skip_url, self::NAME );
		printf( '<p class="bc-welcome-skip-menu-setup"><a href="%s" class="bc-admin-btn bc-admin-btn--outline">%s</a></p>', esc_url( $skip_url ), esc_html( __( 'Skip Menu Setup', 'bigcommerce' ) ) );
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
		if ( ! empty( $_REQUEST['skip'] ) ) {
			$this->mark_complete();
			$this->do_redirect();

			return;
		}

		$submission = $_POST;
		unset( $submission['action'], $submission['_wpnonce'] );
		$menu_id = 0;
		$message = '';

		// Create the new nav menu if requested
		try {
			if ( 'new' === $submission[ Nav_Menu_Options::MENU_SELECT ] ) {
				$menu_id = $this->create_nav_menu( $submission[ Nav_Menu_Options::MENU_NAME ] );
				$message = __( 'Nav menu created. You can edit it through the Customizer or at Appearance ➔ Menus.', 'bigcommerce' );
			}
		} catch ( \Exception $e ) {

			$this->do_error_redirect( $e->getMessage() );

			return;
		}

		$menu_id = $menu_id ?: absint( $submission[ Nav_Menu_Options::MENU_SELECT ] );
		$items   = $submission[ Nav_Menu_Options::ITEMS_SELECT ];

		// there has to be a nav menu to add the menu items to
		if ( empty( $menu_id ) || ! wp_get_nav_menu_object( $menu_id ) ) {
			$this->do_error_redirect( __( 'Please select a navigation menu', 'bigcommerce' ) );

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
			$mini_cart = in_array( 'minicart', $items, true );
			update_option( \BigCommerce\Customizer\Sections\Cart::ENABLE_MINI_CART, $mini_cart ? 'yes' : 'no' );
		}

		if ( in_array( 'products', $items, true ) ) {
			$this->create_products_menu_item( $menu_id );
		}

		if ( empty( $message ) ) {
			$message = __( 'Nav menu updated. You can edit it through the Customizer or at Appearance ➔ Menus.', 'bigcommerce' );
		}

		$this->mark_complete();
		$this->do_redirect( $message );
	}

	/**
	 * Create a new nav menu
	 *
	 * @param string $name The name to give the menu. Value is unescaped user input.
	 *
	 * @return int The ID of the created menu
	 */
	private function create_nav_menu( $name ) {
		$name = sanitize_text_field( $name );
		if ( empty( $name ) ) {
			/**
			 * Filter the default name to give to an automatically generated
			 * navigation menu when the user does not provide a value.
			 *
			 * @param string $name The menu name
			 */
			$name = apply_filters( 'bigcommerce/settings/default_new_menu_name', __( 'BigCommerce', 'bigcommerce' ) );
		}
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			throw new \RuntimeException( sprintf( __( 'Error creating navigation menu. %s', 'bigcommerce' ), $menu_id->get_error_message() ) );
		}

		return $menu_id;
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
	 * @param string $message
	 *
	 * @return void
	 */
	private function do_redirect( $message = '' ) {
		$url = $this->get_url();
		if ( $message ) {
			add_settings_error( self::NAME, 'updated', $message, 'updated' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			$url = add_query_arg( [ 'settings-updated' => 1 ], $url );
		}
		wp_safe_redirect( esc_url_raw( $url ) );
		exit();
	}

	/**
	 * Set the error message and reload the page
	 *
	 * @param string $message The error message to display
	 *
	 * @return void
	 */
	private function do_error_redirect( $message = '' ) {
		$message = $message ?: __( 'We encountered an unexpected error setting up your menu. Please try again.', 'bigcommerce' );
		add_settings_error( self::NAME, 'error', $message, 'error' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		$url = add_query_arg( [ 'settings-updated' => 1 ], $this->get_url() );

		wp_safe_redirect( esc_url_raw( $url ) );
		exit();
	}

	/**
	 * Only register the screen if onboarding is complete,
	 * but the menu setup form has not yet been submitted
	 *
	 * @return bool
	 */
	public function should_register() {
		if ( $this->configuration_status < Settings::STATUS_STORE_TYPE_SELECTED ) {
			return false;
		}

		if ( $this->configuration_status < Settings::STATUS_MENUS_CREATED ) {
			return true;
		}

		return false;
	}

	/**
	 * Adds the menu item to the given menu. Defaults
	 * to creating published menu items.
	 *
	 * @param int   $menu_id
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
