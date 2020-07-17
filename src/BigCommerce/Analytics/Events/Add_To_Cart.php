<?php


namespace BigCommerce\Analytics\Events;


use BigCommerce\Templates\Message;

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

			$args[ Message::ATTRIBUTES ] = array_merge( $args[ Message::ATTRIBUTES ], [
				'data-tracking-trigger' => 'ready',
				'data-tracking-event'   => 'add_to_cart',
				'data-tracking-data'    => wp_json_encode( [
					'cart_id'    => $data['cart_id'],
					'post_id'    => $data['post_id'],
					'product_id' => $data['product_id'],
					'variant_id' => $data['variant_id'],
					'name'       => get_the_title( $data[ 'post_id' ] ),
				] ),
			] );
		}

		return $args;
	}
}