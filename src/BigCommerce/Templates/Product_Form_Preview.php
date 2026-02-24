<?php


namespace BigCommerce\Templates;


/**
 * A specialized version of the product form that disables purchase functionality.
 * It hides product options and renders a disabled purchase button.
 *
 * @package BigCommerce\Templates
 */
class Product_Form_Preview extends Product_Form {

	/**
	 * The template path for rendering the product form preview.
	 *
	 * @var string
	 */
	protected $template = 'components/products/product-form-preview.php';

	/**
	 * Parses the options for the product form preview.
	 *
	 * Ensures that options are never displayed.
	 *
	 * @param array $options The original options array.
	 *
	 * @return array The modified options array with options hidden.
	 */
	protected function parse_options( array $options ) {
		$options[ self::SHOW_OPTIONS ] = false;

		return parent::parse_options( $options );
	}

	/**
	 * Retrieves data for rendering the product form preview.
	 *
	 * Temporarily adds a filter to overwrite the purchase button HTML.
	 *
	 * @return array The data array for rendering the product form preview.
	 */
	public function get_data() {
		add_filter( 'bigcommerce/button/purchase', [ $this, 'overwrite_purchase_button' ], 100, 3 );
		$data = parent::get_data();
		remove_filter( 'bigcommerce/button/purchase', [ $this, 'overwrite_purchase_button' ], 100 );

		return $data;
	}

	/**
	 * Overwrites the purchase button with a disabled button in the product form preview.
	 *
	 * @param string $html    The original button HTML.
	 * @param int    $post_id The product post ID.
	 * @param string $label   The label for the button.
	 *
	 * @return string The modified button HTML with a disabled state.
	 */
	public function overwrite_purchase_button( $html, $post_id, $label ) {
		return sprintf( '<button class="bc-btn" type="button" disabled="disabled">%s</button>', esc_html( $label ) );
	}
}