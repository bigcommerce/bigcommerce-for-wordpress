<?php


namespace BigCommerce\Templates;


class Gift_Certificate_Balance_Page extends Controller {
	const CODE         = 'code';
	const BALANCE      = 'balance';
	const FORM         = 'form';
	const RESPONSE     = 'response';
	const INSTRUCTIONS = 'instructions';

	protected $template = 'components/gift-certificates/balance-shortcode.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CODE    => '',
			self::BALANCE => 0,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::FORM         => $this->get_form(),
			self::RESPONSE     => $this->get_response(),
			self::INSTRUCTIONS => $this->get_instructions(),
		];
	}

	private function get_form() {
		$controller = Gift_Certificate_Balance_Form::factory();

		return $controller->render();
	}

	private function get_response() {
		if ( empty( $this->options[ self::CODE ] ) ) {
			return '';
		}
		$controller = Gift_Certificate_Balance_Response::factory( [
			Gift_Certificate_Balance_Response::CODE    => $this->options[ self::CODE ],
			Gift_Certificate_Balance_Response::BALANCE => $this->options[ self::BALANCE ],
		] );

		return $controller->render();
	}

	private function get_instructions() {
		$controller = Gift_Certificate_Redemption_Instructions::factory();

		return $controller->render();
	}

}