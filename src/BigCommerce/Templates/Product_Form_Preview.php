<?php


namespace BigCommerce\Templates;


/**
 * Class Product_Form_Preview
 *
 * Just like the product form, but never shows options and the button is disabled
 */
class Product_Form_Preview extends Product_Form {

	protected $template = 'components/products/product-form-preview.php';

	protected function parse_options( array $options ) {
		$options[ self::SHOW_OPTIONS ] = false;

		return parent::parse_options( $options );
	}

	public function get_data() {
		add_filter( 'bigcommerce/button/purchase', [ $this, 'overwrite_purchase_button' ], 100, 3 );
		$data = parent::get_data();
		remove_filter( 'bigcommerce/button/purchase', [ $this, 'overwrite_purchase_button' ], 100 );

		return $data;
	}

	/**
	 * @param string $html    The original button HTML
	 * @param int    $post_id The product post ID
	 * @param string $label   The label for the button
	 *
	 * @return string
	 */
	public function overwrite_purchase_button( $html, $post_id, $label ) {
		return sprintf( '<button class="bc-btn" type="button" disabled="disabled">%s</button>', esc_html( $label ) );
	}
}