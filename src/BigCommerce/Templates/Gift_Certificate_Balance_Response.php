<?php


namespace BigCommerce\Templates;


class Gift_Certificate_Balance_Response extends Controller {
	const CODE    = 'code';
	const BALANCE = 'balance';
	const MESSAGE = 'message';

	protected $template = 'components/gift-certificates/balance-response.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::CODE    => '',
			self::BALANCE => 0,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::CODE    => $this->options[ self::CODE ],
			self::BALANCE => $this->get_balance(),
			self::MESSAGE => $this->get_message(),
		];
	}

	/**
	 * @return int
	 */
	private function get_balance() {
		$balance = is_numeric( $this->options[ self::BALANCE ] ) ? $this->options[ self::BALANCE ] : 0;

		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $balance ), $balance );
	}

	private function get_message() {
		if ( is_numeric( $this->options[ self::BALANCE ] ) ) {
			return '';
		}

		return __( 'The gift certificate entered is invalid. Please try again.', 'bigcommerce' );
	}

}