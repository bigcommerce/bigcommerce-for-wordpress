<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Gift_Certificate_Form
 *
 * A block to display the gift certificate form
 */
class Gift_Certificate_Form extends Shortcode_Block {
	const NAME = 'bigcommerce/gift-certificate-form';


	protected $icon = 'tickets-alt';
	protected $shortcode = Shortcodes\Gift_Certificate_Form::NAME;

	protected function title() {
		return __( 'BigCommerce Gift Certificates', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Gift Certificates', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Gift-Cert-Form.png' );
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'checkout', 'bigcommerce' );
		return $keywords;
	}
}