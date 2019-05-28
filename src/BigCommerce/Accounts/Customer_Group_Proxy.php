<?php


namespace BigCommerce\Accounts;

use Bigcommerce\Api\Client;

/**
 * Class Customer_Group_Proxy
 *
 * Adds a caching proxy in front of requests for customer group info
 */
class Customer_Group_Proxy {

	/**
	 * @param array $info
	 * @param int   $id
	 *
	 * @return array
	 * @filter bigcommerce/customer/group_info
	 */
	public function filter_group_info( $info, $id ) {
		$data = $this->fetch_from_cache( $id ) ?: $this->fetch_from_api( $id );

		if ( empty( $data ) ) {
			return $info; // nothing to add
		}

		return array_merge( $info, $data );
	}

	private function fetch_from_cache( $group_id ) {
		$cached = get_transient( $this->cache_key( $group_id ) );
		if ( $cached ) {
			return json_decode( $cached, true );
		}

		return false;
	}

	private function fetch_from_api( $group_id ) {
		$response = Client::getResource( sprintf( '/customer_groups/%d', $group_id ) );
		if ( ! $response ) {
			return false;
		}
		$info = [
			'id'              => $group_id,
			'name'            => $response->name,
			'is_default'      => (bool) $response->is_default,
			'category_access' => $response->category_access,
			'discount_rules'  => $response->discount_rules,
		];

		$info = wp_json_encode( $info );
		$this->set_cache( $info, $group_id );

		return json_decode( $info, true ); // doing the encode/decode for consistency with the output from cache
	}

	private function set_cache( $info, $group_id ) {

		/**
		 * Filter the duration of the group info cache.
		 *
		 * @param int $expiration Time until expiration, in seconds
		 * @param int $group_id   The ID of the group being cached
		 */
		$expiration = apply_filters( 'bigcommerce/customer/group_info_cache_expiration', HOUR_IN_SECONDS, $group_id );

		set_transient( $this->cache_key( $group_id ), $info, $expiration );
	}

	private function cache_key( $group_id ) {
		return sprintf( 'bccustgroupinfo%d', $group_id );
	}
}