<?php


namespace BigCommerce\Api;

/**
 * Handles API v2 requests for shipping data, including zones and shipping methods.
 * Provides methods to fetch available shipping zones, count shipping methods, 
 * and retrieve shipping methods by zone.
 *
 * @package BigCommerce\Api
 */
class Shipping_Api extends v2ApiAdapter {

	/**
	 * Get shipping zones.
	 *
	 * This method retrieves the list of shipping zones configured in the BigCommerce store.
	 *
	 * @return array An array of shipping zones.
	 */
	public function get_zones() {
		return $this->getCollection( '/shipping/zones', 'ShippingZone' );
	}

	/**
	 * Get the count of available shipping methods.
	 *
	 * This method retrieves all shipping zones and counts the available shipping methods 
	 * for each zone. If any errors occur during the retrieval process, it returns 0.
	 *
	 * @return int|float The total count of shipping methods available across all zones.
	 */
	public function count_shipping_methods() {
		try {
			$zones = $this->get_zones();
		} catch ( \Exception $exception ) {
			return 0;
		}

		if ( ! is_array( $zones ) ) {
			return 0;
		}
		$methods = array_map( function ( $zone ) {
			return $this->getCollection( sprintf( '/shipping/zones/%d/methods', $zone->id ) ) ?: [];
		}, $zones );

		return array_sum( array_map( 'count', $methods ) );
	}

	/**
	 * Retrieve the list of available shipping methods by zone ID.
	 *
	 * This method retrieves the list of shipping methods for a specific shipping zone.
	 *
	 * @param int $zone_id The ID of the shipping zone for which to retrieve methods.
	 *
	 * @return array An array of shipping methods for the specified zone.
	 */
	public function get_shipping_methods( $zone_id ) {
		return $this->getCollection( sprintf( '/shipping/zones/%d/methods', $zone_id ) ) ?: [];
	}
}
