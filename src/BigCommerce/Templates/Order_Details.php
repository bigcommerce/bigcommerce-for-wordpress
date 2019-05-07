<?php


namespace BigCommerce\Templates;

use Bigcommerce\Api\Resources\Shipment;

class Order_Details extends Order_Summary {
	const ORDER     = 'order';
	const PRODUCTS  = 'products';
	const SHIPMENTS = 'shipments';

	protected $template = 'components/orders/order-details.php';

	public function get_data() {
		$order = $this->options[ self::ORDER ];
		$data  = parent::get_data();

		$data[ self::PRODUCTS ]  = $this->get_products( $order );
		$data[ self::SHIPMENTS ] = $this->get_shipments( $order );

		return $data;
	}

	protected function get_products( $order ) {
		$channel = $this->identify_channel( $order );
		return array_map( function ( $product ) use ( $channel ) {
			$controller = Order_Product::factory( [
				Order_Product::PRODUCT        => $product,
				Order_Product::THUMBNAIL_SIZE => $this->options[ self::THUMBNAIL_SIZE ],
				Order_Product::CHANNEL        => $channel,
			] );

			return $controller->render();
		}, $order[ 'products' ] );
	}

	protected function get_shipments( $order ) {
		if ( empty( $order[ 'shipments' ] ) ) {
			return [];
		}

		return array_map( function ( Shipment $shipment ) {
			$controller = Order_Shipment::factory( [
				Order_Shipment::SHIPMENT => $shipment,
			] );

			return $controller->render();
		}, $order[ 'shipments' ] );
	}

}