<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the gift certificate balance form.
 */
class Gift_Certificate_Balance extends Shortcode_Block {
	/**
	 * The name of the block.
	 * @var string
	 */
	const NAME = 'bigcommerce/gift-certificate-balance';

	/**
	 * The block icon.
	 * @var string
	 */
	protected $icon = 'money';

	/**
	 * The associated shortcode for the gift certificate balance block.
	 * @var string
	 */
	protected $shortcode = Shortcodes\Gift_Certificate_Balance::NAME;

	/**
	 * Returns the block title.
	 *
	 * @return string The title of the block.
	 */
	protected function title() {
		return __( 'BigCommerce Gift Certificate Balance', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'Gift Certificate Balance', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the block's image.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Gift-Cert-Balance.png' );
	}

	/**
	 * Adds additional keywords for the block.
	 *
	 * @return array The list of keywords, including checkout.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}