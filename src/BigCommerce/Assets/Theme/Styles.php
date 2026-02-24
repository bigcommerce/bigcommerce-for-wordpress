<?php

namespace BigCommerce\Assets\Theme;

use BigCommerce\Customizer\Sections\Colors;

/**
 * Manages the enqueuing of CSS stylesheets for the theme. This includes handling the inclusion of
 * asset files and applying any filters to the stylesheet before it is enqueued. It also checks the
 * theme settings to conditionally load the styles based on user preferences.
 *
 * @package BigCommerce\Assets\Theme
 */
class Styles {

	/**
	 * @var string Path to the plugin assets directory
	 */
	private $directory;

	/**
	 * @var string The asset build version
	 */
	private $version;

	/**
	 * Styles constructor.
	 *
	 * Initializes the Styles class with the asset directory and version.
	 *
	 * @param string $asset_directory The path to the plugin assets directory.
	 * @param string $version The version of the asset build.
	 */
	public function __construct( $asset_directory, $version ) {
		$this->directory = trailingslashit( $asset_directory );
		$this->version   = $version;
	}

	/**
	 * Enqueues the CSS styles for the frontend.
	 *
	 * This method registers and enqueues the main stylesheet, allowing for customization of the
	 * stylesheet filename through filters. It also conditionally loads the stylesheet based on
	 * the theme settings (e.g., whether the full CSS file should be loaded).
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_styles() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		/**
		 * Filters assets stylesheet file.
		 *
		 * @param string $css_file CSS file name.
		 */
		$css_file = apply_filters( 'bigcommerce/assets/stylesheet', $debug ? 'master.css' : 'master.min.css' );
		$css_src  = $this->directory . 'css/' . $css_file;

		wp_register_style( 'bigcommerce-styles', $css_src, [], $this->version );

		if ( get_theme_mod( Colors::CSS, Colors::CSS_FULL ) !== Colors::CSS_OFF ) {
			wp_enqueue_style( 'bigcommerce-styles' );
		}
	}
}
