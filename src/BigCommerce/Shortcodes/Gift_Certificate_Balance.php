<?php

namespace BigCommerce\Shortcodes;

use BigCommerce\Api\Marketing_Api;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Templates;

class Gift_Certificate_Balance implements Shortcode {
	const NAME = 'bigcommerce_gift_balance';

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

		$args = [];
		if ( isset( $_REQUEST[ 'bc-gift-balance' ][ 'code' ] ) ) {
			$code = sanitize_text_field( $_REQUEST[ 'bc-gift-balance' ][ 'code' ] );
			$args = [
				Templates\Gift_Certificate_Balance_Page::CODE    => $code,
				Templates\Gift_Certificate_Balance_Page::BALANCE => $this->get_balance( $code ),
			];
		}
		$controller = Templates\Gift_Certificate_Balance_Page::factory( $args );

		return $controller->render();
	}

	private function get_balance( $code ) {
		try {
			$certificate = $this->api->get_gift_certificate_by_code( $code );
			switch ( $certificate->status ) {
				case 'expired':
					return 0;
				case 'active':
					return $certificate->balance;
				default:
					return null;
			}
		} catch ( ApiException $e ) {
			return null;
		}
	}
}