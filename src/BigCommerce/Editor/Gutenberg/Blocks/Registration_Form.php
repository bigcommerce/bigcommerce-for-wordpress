<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * A block to display the registration form.
 */
class Registration_Form extends Shortcode_Block {
	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce/registration-form';

	/**
	 * The icon for the block.
	 *
	 * @var string
	 */
	protected $icon = 'nametag';

	/**
	 * The shortcode used for this block.
	 *
	 * @var string
	 */
	protected $shortcode = Shortcodes\Registration_Form::NAME;

	/**
	 * Returns the block's title.
	 *
	 * @return string The block's title.
	 */
	protected function title() {
		return __( 'BigCommerce Registration Form', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'Registration Form', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the image associated with the block.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Register-Form.png' );
	}

	/**
	 * Returns an array of keywords related to the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		return [
			__( 'users', 'bigcommerce' ),
			__( 'registration', 'bigcommerce' ),
			__( 'account', 'bigcommerce' ),
		];
	}
}