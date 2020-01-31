<?php


namespace BigCommerce\Assets\Admin;


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
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_scripts() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$manifest_scripts  = $debug ? 'manifest.js' : 'manifest.min.js';
		$vendor_scripts    = $debug ? 'vendor.js' : 'vendor.min.js';
		$admin_scripts     = $debug ? 'scripts.js' : 'scripts.min.js';
		$gutenberg_scripts = $debug ? 'scripts.js' : 'scripts.min.js';

		$manifest_src  = $this->directory . 'js/dist/admin/' . $manifest_scripts;
		$vendor_src    = $this->directory . 'js/dist/admin/' . $vendor_scripts;
		$admin_src     = $this->directory . 'js/dist/admin/' . $admin_scripts;
		$gutenberg_src = $this->directory . 'js/dist/admin/gutenberg/' . $gutenberg_scripts;

		wp_register_script( 'bigcommerce-admin-manifest', $manifest_src, [
			'wp-util',
			'media-upload',
			'media-views',
		], $this->version, true );
		wp_register_script( 'bigcommerce-admin-vendors', $vendor_src, [ 'bigcommerce-admin-manifest' ], $this->version, true );
		wp_register_script( 'bigcommerce-admin-scripts', $admin_src, [ 'bigcommerce-admin-vendors', 'wp-i18n' ], $this->version, true );
		wp_register_script( 'bigcommerce-gutenberg-scripts', $gutenberg_src, [
			'wp-i18n',
			'wp-editor',
			'wp-element',
			'wp-blocks',
			'wp-plugins',
			'wp-components',
			'bigcommerce-admin-scripts',
		], $this->version, false );
		add_action( 'admin_print_scripts-post.php', function() {
			wp_add_inline_script( 'wp-edit-post', 'window._wpLoadBlockEditor.then( window.bigcommerce_gutenberg_config.initPlugins() );' );
		});

		wp_localize_script( 'bigcommerce-admin-scripts', 'bigcommerce_admin_config', $this->config->get_data() );
		wp_localize_script( 'bigcommerce-admin-scripts', 'bigcommerce_admin_i18n', $this->localization->get_data() );
		wp_localize_script( 'bigcommerce-gutenberg-scripts', 'bigcommerce_gutenberg_config', $this->config->get_gutenberg_data() );

		/*
		 * Rather than enqueuing this immediately, delay until after
		 * admin_print_footer_scripts:50. This is when the WP visual
		 * editor prints the tinymce config.
		 */
		add_action( 'admin_print_footer_scripts', [ $this, 'print_footer_scripts' ], 60, 0 );
	}

	/**
	 * @action admin_print_footer_scripts
	 */
	public function print_footer_scripts() {
		// do not enqueue gutenberg scripts

		wp_enqueue_script( 'bigcommerce-admin-scripts' );

		// since footer scripts have already printed, process the queue again on the next available action
		add_action( "admin_footer-" . $GLOBALS[ 'hook_suffix' ], '_wp_footer_scripts' );
	}
}
