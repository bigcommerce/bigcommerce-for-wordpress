<?php


namespace BigCommerce\Analytics\Events;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Sections\Analytics;
use BigCommerce\Templates\Product_Title;
use BigCommerce\Templates\View_Product_Button;

/**
 * Class View_Product
 *
 * Add analytics events to the permalink and buttons
 */
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

		$track_data = [
				'post_id'    => $product->post_id(),
				'product_id' => $product->bc_id(),
				'name'       => get_the_title( $product->post_id() ),
		];
		$track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

		$options[ View_Product_Button::ATTRIBUTES ] = array_merge( $options[ View_Product_Button::ATTRIBUTES ], [
			'data-tracking-trigger' => 'click',
			'data-tracking-event'   => 'view_product',
			'data-tracking-data'    => wp_json_encode( $track_data ),
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

		$track_data = [
			'post_id'    => $product->post_id(),
			'product_id' => $product->bc_id(),
			'name'       => get_the_title( $product->post_id() ),
		];
		$track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

		$options[ Product_Title::LINK_ATTRIBUTES ] = array_merge( $options[ Product_Title::LINK_ATTRIBUTES ], [
			'data-tracking-trigger' => 'click',
			'data-tracking-event'   => 'view_product',
			'data-tracking-data'    => wp_json_encode( $track_data ),
		] );

		return $options;
	}

	/**
	 * Update tracking data for product if Analytics options are set to SKU tracking
	 *
	 * @param $track_data
	 *
	 * @return mixed
	 */
	public function change_track_data( $track_data ) {
		$should_track_sku = (bool) get_option( Analytics::TRACK_PRODUCT_SKU, 0 );

		if ( empty( $should_track_sku ) ) {
			return $track_data;
		}

		try {
			$product           = new Product( $track_data['post_id'] );
			$track_data['sku'] = $product->get_property( 'sku' );

			if ( ! empty( $track_data['variant_id'] ) ) {
				$track_data['variant_sku'] = $product->get_variant_sku( $track_data['variant_id'] );
			}

			return $track_data;
		} catch ( \Exception $exception ) {
			return $track_data;
		}
	}
}
