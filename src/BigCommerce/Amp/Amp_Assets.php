<?php

namespace BigCommerce\Amp;

use BigCommerce\Settings;

/**
 * Class Amp_Assets
 *
 * Handles loading styles and scripts needed for AMP functionality and layouts.
 *
 * @package BigCommerce\Amp
 */
class Amp_Assets {

    /**
     * Path to the plugin assets directory.
     *
     * @var string
     */
    private $directory;

    /**
     * URL of the plugin asset directory.
     *
     * @var string
     */
    private $asset_directory_url;

    /**
     * Path to the customizer template file.
     *
     * @var string
     */
    private $customizer_template_file;

    /**
     * Constructor for the Amp_Assets class.
     *
     * @param string $asset_directory Path to the plugin assets directory.
     * @param string $asset_directory_url URL to the plugin asset directory.
     * @param string $customizer_template_file Path to the customizer template file.
     */
    public function __construct( $asset_directory, $asset_directory_url, $customizer_template_file ) {
        $this->directory                = trailingslashit( $asset_directory );
        $this->asset_directory_url      = trailingslashit( $asset_directory_url );
        $this->customizer_template_file = $customizer_template_file;
    }

    /**
     * Outputs custom AMP CSS styles directly in the document.
     *
     * Loads the appropriate stylesheet based on the current page (e.g., cart or general) 
     * and performs necessary adjustments like replacing relative paths with absolute URLs.
     */
    public function styles() {
        $debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

        $post         = get_post();
        $css_file     = $debug ? 'master-amp.css' : 'master-amp.min.css';
        $cart_page_id = intval( get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 ) );
        if ( $post->ID === $cart_page_id ) {
            $css_file = $debug ? 'cart-amp.css' : 'cart-amp.min.css';
        }
        $css_src      = $this->directory . 'css/' . $css_file;
        $css_contents = file_get_contents( $css_src );

        // Use absolute URLs for web fonts.
        $css_contents = str_replace( '../fonts', esc_url( $this->asset_directory_url . 'fonts' ), $css_contents );

        // Remove all !important rules.
        $css_contents = str_replace( '!important', '', $css_contents );

        echo $css_contents; // WPCS: XSS okay. CSS loaded from our own CSS file.
    }

    /**
     * Retrieves AMP script handles.
     *
     * Relevant only in Classic Mode; component scripts are automatically included 
     * in Native/Paired modes. Used in the `amp_post_template_data` filter.
     *
     * @see amp_register_default_scripts()
     * @return string[] List of script handles.
     */
    public function scripts() {
        $handles = array(
            'amp-carousel',
            'amp-form',
            'amp-bind',
            'amp-sidebar',
            'amp-list',
            'amp-mustache',
        );

        if ( is_archive() ) {
            $handles[] = 'amp-lightbox';
        }

        return $handles;
    }

    /**
     * Filters the main stylesheet when in AMP paired mode.
     *
     * Determines the appropriate stylesheet (debug or minified version) based on the
     * page type (e.g., cart page or general page).
     *
     * @param string $stylesheet Stylesheet file name.
     * @return string Modified stylesheet file name.
     */
    public function filter_stylesheet( $stylesheet ) {
        $debug        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
        $stylesheet   = $debug ? 'master-amp.css' : 'master-amp.min.css';
        $post         = get_post();
        $cart_page_id = intval( get_option( Settings\Sections\Cart::OPTION_CART_PAGE_ID, 0 ) );
        if ( $post->ID === $cart_page_id ) {
            $stylesheet = $debug ? 'cart-amp.css' : 'cart-amp.min.css';
        }

        return $stylesheet;
    }
}
