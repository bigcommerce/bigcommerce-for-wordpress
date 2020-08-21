<?php


namespace BigCommerce\Container;


use BigCommerce\Compatibility\Template_Compatibility;
use BigCommerce\Compatibility\Themes\Theme_Factory;
use BigCommerce\Compatibility\WooCommerce\Facade;
use BigCommerce\Compatibility\WooCommerce\Cart as WC_Cart;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Container\Api;
use Pimple\Container;

class Compatibility extends Provider {
	const TEMPLATES     = 'theme_compat.templates';
	const THEME         = 'theme_compat.theme';
	const WC_FACADE     = 'woo_compat.wc_facade';

	public function register( Container $container ) {
		$container[ self::TEMPLATES ] = function ( Container $container ) {
			return new Template_Compatibility();
		};
		
		$container[ self::THEME ] = function ( Container $container ) {
			$factory = new Theme_Factory();

			$wp_theme = wp_get_theme();

			if ( $wp_theme->parent() ) {
				$version = $wp_theme->parent()->get( 'Version' );
			} else {
				$version = $wp_theme->get( 'Version' );
			}

			return $factory->make( $wp_theme->get_template(), $version );
		};

		$container[ self::WC_FACADE ] = function ( Container $container ) {
			$wc_cart = new WC_Cart( $container[ Api::FACTORY ]->cart() );
			return new Facade( $wc_cart );
		};

		add_filter( 'page_template', $this->create_callback( 'page_template_override', function ( $template, $type, $templates ) use ( $container ) {
			return $container[ self::TEMPLATES ]->override_page_template( $template, $type, $templates );
		} ), 10, 3 );
		
		add_action( 'setup_theme', $this->create_callback( 'woo_compat_functions', function () use ( $container ) {
			if ( filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING ) === 'activate' && filter_input( INPUT_GET, 'plugin', FILTER_SANITIZE_STRING ) === 'woocommerce/woocommerce.php' ) {
				return;
			}
			include_once( dirname( $container[ 'plugin_file' ] ) . '/src/BigCommerce/Compatibility/woocommerce-functions.php' );

			$container[ self::THEME ]->load_compat_functions();
		} ), 10, 0 );
		
		add_action( 'wp', $this->create_callback( 'woo_compat_theme_flatsome_accounts_page_fix', function () {
			if ( is_page( get_option( \BigCommerce\Pages\Account_Page::NAME, 0 ) ) ) {
			    remove_filter( 'the_content', 'flatsome_contentfix' );
			}
		} ) );
		
		add_action( 'init', $this->create_callback( 'woo_compat_theme_flatsome_replace_wc_related_shortcodes', function () {
			if ( get_option( 'template' ) !== 'flatsome') {
				return;
			}
			
			global $shortcode_tags;
			foreach ( $shortcode_tags as $tag ) {
				if ( is_string( $tag ) && strpos( $tag, 'ux_product' ) !== false ) {
					add_shortcode( $tag, '__return_null' );
				}
			}
		} ) );
		
		add_action( 'init', $this->create_callback( 'wordpress_4_dot_9', function () use ( $container ) {
			if ( version_compare( $GLOBALS[ 'wp_version' ], '4.9', '<' ) ) {
				include_once( dirname( $container[ 'plugin_file' ] ) . '/src/BigCommerce/Compatibility/wordpress-4-dot-9.php' );
			}
		} ), 10, 0 );

		add_action( 'init', $this->create_callback( 'wordpress_5_dot_1', function () use ( $container ) {
			if ( version_compare( $GLOBALS[ 'wp_version' ], '5.1', '<' ) ) {
				include_once( dirname( $container[ 'plugin_file' ] ) . '/src/BigCommerce/Compatibility/wordpress-5-dot-1.php' );
			}
		} ), 10, 0 );
		
		add_action( 'pre_option_woocommerce_myaccount_page_id', $this->create_callback( 'woo_compat_filter_myaccount_page_id', function () {
			if ( is_user_logged_in() ) {
				return get_option( Account_Page::NAME, 0 );
			}
			return get_option( Login_Page::NAME, 0 );
		} ), 10, 0 );
	}
}