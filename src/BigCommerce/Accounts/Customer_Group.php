<?php


namespace BigCommerce\Accounts;

/**
 * Class Customer_Group
 *
 * Represents a customer group in BigCommerce and provides methods to retrieve group information.
 */
class Customer_Group {
	/** @var int The ID of the customer group. */
	private $group_id;

	/**
	 * Customer_Group constructor.
	 *
	 * Initializes the customer group with the specified group ID.
	 *
	 * @param int $group_id The ID of the customer group.
	 */
	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * Get information about the customer group.
	 *
	 * Retrieves information about the customer group, including:
	 * - id             (int)    The group ID
	 * - name           (string) The group name
	 * - is_default     (bool)   Whether the group is the default for new customers
	 * - discount_rules (array)  Discount rules applied to the group
	 *
	 * The returned data can be filtered using the `bigcommerce/customer/group_info` filter.
	 *
	 * @return array The customer group information.
	 * @filter bigcommerce/customer/group_info A filter applied to the customer group info before returning.
	 */
	public function get_info() {
		/**
		 * Filters customer group info.
		 *
		 * @param array $default_group The default group data.
		 * @param int   $group_id      The group ID.
		 */
		return apply_filters( 'bigcommerce/customer/group_info', $this->get_default_group(), $this->group_id );
	}

	private function get_default_group() {
		return [
			'id'              => $this->group_id,
			'name'            => '',
			'is_default'      => false,
			'category_access' => [],
			'discount_rules'  => [],
		];
	}
}
