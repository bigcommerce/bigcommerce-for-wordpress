<?php

namespace BigCommerce\Accounts\Roles;

/**
 * Interface Role
 *
 * An interface for defining user roles in the BigCommerce accounts system. Any role class
 * that implements this interface must provide methods to retrieve the role's unique identifier
 * and its label.
 *
 * @package BigCommerce\Accounts\Roles
 */
interface Role {

	/**
	 * Gets the ID of the role.
	 *
	 * This method must be implemented by any class that represents a user role. It should
	 * return a unique identifier for the role, typically a string constant or similar value.
	 *
	 * @return string The ID of the role.
	 */
	public function get_id();

	/**
	 * Gets the label of the role.
	 *
	 * This method must be implemented by any class that represents a user role. It should
	 * return a translatable label or name for the role, typically used for display purposes
	 * in the user interface.
	 *
	 * @return string The label of the role.
	 */
	public function get_label();
}
