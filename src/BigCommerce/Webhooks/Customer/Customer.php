<?php

namespace BigCommerce\Webhooks\Customer;

use \BigCommerce\Accounts\Customer as Account;

trait Customer {

	/**
	 * Get customer by BC id
	 *
	 * @param $id
	 *
	 * @return false|mixed|null
	 */
	public function get_by_bc_id( $id ) {
		if ( empty( $id ) ) {
			throw new \InvalidArgumentException( __( 'Customer ID must be a positive integer', 'bigcommerce' ) );
		}

		global $wpdb;

		$users = get_users([
				'meta_key'   => $wpdb->get_blog_prefix() . Account::CUSTOMER_ID_META,
				'meta_value' => absint( $id ),
		]);

		if ( empty( $users ) ) {
			return null;
		}

		return reset( $users );
	}

}
