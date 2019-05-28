<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Title extends Controller {

	const PRODUCT   = 'product';
	const TITLE     = 'title';
	const CONDITION = 'condition';
	const INVENTORY = 'inventory';
	const PERMALINK = 'permalink';

	const SHOW_CONDITION  = 'show_condition';
	const SHOW_INVENTORY  = 'show_inventory';
	const USE_PERMALINK   = 'use_permalink';
	const LINK_ATTRIBUTES = 'link_attributes';
	const HEADER_LEVEL    = 'header_level';
	const HEADER_TAG      = 'header_tag';

	protected $template = 'components/products/product-title.php';


	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT         => null,
			self::SHOW_CONDITION  => true,
			self::SHOW_INVENTORY  => true,
			self::USE_PERMALINK   => true,
			self::LINK_ATTRIBUTES => [],
			self::HEADER_LEVEL    => 3,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		if ( empty( $this->options[ self::PRODUCT ] ) ) {
			return [
				self::PRODUCT         => null,
				self::TITLE           => '',
				self::CONDITION       => '',
				self::INVENTORY       => '',
				self::PERMALINK       => '',
				self::USE_PERMALINK   => '',
				self::LINK_ATTRIBUTES => '',
				self::HEADER_TAG      => $this->header_tag( $this->options[ self::HEADER_LEVEL ] ),
			];
		}
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];
		$data    = [
			self::PRODUCT         => $product,
			self::TITLE           => get_the_title( $product->post_id() ),
			self::CONDITION       => $this->get_condition( $product ),
			self::INVENTORY       => $this->get_inventory( $product ),
			self::PERMALINK       => get_the_permalink( $product->post_id() ),
			self::USE_PERMALINK   => $this->options[ self::USE_PERMALINK ],
			self::LINK_ATTRIBUTES => $this->build_attribute_string( $this->options[ self::LINK_ATTRIBUTES ] ),
			self::HEADER_TAG      => $this->header_tag( $this->options[ self::HEADER_LEVEL ] ),
		];

		return $data;
	}

	private function get_condition( Product $product ) {
		if ( empty( $this->options[ self::SHOW_CONDITION ] ) || ! $product->show_condition() ) {
			return '';
		}
		$controller = Product_Condition::factory( [
			Product_Condition::PRODUCT => $product,
		] );

		return $controller->render();
	}

	private function get_inventory( Product $product ) {
		if ( empty( $this->options[ self::SHOW_INVENTORY ] ) ) {
			return '';
		}
		$controller = Inventory_Level::factory( [
			Inventory_Level::PRODUCT => $product,
		] );

		return $controller->render();
	}

	private function header_tag( $level ) {
		$level = max( 1, absint( $level ) );

		return sprintf( 'h%d', $level );
	}
}