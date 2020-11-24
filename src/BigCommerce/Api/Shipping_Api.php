<?php


namespace BigCommerce\Api;


class Shipping_Api extends v2ApiAdapter {
	public function get_zones() {
		return $this->getCollection( '/shipping/zones', 'ShippingZone' );
	}

	public function count_shipping_methods() {
		$zones = $this->get_zones();
		if ( ! is_array( $zones ) ) {
			return 0;
		}
		$methods = array_map( function ( $zone ) {
			return $this->getCollection( sprintf( '/shipping/zones/%d/methods', $zone->id ) ) ?: [];
		}, $zones );

		return array_sum( array_map( 'count', $methods ) );
	}

	public function get_shipping_methods( $zone_id ) {
		return $this->getCollection( sprintf( '/shipping/zones/%d/methods', $zone_id ) ) ?: [];
	}
}
