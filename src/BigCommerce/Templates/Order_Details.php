<?php


namespace BigCommerce\Templates;

use Bigcommerce\Api\Resources\Shipment;

class Order_Details extends Order_Summary {
	const ORDER     = 'order';
	const PRODUCTS  = 'products';
	const SHIPMENTS = 'shipments';

	protected $template = 'components/order-details.php';

	public function get_data() {
		$order = $this->options[ self::ORDER ];
		$data  = parent::get_data();

		$data[ self::PRODUCTS ]  = $this->get_products( $order );
		$data[ self::SHIPMENTS ] = $this->get_shipments( $order );

		return $data;
	}

	protected function get_products( $order ) {
		return array_map( function ( $product ) {
			$controller = new Order_Product( [
				Order_Product::PRODUCT        => $product,
				Order_Product::THUMBNAIL_SIZE => $this->options[ self::THUMBNAIL_SIZE ],
			] );

			return $controller->render();
		}, $order[ 'products' ] );
	}

	protected function get_shipments( $order ) {
		if ( empty( $order[ 'shipments' ] ) ) {
			return [];
		}

		return array_map( function ( Shipment $shipment ) {
			$controller = new Order_Shipment( [
				Order_Shipment::SHIPMENT => $shipment,
			] );

			return $controller->render();
		}, $order[ 'shipments' ] );
	}

}