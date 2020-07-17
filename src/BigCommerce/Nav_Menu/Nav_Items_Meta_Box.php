<?php


namespace BigCommerce\Nav_Menu;

use BigCommerce\Meta_Boxes\Meta_Box;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Responsible for handling the BigCommerce menu items
 * in the nav menu admin
 */
class Nav_Items_Meta_Box extends Meta_Box {
	const NAME       = 'bigcommerce_nav_menu';
	const ACTION     = 'bigcommerce_add_menu_items';
	const CATEGORIES = 'bigcommerce_categories';
	const BRANDS     = 'bigcommerce_brands';

	const USER_INITIALIZED = 'bigcommerce_nav_settings_initialized';

	protected function get_name() {
		return self::NAME;
	}

	protected function get_title() {
		return __( 'BigCommerce', 'bigcommerce' );
	}

	/**
	 * Renders the meta box, emulating the markup of the WP
	 * core nav meta boxes to take advantage of the nav JS
	 *
	 * @param object $object
	 *
	 * @return void
	 */
	public function render( $object ) {
		$taxonomies = [
			Product_Category::NAME,
			Brand::NAME,
		];

		// prepare the data that WP expects
		$items      = array_map( function ( $tax ) {
			$taxonomy = get_taxonomy( $tax );
			$item     = (object) [
				'db_id'            => 0,
				'object_id'        => 0,
				'label'            => $taxonomy->label,
				'object'           => $tax,
				'menu_item_parent' => 0,
				'type'             => Dynamic_Menu_Items::TYPE,
				'title'            => $taxonomy->label,
				'url'              => get_post_type_archive_link( Product::NAME ),
				'target'           => '',
				'attr_title'       => '',
				'classes'          => [],
				'xfn'              => '',
			];

			return wp_setup_nav_menu_item( $item );
		}, $taxonomies );

		// Render the HTML to emulate the core WP meta boxes
		$walker = new \Walker_Nav_Menu_Checklist();
		echo '<div id="bigcommerce-menu-items">';
		echo '<div class="tabs-panel tabs-panel-active"><ul class="categorychecklist form-no-clear">';
		echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $items ), 0, (object) [ 'walker' => $walker ] );
		echo '</ul></div>';

		echo '<p class="button-controls wp-clearfix"><span class="add-to-menu">';
		echo '';
		ob_start();
		wp_nav_menu_disabled_check( $GLOBALS[ 'nav_menu_selected_id' ] );
		$disabled = ob_get_clean();
		printf(
			'<input type="submit" %s class="button submit-add-to-menu right" value="%s" name="add-bigcommerce-menu-item" id="%s" />',
			$disabled,
			esc_attr( __( 'Add to Menu', 'bigcommerce' ) ),
			'submit-bigcommerce-menu-items' // must match the ID of the wrapper <div> above
		);
		echo '<span class="spinner"></span>';
		echo '</span></p></div>';
	}

	public function set_nav_menu_screen_options(){
		$already_updated_user = get_user_option( self::USER_INITIALIZED );

		if ( empty( $already_updated_user ) ) {
			$user_id    = get_current_user_id();
			$prev_value = (array) get_user_option( 'metaboxhidden_nav-menus', $user_id );

			// Menus to be displayed by default
			$options = [
				self::NAME,
				'add-' . Product_Category::NAME,
				'add-' . Brand::NAME,
				'add-post-type-' . Product::NAME
			];

			$meta_value = array_diff( $prev_value, $options );

			update_user_option( $user_id, 'metaboxhidden_nav-menus', $meta_value, $prev_value );
			update_user_option( $user_id, self::USER_INITIALIZED, 1, true );
		}
	}

	protected function get_screen() {
		return 'nav-menus';
	}

	/**
	 * @return string One of 'normal', 'side', or 'advanced'
	 */
	protected function get_context() {
		return 'side';
	}

	/**
	 * Handle the ajax request to add our custom menu
	 * item type. Hook in just before the standard
	 * WP handler, which would reject our data.
	 *
	 * @return void
	 * @action wp_ajax_add-menu-item 0
	 * @see wp_ajax_add_menu_item
	 */
	public function handle_ajax_request() {
		// identify any bigcommerce menu items in the submission
		$menu_items_data = [];

		$menu_item = filter_input_array( INPUT_POST, [
			'menu-item' => [
				'filter' => FILTER_SANITIZE_STRING, 
				'flags'  => FILTER_REQUIRE_ARRAY,
			]
		] );

		if ( ! $menu_item || ! $menu_item['menu-item'] ) {
			return;
		}

		foreach ( $menu_item as $menu_item_data ) {
			if ( Dynamic_Menu_Items::TYPE !== $menu_item_data[ 'menu-item-type' ] ) {
				continue;
			}
			$menu_items_data[] = $menu_item_data;
		}

		if ( empty( $menu_items_data ) ) {
			return; // not a request we need to handle
		}

		// validate that we have a legitimate request

		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( - 1 );
		}

		// create the menu items

		require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

		$item_ids = [];
		foreach ( $menu_items_data as $submitted_item ) {
			$args = [
				'menu-item-db-id'       => isset( $submitted_item[ 'menu-item-db-id' ] ) ? $submitted_item[ 'menu-item-db-id' ] : '',
				'menu-item-object-id'   => isset( $submitted_item[ 'menu-item-object-id' ] ) ? $submitted_item[ 'menu-item-object-id' ] : '',
				'menu-item-object'      => isset( $submitted_item[ 'menu-item-object' ] ) ? $submitted_item[ 'menu-item-object' ] : '',
				'menu-item-parent-id'   => isset( $submitted_item[ 'menu-item-parent-id' ] ) ? $submitted_item[ 'menu-item-parent-id' ] : '',
				'menu-item-position'    => isset( $submitted_item[ 'menu-item-position' ] ) ? $submitted_item[ 'menu-item-position' ] : '',
				'menu-item-type'        => isset( $submitted_item[ 'menu-item-type' ] ) ? $submitted_item[ 'menu-item-type' ] : '',
				'menu-item-title'       => isset( $submitted_item[ 'menu-item-title' ] ) ? $submitted_item[ 'menu-item-title' ] : '',
				'menu-item-url'         => isset( $submitted_item[ 'menu-item-url' ] ) ? $submitted_item[ 'menu-item-url' ] : '',
				'menu-item-description' => isset( $submitted_item[ 'menu-item-description' ] ) ? $submitted_item[ 'menu-item-description' ] : '',
				'menu-item-attr-title'  => isset( $submitted_item[ 'menu-item-attr-title' ] ) ? $submitted_item[ 'menu-item-attr-title' ] : '',
				'menu-item-target'      => isset( $submitted_item[ 'menu-item-target' ] ) ? $submitted_item[ 'menu-item-target' ] : '',
				'menu-item-classes'     => isset( $submitted_item[ 'menu-item-classes' ] ) ? $submitted_item[ 'menu-item-classes' ] : '',
				'menu-item-xfn'         => isset( $submitted_item[ 'menu-item-xfn' ] ) ? $submitted_item[ 'menu-item-xfn' ] : '',
			];

			$item_ids[] = wp_update_nav_menu_item( 0, 0, $args );
		}

		// output the menu item HTML

		$menu_items = array_filter( array_map( function ( $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj        = wp_setup_nav_menu_item( $menu_obj );
				$menu_obj->label = $menu_obj->title;

				return $menu_obj;
			}

			return null;
		}, $item_ids ) );

		$menu = filter_input( INPUT_POST, 'menu', FILTER_SANITIZE_STRING );
		/** This filter is documented in wp-admin/includes/nav-menu.php */
		$walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $menu );

		if ( ! class_exists( $walker_class_name ) ) {
			wp_die( 0 );
		}

		if ( ! empty( $menu_items ) ) {
			$args = [
				'after'       => '',
				'before'      => '',
				'link_after'  => '',
				'link_before' => '',
				'walker'      => new $walker_class_name,
			];
			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}
		wp_die();
	}
}