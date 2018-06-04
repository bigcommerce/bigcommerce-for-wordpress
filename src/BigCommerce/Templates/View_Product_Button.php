<?php


namespace BigCommerce\Templates;


use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Post_Types\Product\Product;

class View_Product_Button extends Controller {

	const PRODUCT   = 'product';
	const LABEL     = 'label';
	const PERMALINK = 'permalink';

	protected $template = 'components/view-product-button.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		if ( empty( $this->options[ self::PRODUCT ] ) ) {
			return [
				self::PRODUCT   => null,
				self::PERMALINK => '',
				self::LABEL     => '',
			];
		}
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];
		$data    = [
			self::PRODUCT   => $product,
			self::PERMALINK => get_the_permalink( $product->post_id() ),
			self::LABEL     => $this->options[ self::LABEL ] ?: $this->get_label(),
		];

		return $data;
	}

	protected function get_label() {
		$default   = __( 'View Product', 'bigcommerce' );
		$cta_label = get_option( Buttons::VIEW_PRODUCT, $default );

		return $cta_label;
	}
}