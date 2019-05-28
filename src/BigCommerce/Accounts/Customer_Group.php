<?php


namespace BigCommerce\Accounts;

class Customer_Group {
	/** @var int */
	private $group_id;

	/**
	 * @param int $group_id
	 */
	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * Get info about a customer group. Properties include:
	 *  - id             int    The group ID
	 *  - name           string The group name
	 *  - is_default     bool   Whether the group is the default for new customers
	 *  - discount_rules array  Discount rules applied to the group
	 *
	 * @return array
	 */
	public function get_info() {
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