<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Form extends Controller {
	const PRODUCT      = 'product';
	const OPTIONS      = 'options';
	const MODIFIERS    = 'modifiers';
	const BUTTON       = 'button';
	const MIN_QUANTITY = 'min_quantity';
	const MAX_QUANTITY = 'max_quantity';

	const SHOW_OPTIONS = 'show_options';

	protected $template = 'components/product-form.php';

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
			self::PRODUCT      => $product,
			self::BUTTON       => $product->purchase_button(),
			self::MIN_QUANTITY => max( (int) $product->order_quantity_minimum, 1 ),
			self::MAX_QUANTITY => $this->options[ self::SHOW_OPTIONS ] ? $this->get_max_quantity( (int) $product->order_quantity_maximum, $product->get_inventory_level() ) : 1,
			self::OPTIONS      => $this->options[ self::SHOW_OPTIONS ] ? $this->get_options( $product ) : '',
			self::MODIFIERS    => $this->options[ self::SHOW_OPTIONS ] ? $this->get_modifiers( $product ) : '',
		];
	}

	protected function get_options( Product $product ) {
		$component = new Product_Options( [
			Product_Options::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_modifiers( Product $product ) {
		$component = new Product_Modifiers( [
			Product_Modifiers::PRODUCT => $product,
		] );

		return $component->render();
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