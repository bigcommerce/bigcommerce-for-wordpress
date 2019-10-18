<?php


namespace BigCommerce\Container;


use BigCommerce\Customizer\Panels;
use BigCommerce\Customizer\Sections;
use BigCommerce\Customizer\Styles;
use Pimple\Container;

class Theme_Customizer extends Provider {
	const PANEL_PRIMARY           = 'customizer.panel.primary';
	const SECTION_BUTTONS         = 'customizer.section.buttons';
	const SECTION_COLORS          = 'customizer.section.colors';
	const SECTION_PRODUCT_SINGLE  = 'customizer.section.product_single';
	const SECTION_PRODUCT_ARCHIVE = 'customizer.section.product_archive';
	const SECTION_CART            = 'customizer.section.cart';
	const SECTION_CHECKOUT        = 'customizer.section.checkout';
	const STYLES                  = 'customizer.styles';

	public function register( Container $container ) {
		$container[ self::PANEL_PRIMARY ] = function ( Container $container ) {
			return new Panels\Primary();
		};

		$container[ self::SECTION_BUTTONS ] = function ( Container $container ) {
			return new Sections\Buttons();
		};

		$container[ self::SECTION_COLORS ] = function ( Container $container ) {
			return new Sections\Colors();
		};

		$container[ self::SECTION_PRODUCT_SINGLE ] = function ( Container $container ) {
			return new Sections\Product_Single();
		};

		$container[ self::SECTION_PRODUCT_ARCHIVE ] = function ( Container $container ) {
			return new Sections\Product_Archive();
		};

		$container[ self::SECTION_CART ] = function ( Container $container ) {
			return new Sections\Cart();
		};

		$container[ self::SECTION_CHECKOUT ] = function ( Container $container ) {
			return new Sections\Checkout();
		};

		$container[ self::STYLES ] = function ( Container $container ) {
			$path = dirname( $container[ 'plugin_file' ] ) . '/assets/customizer.template.css';

			return new Styles( $path );
		};

		add_action( 'customize_register', $this->create_callback( 'customize_register', function ( $wp_customize ) use ( $container ) {
			$container[ self::PANEL_PRIMARY ]->register( $wp_customize );
			$container[ self::SECTION_BUTTONS ]->register( $wp_customize );
			$container[ self::SECTION_COLORS ]->register( $wp_customize );
			$container[ self::SECTION_PRODUCT_SINGLE ]->register( $wp_customize );
			$container[ self::SECTION_PRODUCT_ARCHIVE ]->register( $wp_customize );

			if ( get_option( \BigCommerce\Settings\Sections\Cart::OPTION_ENABLE_CART, true ) ) {
				$container[ self::SECTION_CART ]->register( $wp_customize );
			}

			if ( get_option( \BigCommerce\Settings\Sections\Cart::OPTION_EMBEDDED_CHECKOUT, true ) ) {
				$container[ self::SECTION_CHECKOUT ]->register( $wp_customize );
			}
		} ), 10, 1 );

		add_action( 'wp_head', $this->create_callback( 'customizer_styles', function () use ( $container ) {
			$container[ self::STYLES ]->print_styles();
		} ), 10, 0 );
	}

}
