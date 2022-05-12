<?php


namespace BigCommerce\Analytics\Events;


use BigCommerce\Settings\Sections\Analytics;
use BigCommerce\Templates\Message;
use BigCommerce\Post_Types\Product\Product;

/**
 * Class Add_To_Cart
 *
 * Add analytics tracking to purchase buttons and set success message tracking attributes
 */
class Add_To_Cart {


	/**
	 * @param array $args
	 * @param array $data
	 *
	 * @return array
	 */
	public function set_tracking_attributes_on_success_message( $args, $data ) {
		if ( ! array_key_exists( 'data', $data ) ) {
			return $args;
		}
		$data = $data['data'];
		if ( array_key_exists( 'key', $data ) && $data['key'] == 'add_to_cart' ) {
			$data = wp_parse_args( $data, [
				'cart_id'    => '',
				'post_id'    => 0,
				'product_id' => 0,
				'variant_id' => 0,
			] );

			$track_data = [
				'cart_id'    => $data['cart_id'],
				'post_id'    => $data['post_id'],
				'product_id' => $data['product_id'],
				'variant_id' => $data['variant_id'],
				'name'       => get_the_title( $data['post_id'] ),
			];
			$track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

			$args[ Message::ATTRIBUTES ] = array_merge( $args[ Message::ATTRIBUTES ], [
				'data-tracking-trigger' => 'ready',
				'data-tracking-event'   => 'add_to_cart_message',
				'data-tracking-data'    => wp_json_encode( $track_data ),
			] );
		}

		return $args;
	}

	/**
	 * @param array  $attributes
	 * @param Product $product
	 *
	 * @return array
	 * @filter bigcommerce/button/purchase/attributes
	 */
	public function add_tracking_attributes_to_purchase_button( $attributes, $product ) {
		$track_data = [
			'post_id'    => $product->post_id(),
			'product_id' => $product->bc_id(),
			'name'       => get_the_title( $product->post_id() ),
		];
		$track_data = apply_filters( Analytics::TRACK_BY_HOOK, $track_data );

		return array_merge( $attributes, [
			'data-tracking-trigger' => 'ready',
			'data-tracking-event'   => 'add_to_cart',
			'data-tracking-data'    => wp_json_encode( $track_data ),
		] );
	}
}
