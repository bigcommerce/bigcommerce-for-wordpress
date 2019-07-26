<?php


namespace BigCommerce\Templates;

Class Wishlist_Not_Available extends Controller {
	const MESSAGE = 'message';

	protected $template = 'components/wishlist/not-available.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::MESSAGE => __( 'Information about this Wish List is not available.', 'bigcommerce' ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::MESSAGE => wp_kses( $this->options[ self::MESSAGE ], 'data' ),
		];
	}
}
