<?php


namespace BigCommerce\Assets\Admin;


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
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_styles() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$css_file           = $debug ? 'bc-admin.css' : 'bc-admin.min.css';
		$gutenberg_css_file = $debug ? 'bc-gutenberg.css' : 'bc-gutenberg.min.css';

		$css_src           = $this->directory . 'css/' . $css_file;
		$gutenberg_css_src = $this->directory . 'css/' . $gutenberg_css_file;

		wp_enqueue_style( 'bigcommerce-admin-styles', $css_src, [], $this->version );
		wp_enqueue_style( 'bigcommerce-gutenberg-admin-styles', $gutenberg_css_src, [], $this->version );
	}
}