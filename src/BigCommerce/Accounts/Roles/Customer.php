<?php


namespace BigCommerce\Accounts\Roles;

/**
 * Class Customer
 *
 * A user role with no capabilities, not even `read`
 */
class Customer implements Role {
	const NAME = 'customer';

	public function get_id() {
		return self::NAME;
	}

	public function get_label() {
		return __( 'Customer', 'bigcommerce' );
	}
}