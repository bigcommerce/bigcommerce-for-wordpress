<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Inventory_Level extends Controller {
	const PRODUCT = 'product';
	const STATUS  = 'status';
	const LABEL   = 'label';

	protected $template = 'components/products/inventory-level.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT => $product,
			self::STATUS  => $this->get_status( $product ),
			self::LABEL   => $this->get_label( $product ),
		];
	}


	public function render() {
		if ( ! $this->should_show_inventory( $this->options[ self::PRODUCT ] ) ) {
			return '';
		}

		return parent::render();
	}

	private function get_status( Product $product ) {
		if ( $product->out_of_stock() ) {
			return 'out_of_stock';
		}
		if ( $product->low_inventory() ) {
			return 'low_inventory';
		}

		return 'in_stock';
	}

	private function get_label( Product $product ) {
		if ( $product->out_of_stock() ) {
			return __( 'Out of Stock', 'bigcommerce' );
		}

		$inventory = $product->get_inventory_level();

		return sprintf( _n( '%d in Stock', '%d in Stock', $inventory, 'bigcommerce' ), $inventory );
	}

	private function should_show_inventory( Product $product ) {
		$show = get_option( \BigCommerce\Customizer\Sections\Product_Single::INVENTORY_DISPLAY, 'no' ) === 'yes';

		if ( $product->get_inventory_level() === - 1 ) {
			$show = false;
		} elseif ( $product->out_of_stock() ) {
			$show = true;
		} elseif ( $product->low_inventory() ) {
			$show = true;
		}

		/**
		 * Filter whether to display inventory for a product
		 *
		 * @param bool    $show    Whether to show the inventory level
		 * @param Product $product The product being displayed
		 */
		return apply_filters( 'bigcommerce/product/inventory/should_display', $show, $product );
	}

}
