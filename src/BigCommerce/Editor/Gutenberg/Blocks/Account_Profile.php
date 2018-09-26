<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Shortcodes;

/**
 * Class Account_Profile
 *
 * A block to display the current user's account profile
 */
class Account_Profile extends Gutenberg_Block {
	const NAME = 'bigcommerce/account-profile';


	public function __construct() {
		parent::__construct();
	}

	public function render( $attributes ) {
		return sprintf( '[%s]', Shortcodes\Account_Profile::NAME ); // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'       => $this->name(),
			'title'      => __( 'BigCommerce Account Profile', 'bigcommerce' ),
			'category'   => 'widgets',
			'keywords'   => [
				__( 'user', 'bigcommerce' ),
				__( 'account', 'bigcommerce' ),
			],
			'shortcode'  => sprintf( '[%s]', Shortcodes\Account_Profile::NAME ),
			'block_html' => [
				'title' => __( 'My Account', 'bigcommerce' ),
			],
		];
	}
}