<?php


namespace BigCommerce\Templates;

class Wishlist_New_Button extends Controller {
	const LABEL      = 'label';
	const PRODUCTS   = 'products';
	const ATTRIBUTES = 'attributes';
	const FORM       = 'form_template';

	protected $wrapper_tag        = 'div';
	protected $wrapper_classes    = [ 'bc-wish-list-new' ];
	protected $wrapper_attributes = [ 'data-js' => 'bc-manage-wish-list' ];

	protected $template = 'components/wishlist/new-button.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::LABEL    => __( 'New Wish List', 'bigcommerce' ),
			self::PRODUCTS => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::LABEL      => $this->options[ self::LABEL ],
			self::ATTRIBUTES => $this->build_attribute_string( $this->required_attributes() ),
			self::FORM       => $this->render_form_template( $this->options[ self::PRODUCTS ] ),
		];
	}

	protected function required_attributes() {
		return [
			'type'         => 'button',
			'data-js'      => 'bc-wish-list-dialog-trigger',
			'data-trigger' => 'bc-create-wish-list-form--new',
			'data-content' => 'bc-create-wish-list-form--new',
		];
	}

	protected function render_form_template( array $product_ids ) {
		$component = Wishlist_Create::factory( [
			Wishlist_Create::PRODUCTS => $product_ids,
		] );

		return $component->render();
	}

}
