<?php

namespace BigCommerce\Assets\Admin;

/**
 * Handles the enqueueing and registration of admin scripts and Gutenberg-specific scripts.
 *
 * @package BigCommerce\Assets\Admin
 */
class Scripts {

    /**
     * @var string $directory Path to the plugin assets directory.
     */
    private $directory;

    /**
     * @var string $version The asset build version.
     */
    private $version;

    /**
     * @var JS_Config $config Configuration for JavaScript.
     */
    private $config;

    /**
     * @var JS_Localization $localization Localization data for JavaScript.
     */
    private $localization;

    /**
     * Constructor.
     *
     * @param string          $asset_directory Path to the plugin assets directory.
     * @param string          $version Asset build version.
     * @param JS_Config       $config Configuration object for JavaScript.
     * @param JS_Localization $localization Localization object for JavaScript.
     */
    public function __construct( $asset_directory, $version, JS_Config $config, JS_Localization $localization ) {
        $this->directory    = trailingslashit( $asset_directory );
        $this->version      = $version;
        $this->config       = $config;
        $this->localization = $localization;
    }

    /**
     * Enqueue admin and Gutenberg scripts.
     *
     * Registers and enqueues required scripts, and localizes them with configuration
     * and localization data. Delays certain script outputs until after the admin footer scripts.
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
            'wp-block-editor',
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

        add_action( 'admin_print_footer_scripts', [ $this, 'print_footer_scripts' ], 60, 0 );
        add_action( 'admin_print_footer_scripts', [ $this, 'print_footer_scripts' ], 60, 0 );
    }

    /**
     * Print footer scripts.
     *
     * Enqueues the admin scripts and reprocesses the script queue
     * to ensure correct execution timing.
     */
    public function print_footer_scripts() {
        // Do not enqueue Gutenberg scripts.

        wp_enqueue_script( 'bigcommerce-admin-scripts' );

        add_action( "admin_footer-" . $GLOBALS['hook_suffix'], '_wp_footer_scripts' );
    }
}
