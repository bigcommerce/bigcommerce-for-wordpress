<?php


namespace BigCommerce\Settings\Sections;

use BigCommerce\Settings\Screens\Nav_Menu_Screen;

class Nav_Menu_Options extends Settings_Section {
	const NAME         = 'nav_menu';
	const MENU_SELECT  = 'bigcommerce_select_nav_menu';
	const MENU_NAME    = 'bigcommerce_nav_menu_name';
	const ITEMS_SELECT = 'bigcommerce_select_menu_items';

	/**
	 * @action bigcommerce/settings/register/screen= . Nav_Menu_Screen::NAME
	 */
	public function register_settings_section() {

		$screen = Nav_Menu_Screen::NAME;

		add_settings_section(
			self::NAME,
			__( "Select which items you'd like to display in your menu", 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/nav_menu_options/top', $section );
			},
			$screen
		);

		add_settings_field(
			self::MENU_SELECT,
			esc_html( __( 'Select Menu', 'bigcommerce' ) ),
			[ $this, 'menu_select', ],
			$screen,
			self::NAME,
			[
				'option'    => self::MENU_SELECT,
				'label_for' => 'field-' . self::MENU_SELECT,
			]
		);

		add_settings_field(
			self::MENU_NAME,
			esc_html( __( 'New Menu Name', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			$screen,
			self::NAME,
			[
				'option'    => self::MENU_NAME,
				'label_for' => 'field-' . self::MENU_NAME,
				'type'      => 'text',
				'class'     => 'bc-settings-new-menu-field',
			]
		);

		add_settings_field(
			self::ITEMS_SELECT . '-categories',
			esc_html( __( 'Categories', 'bigcommerce' ) ),
			[ $this, 'items_select', ],
			$screen,
			self::NAME,
			[
				'option'  => self::ITEMS_SELECT,
				'choices' => [
					'categories' => __( 'Add Categories to Menu', 'bigcommerce' ),
				],
			]
		);

		add_settings_field(
			self::ITEMS_SELECT . '-brands',
			esc_html( __( 'Brands', 'bigcommerce' ) ),
			[ $this, 'items_select', ],
			$screen,
			self::NAME,
			[
				'option'  => self::ITEMS_SELECT,
				'choices' => [
					'brands' => __( 'Add Brands to Menu', 'bigcommerce' ),
				],
			]
		);

		add_settings_field(
			self::ITEMS_SELECT . '-account',
			esc_html( __( 'Account', 'bigcommerce' ) ),
			[ $this, 'items_select', ],
			$screen,
			self::NAME,
			[
				'option'  => self::ITEMS_SELECT,
				'choices' => [
					'profile' => __( 'Account Profile', 'bigcommerce' ),
					'orders'  => __( 'Order History', 'bigcommerce' ),
					'address' => __( 'Shipping Address', 'bigcommerce' ),
				],
			]
		);

		add_settings_field(
			self::ITEMS_SELECT . '-cart',
			esc_html( __( 'Cart', 'bigcommerce' ) ),
			[ $this, 'items_select', ],
			$screen,
			self::NAME,
			[
				'option'  => self::ITEMS_SELECT,
				'choices' => [
					'cart'     => __( 'Add Cart to Menu', 'bigcommerce' ),
					'minicart' => __( 'Show mini-cart widget when users click on the cart menu item', 'bigcommerce' ),
				],
			]
		);

		add_settings_field(
			self::ITEMS_SELECT . '-products',
			esc_html( __( 'All Products', 'bigcommerce' ) ),
			[ $this, 'items_select', ],
			$screen,
			self::NAME,
			[
				'option'  => self::ITEMS_SELECT,
				'choices' => [
					'products' => __( 'Add All Products to Menu', 'bigcommerce' ),
				],
			]
		);
	}

	/**
	 * Render the nav menu select box
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function menu_select( $args ) {
		$menus   = wp_get_nav_menus();
		$default = $this->default_menu_selection( $menus );
		$options = array_map( function ( $menu ) use ( $default ) {
			return sprintf( '<option value="%d" %s>%s</option>', (int) $menu->term_id, selected( $default, $menu->term_id, false ), esc_html( $menu->name ) );
		}, $menus );
		printf( '<select name="%s" id="%s" class="regular-text bc-field-choices" data-js="bc-settings-select-menu-field" required>', esc_attr( $args['option'] ), esc_attr( $args['label_for'] ) );
		printf( '<option value="new">%s</option>', esc_html( __( ' — Create a New Menu — ', 'bigcommerce' ) ) );
		echo implode( "\n", $options );
		echo '</select>';
	}

	/**
	 * Identify which nav menu to select by default. Try to make an
	 * intelligent guess based on what's available.
	 *
	 * @param array $menus
	 *
	 * @return int
	 */
	private function default_menu_selection( $menus ) {
		$registered_locations = get_registered_nav_menus();
		$recently_edited      = absint( get_user_option( 'nav_menu_recently_edited' ) );
		$menu_locations       = get_nav_menu_locations();

		// If there's only one menu location in the theme, pick the menu assigned to it
		if ( count( $registered_locations ) === 1 && array_key_exists( key( $registered_locations ), $menu_locations ) ) {
			return absint( $menu_locations[ key( $registered_locations ) ] );
		}

		// If there is only one menu assigned to a location, pick it
		if ( count( $menu_locations ) === 1 ) {
			return reset( $menu_locations );
		}

		// If the user recently edited a menu, pick it
		if ( $recently_edited ) {
			return $recently_edited;
		}

		// If we have _any_ menus, pick one
		if ( $menus ) {
			return reset( $menus )->term_id;
		}

		// There are no menus
		return 0;
	}

	/**
	 * Render the checkboxes for the menu item choices
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public function items_select( $args ) {
		$template = '<label class="bc-settings-section__nav-menu-item-label bc-settings-section__nav-menu-item-label--%s"><input type="checkbox" name="%s[]" value="%s" checked class="bc-settings-section__nav-menu-item-checkbox" /> <span>%s</span></label>';
		$choices  = [];
		foreach ( $args['choices'] as $key => $label ) {
			$choices[] = sprintf( $template, sanitize_html_class( $key ), esc_attr( $args['option'] ), esc_attr( $key ), esc_html( $label ) );
		}
		echo implode( ' ', $choices );
	}
}
