<?php


namespace BigCommerce\Templates;

Class Product_Not_Available extends Controller {
	const MESSAGE = 'message';

	protected $template = 'components/products/not-available.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::MESSAGE => __( 'Information for this product is not available.', 'bigcommerce' ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::MESSAGE => wp_kses( $this->options[ self::MESSAGE ], 'data' ),
		];
	}
}
