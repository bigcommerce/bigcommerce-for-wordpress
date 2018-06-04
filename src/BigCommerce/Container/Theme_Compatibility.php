<?php


namespace BigCommerce\Container;


use BigCommerce\Compatibility\Template_Compatibility;
use Pimple\Container;

class Theme_Compatibility extends Provider {
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
	}
}