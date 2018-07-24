<?php


namespace BigCommerce\Templates;


class Gift_Certificate_Redemption_Instructions extends Controller {
	protected $template = 'components/gift-certificates/redemption-instructions.php';

	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [];
	}
}