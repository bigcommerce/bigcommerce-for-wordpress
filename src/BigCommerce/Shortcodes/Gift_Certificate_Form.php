<?php

namespace BigCommerce\Shortcodes;

use BigCommerce\Api\Marketing_Api;
use BigCommerce\Templates;

class Gift_Certificate_Form implements Shortcode {
	const NAME = 'bigcommerce_gift_form';

	/**
	 * @var Marketing_Api
	 */
	private $api;

	public function __construct( Marketing_Api $api ) {
		$this->api = $api;
	}

	public function render( $attr, $instance ) {
		if ( ( (bool) get_option( \BigCommerce\Settings\Sections\Gift_Certificates::OPTION_ENABLE, true ) ) == false ) {
			return ''; // render nothing if gift certificates are disabled
		}

		$controller = Templates\Gift_Certificate_Page::factory();

		return $controller->render();
	}
}