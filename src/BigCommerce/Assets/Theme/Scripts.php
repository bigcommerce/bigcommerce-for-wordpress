<?php


namespace BigCommerce\Assets\Theme;


use BigCommerce\Pages\Checkout_Page;

/**
 * Manages the enqueuing of JavaScript files for the theme. It handles the inclusion of asset files,
 * conditional script loading based on page type, and localization of JavaScript data.
 *
 * @package BigCommerce\Assets\Theme
 */
class Scripts {
	/**
	 * @var string Path to the plugin assets directory
	 */
	private $directory;

	/**
	 * @var string The asset build version
	 */
	private $version;

	/**
	 * @var JS_Config
	 */
	private $config;

	/**
	 * @var JS_Localization
	 */
	private $localization;

	/**
	 * Scripts constructor.
	 *
	 * Initializes the Scripts class with the asset directory, version, and necessary dependencies.
	 *
	 * @param string $asset_directory The path to the plugin assets directory.
	 * @param string $version The version of the asset build.
	 * @param JS_Config $config The JS_Config object for configuration data.
	 * @param JS_Localization $localization The JS_Localization object for localized strings.
	 */
	public function __construct( $asset_directory, $version, JS_Config $config, JS_Localization $localization ) {
		$this->directory    = trailingslashit( $asset_directory );
		$this->version      = $version;
		$this->config       = $config;
		$this->localization = $localization;
	}

	/**
	 * Enqueues JavaScript files for the frontend.
	 *
	 * This method registers and enqueues the necessary JavaScript files for the theme, including
	 * manifest, vendor, and plugin scripts. It also conditionally loads the BigCommerce checkout SDK
	 * if the current page is the checkout page.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$manifest_scripts = $debug ? 'manifest.js' : 'manifest.min.js';
		$vendor_scripts   = $debug ? 'vendor.js' : 'vendor.min.js';
		$plugin_scripts   = $debug ? 'scripts.js' : 'scripts.min.js';

		$manifest_src = $this->directory . 'js/dist/' . $manifest_scripts;
		$vendor_src   = $this->directory . 'js/dist/' . $vendor_scripts;
		$plugin_src   = $this->directory . 'js/dist/' . $plugin_scripts;

		if ( is_page( get_option( Checkout_Page::NAME, 0 ) ) ) {
			wp_enqueue_script( 'bigcommerce-checkout-sdk', 'https://checkout-sdk.bigcommerce.com/v1/loader.js', [], $this->version, true );
		}

		wp_register_script( 'bigcommerce-manifest', $manifest_src, [], $this->version, true );
		wp_register_script( 'bigcommerce-vendors', $vendor_src, [ 'bigcommerce-manifest', 'jquery' ], $this->version, true );
		wp_register_script( 'bigcommerce-scripts', $plugin_src, [ 'bigcommerce-vendors' ], $this->version, true );

		wp_localize_script( 'bigcommerce-scripts', 'bigcommerce_config', $this->config->get_data() );
		wp_localize_script( 'bigcommerce-scripts', 'bigcommerce_i18n', $this->localization->get_data() );

		wp_enqueue_script( 'bigcommerce-scripts' );
	}
}
