<?php


namespace BigCommerce\Api;


class Shipping_Api extends v2ApiAdapter {
	public function get_zones() {
		return $this->getCollection( '/shipping/zones', 'ShippingZone' );
	}
}