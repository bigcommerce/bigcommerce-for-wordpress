<?php


namespace BigCommerce\Assets\Theme;


use BigCommerce\Customizer\Sections\Colors;

class Styles {

	/**
	 * @var string Path to the plugin assets directory
	 */
	private $directory;

	/**
	 * @var string The asset build version
	 */
	private $version;

	public function __construct( $asset_directory, $version ) {
		$this->directory = trailingslashit( $asset_directory );
		$this->version   = $version;
	}

	/**
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_styles() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$css_file = apply_filters( 'bigcommerce/assets/stylesheet', $debug ? 'master.css' : 'master.min.css' );
		$css_src  = $this->directory . 'css/' . $css_file;

		wp_register_style( 'bigcommerce-styles', $css_src, [], $this->version );

		if ( get_theme_mod( Colors::CSS, Colors::CSS_FULL ) !== Colors::CSS_OFF ) {
			wp_enqueue_style( 'bigcommerce-styles' );
		}
	}
}