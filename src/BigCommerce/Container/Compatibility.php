<?php


namespace BigCommerce\Container;


use BigCommerce\Compatibility\Template_Compatibility;
use Pimple\Container;

class Compatibility extends Provider {
	const TEMPLATES = 'theme_compat.templates';

	public function register( Container $container ) {
		$container[ self::TEMPLATES ] = function ( Container $container ) {
			return new Template_Compatibility();
		};

		add_filter( 'page_template', $this->create_callback( 'page_template_override', function ( $template, $type, $templates ) use ( $container ) {
			return $container[ self::TEMPLATES ]->override_page_template( $template, $type, $templates );
		} ), 10, 3 );

		add_action( 'after_setup_theme', $this->create_callback( 'woo_compat_functions', function () use ( $container ) {
			include_once( dirname( $container[ 'plugin_file' ] ) . '/src/BigCommerce/Compatibility/woocommerce-functions.php' );
		} ), 10, 0 );

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
	}
}