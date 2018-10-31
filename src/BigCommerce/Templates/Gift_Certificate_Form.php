<?php


namespace BigCommerce\Templates;

use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;


class Gift_Certificate_Form extends Form_Controller {
	const DEFAULTS     = 'defaults';
	const BUTTON_LABEL = 'button_label';
	const ERRORS       = 'errors';

	protected $template = 'components/gift-certificates/purchase-form.php';

	protected $submission_key = 'bc-gift-purchase';

	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = parent::get_data();

		$data[ self::BUTTON_LABEL ] = $this->get_button_label();

		return $data;
	}

	protected function get_form_defaults() {
		return [
			'sender-name'     => '',
			'sender-email'    => '',
			'recipient-name'  => '',
			'recipient-email' => '',
			'amount'          => '',
			'message'         => '',
		];
	}

	protected function get_button_label() {
		if ( get_option( Cart_Settings::OPTION_ENABLE_CART, true ) ) {
			return get_option( Buttons::ADD_TO_CART, __( 'Add to Cart', 'bigcommerce' ) );
		} else {
			return get_option( Buttons::BUY_NOW, __( 'Buy Now', 'bigcommerce' ) );
		}
	}
}