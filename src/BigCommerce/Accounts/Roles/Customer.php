<?php


namespace BigCommerce\Accounts\Roles;

/**
 * Class Customer
 *
 * A user role with no capabilities, not even `read`. This class represents a "Customer" role
 * in the BigCommerce accounts system. It implements the `Role` interface and provides
 * methods to retrieve the role's ID and label.
 *
 * @package BigCommerce\Accounts\Roles
 */
class Customer implements Role {

	/**
	 * The name of the role.
	 *
	 * This constant defines the identifier for the `Customer` role. It is used to retrieve
	 * and reference the role throughout the application.
	 *
	 * @var string
	 */
	const NAME = 'customer';

	/**
	 * Gets the ID of the customer role.
	 *
	 * This method returns the unique identifier for the `Customer` role, which is the
	 * value of the `NAME` constant (`'customer'`).
	 *
	 * @return string The ID of the customer role.
	 */
	public function get_id() {
		return self::NAME;
	}

	/**
	 * Gets the label of the customer role.
	 *
	 * This method returns the label for the `Customer` role, which is a translatable string.
	 * It is typically used to display the role's name in a UI.
	 *
	 * @return string The label of the customer role.
	 */
	public function get_label() {
		return __( 'Customer', 'bigcommerce' );
	}
}
