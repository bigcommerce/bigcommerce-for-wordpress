<?php


namespace BigCommerce\Merchant\Models;


/**
 * Class Account_Contact
 *
 * Contact details for an account
 */
class Account_Contact implements \JsonSerializable {
	public $email          = '';
	public $first_name     = '';
	public $last_name      = '';
	public $address_line_1 = '';
	public $address_line_2 = '';
	public $city           = '';
	public $district       = '';
	public $postal_code    = '';
	public $country        = '';
	public $phone_number   = '';

	public function __construct( $args = [] ) {
		foreach ( $args as $key => $value ) {
			if ( property_exists( __CLASS__, $key ) ) {
				$this->$key = $value;
			}
		}
	}

	public function jsonSerialize() {
		return [
			'email'          => $this->email,
			'first_name'     => $this->first_name,
			'last_name'      => $this->last_name,
			'address_line_1' => $this->address_line_1,
			'address_line_2' => $this->address_line_2,
			'city'           => $this->city,
			'district'       => $this->district,
			'postal_code'    => $this->postal_code,
			'country'        => $this->country,
			'phone_number'   => $this->phone_number,
		];
	}

}