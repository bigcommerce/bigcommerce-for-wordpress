<?php


namespace BigCommerce\Templates;


class Cart_Coupon_Code extends Controller {

	const COUPONS = 'coupons';

	protected $template = 'components/cart/coupon-code.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-coupon-code' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-coupon-code' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::COUPONS => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::COUPONS => $this->options[ self::COUPONS ],
		];
	}

	/**
	 * Add a class with the image size we're using
	 *
	 * @return string[]
	 */
	protected function get_wrapper_classes() {
		$coupons = $this->options[ self::COUPONS ];
		$class   = empty( $coupons ) ? 'bc-hide-remove-form' : 'bc-hide-add-form';

		return array_merge( parent::get_wrapper_classes(), [
			sanitize_html_class( $class ),
		] );
	}

}
