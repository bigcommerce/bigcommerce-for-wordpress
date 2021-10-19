<?php


namespace BigCommerce\Api;

/**
 * Class Shipping_Api
 *
 * Handle api v2 request for shipping data: zones, methods
 *
 * @package BigCommerce\Api
 */
class Shipping_Api extends v2ApiAdapter {

    /**
     * Get shipping zones
     *
     * @return array
     */
	public function get_zones() {
		return $this->getCollection( '/shipping/zones', 'ShippingZone' );
	}

    /**
     * Get count of available shipping methods
     *
     * @return float|int
     */
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

    /**
     * Retrieve the list of available shipping methods by zone_id
     *
     * @param $zone_id
     *
     * @return array
     */
	public function get_shipping_methods( $zone_id ) {
		return $this->getCollection( sprintf( '/shipping/zones/%d/methods', $zone_id ) ) ?: [];
	}
}
