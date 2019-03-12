<?php

namespace BigCommerce\Amp;

use BigCommerce\Settings;

/**
 * Class Amp_Assets
 *
 * Load styles and scripts we'll need for our AMP functionality and layouts.
 *
 * @package BigCommerce\Amp
 */
class Amp_Assets {

	/**
	 * @var string Path to the plugin assets directory
	 */
	private $directory;

	/**
	 * URL of asset directory
	 *
	 * @var string
	 */
	private $asset_directory_url;

	/**
	 * @var string Path to the plugin assets directory
	 */
	private $customizer_template_file;

	/**
	 * Assets constructor.
	 */
	public function __construct( $asset_directory, $asset_directory_url, $customizer_template_file ) {
		$this->directory                = trailingslashit( $asset_directory );
		$this->asset_directory_url      = trailingslashit( $asset_directory_url );
		$this->customizer_template_file = $customizer_template_file;
	}

	/**
	 * Add custom CSS.
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
	 * Get AMP script handles.
	 *
	 * This is only relevant in the Classic Mode, as component scripts are automatically in Native/Paired modes.
	 * This is used in an 'amp_post_template_data' filter.
	 *
	 * @see amp_register_default_scripts()
	 * @return string[] Script handles.
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
	 * Filter the main stylesheet when we are in AMP paired mode.
	 *
	 * @param string $stylesheet Stylesheet file name.
	 *
	 * @return mixed
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
