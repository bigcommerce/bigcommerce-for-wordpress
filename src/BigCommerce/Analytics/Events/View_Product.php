<?php


namespace BigCommerce\Analytics\Events;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Templates\Product_Title;
use BigCommerce\Templates\View_Product_Button;

class View_Product {
	/**
	 * @param array  $options
	 * @param string $template
	 *
	 * @return array
	 * @filter bigcommerce/template=components/products/view-product-button.php/options
	 */
	public function add_tracking_attributes_to_button( $options = [], $template = '' ) {
		if ( empty( $options[ View_Product_Button::PRODUCT ] ) ) {
			return $options;
		}
		/** @var Product $product */
		$product = $options[ View_Product_Button::PRODUCT ];

		if ( empty( $options[ View_Product_Button::ATTRIBUTES ] ) ) {
			$options[ View_Product_Button::ATTRIBUTES ] = [];
		}

		$options[ View_Product_Button::ATTRIBUTES ] = array_merge( $options[ View_Product_Button::ATTRIBUTES ], [
			'data-tracking-trigger' => 'click',
			'data-tracking-event'   => 'view_product',
			'data-tracking-data'    => wp_json_encode( [
				'post_id'    => $product->post_id(),
				'product_id' => $product->bc_id(),
				'name'       => get_the_title( $product->post_id() ),
			] ),
		] );

		return $options;
	}

	/**
	 * @param array  $options
	 * @param string $template
	 *
	 * @return array
	 * @filter bigcommerce/template=components/products/product-title.php/options
	 */
	public function add_tracking_attributes_to_permalink( $options, $template ) {
		if ( empty( $options[ Product_Title::USE_PERMALINK ] ) || empty( $options[ Product_Title::PRODUCT ] ) ) {
			return $options;
		}

		if ( empty( $options[ View_Product_Button::ATTRIBUTES ] ) ) {
			$options[ View_Product_Button::ATTRIBUTES ] = [];
		}

		$product = $options[ Product_Title::PRODUCT ];

		$options[ Product_Title::LINK_ATTRIBUTES ] = array_merge( $options[ Product_Title::LINK_ATTRIBUTES ], [
			'data-tracking-trigger' => 'click',
			'data-tracking-event'   => 'view_product',
			'data-tracking-data'    => wp_json_encode( [
				'post_id'    => $product->post_id(),
				'product_id' => $product->bc_id(),
				'name'       => get_the_title( $product->post_id() ),
			] ),
		] );

		return $options;
	}
}