<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Gift_Certificate_Form
 *
 * A block to display the gift certificate form
 */
class Gift_Certificate_Balance extends Shortcode_Block {
	const NAME = 'bigcommerce/gift-certificate-balance';

	protected $icon = 'money';
	protected $shortcode = Shortcodes\Gift_Certificate_Balance::NAME;

	protected function title() {
		return __( 'BigCommerce Gift Certificate Balance', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Gift Certificate Balance', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Gift-Cert-Balance.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}