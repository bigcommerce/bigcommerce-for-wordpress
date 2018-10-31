<?php


namespace BigCommerce\Templates;

use Bigcommerce\Api\Resources\Shipment;
use BigCommerce\Post_Types\Product\Product;

class Order_Shipment extends Controller {
	const SHIPMENT = 'shipment';

	const METHOD          = 'method';
	const TRACKING_NUMBER = 'tracking_number';
	const PROVIDER        = 'provider';
	const CARRIER         = 'carrier';
	const ADDRESS         = 'address';
	const ITEMS           = 'items';

	protected $template = 'components/orders/order-shipment.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::SHIPMENT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Shipment $shipment */
		$shipment = $this->options[ self::SHIPMENT ];
		$data     = [
			self::METHOD          => $shipment->shipping_method,
			self::TRACKING_NUMBER => $shipment->tracking_number,
			self::PROVIDER        => $shipment->shipping_provider,
			self::CARRIER         => $shipment->tracking_carrier,
			self::ADDRESS         => $shipment->shipping_address,
			self::ITEMS           => $this->get_items( $shipment ),
		];

		return $data;
	}

	private function get_items( Shipment $shipment ) {
		return array_map( function ( $item ) {
			$post_id = $this->get_product_post( $item->product_id );

			return [
				'quantity'   => $item->quantity,
				'product_id' => $item->product_id,
				'post_id'    => $post_id,
				'title'      => get_the_title( $post_id ),
			];
		}, $shipment->items );
	}

	/**
	 * @param int $product_id
	 *
	 * @return int The ID of the WP post associated with the product ID
	 */
	private function get_product_post( $product_id ) {
		if ( empty( $product_id ) ) {
			return 0;
		}
		$posts = get_posts( [
			'bigcommerce_id__in' => [ $product_id ],
			'post_type'          => Product::NAME,
			'post_status'        => 'publish',
			'fields'             => 'ids',
		] );
		if ( empty( $posts ) ) {
			return 0;
		}

		return reset( $posts );
	}

}