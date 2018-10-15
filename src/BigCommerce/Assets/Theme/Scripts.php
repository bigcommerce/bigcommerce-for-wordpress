<?php


namespace BigCommerce\Assets\Theme;


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

	public function __construct( $asset_directory, $version, JS_Config $config, JS_Localization $localization ) {
		$this->directory    = trailingslashit( $asset_directory );
		$this->version      = $version;
		$this->config       = $config;
		$this->localization = $localization;
	}

	/**
	 * Enqueue scripts
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$manifest_scripts = $debug ? 'manifest.js' : 'manifest.min.js';
		$vendor_scripts   = $debug ? 'vendor.js' : 'vendor.min.js';
		$plugin_scripts   = $debug ? 'scripts.js' : 'scripts.min.js';

		// TODO: Need to pull in `embedded-checkout` using NPM, and include it as a part of the `vendor` bundle. It will be provided by @bigcommerce/checkout-sdk.
		$checkout_src = $this->directory . 'js/dist/embedded-checkout.umd.js';
		$manifest_src = $this->directory . 'js/dist/' . $manifest_scripts;
		$vendor_src   = $this->directory . 'js/dist/' . $vendor_scripts;
		$plugin_src   = $this->directory . 'js/dist/' . $plugin_scripts;

		wp_register_script( 'bigcommerce-manifest', $manifest_src, [], $this->version, true );
		wp_register_script( 'bigcommerce-checkout', $checkout_src, [], $this->version, true );
		wp_register_script( 'bigcommerce-vendors', $vendor_src, [ 'bigcommerce-manifest', 'bigcommerce-checkout', 'jquery' ], $this->version, true );
		wp_register_script( 'bigcommerce-scripts', $plugin_src, [ 'bigcommerce-vendors' ], $this->version, true );

		wp_localize_script( 'bigcommerce-scripts', 'bigcommerce_config', $this->config->get_data() );
		wp_localize_script( 'bigcommerce-scripts', 'bigcommerce_i18n', $this->localization->get_data() );

		wp_enqueue_script( 'bigcommerce-scripts' );
	}
}