<?php

namespace BigCommerce\Assets\Admin;

/**
 * Handles the enqueueing and registration of admin and Gutenberg-specific CSS styles.
 *
 * @package BigCommerce\Assets\Admin
 */
class Styles {
    /**
     * @var string Path to the plugin assets directory.
     */
    private $directory;

    /**
     * @var string The asset build version.
     */
    private $version;

    /**
     * Constructor.
     *
     * Initializes the class with the provided directory path and version.
     *
     * @param string $asset_directory Path to the plugin assets directory.
     * @param string $version Asset build version.
     */
    public function __construct( $asset_directory, $version ) {
        $this->directory = trailingslashit( $asset_directory );
        $this->version   = $version;
    }

    /**
     * Enqueue admin and Gutenberg styles.
     *
     * Registers and enqueues the required CSS styles for the BigCommerce admin panel
     * and Gutenberg editor. The styles are minified for production, with debug support
     * for unminified versions.
     *
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