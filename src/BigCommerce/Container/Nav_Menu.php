<?php


namespace BigCommerce\Container;

use BigCommerce\Nav_Menu\Dynamic_Menu_Items;
use BigCommerce\Nav_Menu\Nav_Items_Customizer;
use BigCommerce\Nav_Menu\Nav_Items_Meta_Box;
use Pimple\Container;

class Nav_Menu extends Provider {
	const ITEMS      = 'navigation.items';
	const METABOX    = 'navigation.metabox';
	const CUSTOMIZER = 'navigation.customizer';

	public function register( Container $container ) {
		$this->menu_items( $container );
		$this->metabox( $container );
		$this->customizer( $container );
	}

	private function menu_items( Container $container ) {
		$container[ self::ITEMS ] = function ( Container $container ) {
			return new Dynamic_Menu_Items();
		};

		add_filter( 'wp_setup_nav_menu_item', $this->create_callback( 'setup_menu_item', function ( $item ) use ( $container ) {
			return $container[ self::ITEMS ]->setup_menu_item( $item );
		} ), 10, 1 );

		add_filter( 'wp_get_nav_menu_items', $this->create_callback( 'insert_dynamic_menu_items', function ( $items, $menu, $args ) use ( $container ) {
			return $container[ self::ITEMS ]->insert_dynamic_menu_items( $items, $menu, $args );
		} ), 20, 3 );
	}

	private function metabox( Container $container ) {
		$container[ self::METABOX ] = function ( Container $container ) {
			return new Nav_Items_Meta_Box();
		};

		add_action( 'load-nav-menus.php', $this->create_callback( 'register_metabox', function () use ( $container ) {
			$container[ self::METABOX ]->register();
		} ), 10, 0 );

		add_action( 'wp_ajax_add-menu-item', $this->create_callback( 'ajax_add_menu_item', function () use ( $container ) {
			$container[ self::METABOX ]->handle_ajax_request();
		} ), 0, 0 );
	}

	private function customizer( Container $container ) {
		$container[ self::CUSTOMIZER ] = function ( Container $container ) {
			return new Nav_Items_Customizer();
		};
		add_filter( 'customize_nav_menu_available_item_types', $this->create_callback( 'register_customizer_item_type', function ( $types ) use ( $container ) {
			return $container[ self::CUSTOMIZER ]->register_item_type( $types );
		} ), 10, 1 );

		add_filter( 'customize_nav_menu_available_items', $this->create_callback( 'register_customizer_menu_items', function ( $items, $type, $object, $page ) use ( $container ) {
			return $container[ self::CUSTOMIZER ]->register_menu_items( $items, $type, $object, $page );
		} ), 10, 4 );
	}

}