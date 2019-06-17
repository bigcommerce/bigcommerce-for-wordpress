<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Cart;

class Product_Form extends Controller {
	const PRODUCT             = 'product';
	const OPTIONS             = 'options';
	const MODIFIERS           = 'modifiers';
	const BUTTON              = 'button';
	const MESSAGE             = 'message';
	const MIN_QUANTITY        = 'min_quantity';
	const MAX_QUANTITY        = 'max_quantity';
	const AJAX_ADD_TO_CART    = 'ajax_add_to_cart';
	const SHOW_OPTIONS        = 'show_options';
	const QUANTITY_FIELD_TYPE = 'quantity_field_type';

	protected $template = 'components/products/product-form.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT      => null,
			self::SHOW_OPTIONS => true,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT             => $product,
			self::BUTTON              => $product->purchase_button(),
			self::MESSAGE             => $product->purchase_message(),
			self::MIN_QUANTITY        => max( (int) $product->order_quantity_minimum, 1 ),
			self::MAX_QUANTITY        => $this->get_max_quantity( (int) $product->order_quantity_maximum, $product->get_inventory_level() ),
			self::OPTIONS             => $this->options[ self::SHOW_OPTIONS ] ? $this->get_options( $product ) : '',
			self::MODIFIERS           => '', // left for backwards compatibility - 1.7.0
			self::AJAX_ADD_TO_CART    => (bool) get_option( Cart::OPTION_AJAX_CART, true ),
			self::QUANTITY_FIELD_TYPE => $this->options[ self::SHOW_OPTIONS ] ? 'number' : 'hidden',
		];
	}

	public function render() {
		if ( ! $this->options[ self::PRODUCT ]->is_purchasable() ) {
			return '';
		}
		return parent::render();
	}

	/**
	 * @param Product $product
	 *
	 * @return string The rendered option and modifier fields for the product
	 */
	protected function get_options( Product $product ) {
		$component = Product_Options::factory( [
			Product_Options::PRODUCT => $product,
		] );

		return $component->render();
	}

	/**
	 * @return string
	 * @deprecated Modifiers are combined with options as of version 1.7.0
	 */
	protected function get_modifiers() {
		return '';
	}

	private function get_max_quantity( $order_max, $inventory ) {
		$order_max = (int) $order_max;
		$inventory = (int) $inventory;
		if ( $inventory < 0 ) { // no inventory restriction, so fall back to order restriction
			return $order_max;
		}
		if ( $inventory == 0 ) { // no inventory remaining
			return - 1;
		}
		if ( $order_max == 0 ) {
			return $inventory; // no order restriction, so use inventory limit
		}

		return min( $order_max, $inventory );
	}
}