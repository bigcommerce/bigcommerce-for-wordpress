<?php


namespace BigCommerce\Merchant\Models;


class Create_Account_Request implements \JsonSerializable {
	/**
	 * @var string
	 */
	private $secret_key = '';
	/**
	 * @var string
	 */
	private $store_name = '';
	/**
	 * @var string
	 */
	private $store_url = '';
	/**
	 * @var Account_Contact
	 */
	private $contact;

	public function __construct( $secret_key, $store_name, $store_url, Account_Contact $contact ) {
		$this->secret_key = $secret_key;
		$this->store_name = $store_name;
		$this->store_url  = $store_url;
		$this->contact    = $contact;
	}

	public function jsonSerialize() {
		return [
			'secret_key' => $this->secret_key,
			'store_name' => $this->store_name,
			'store_url'  => $this->store_url,
			'contact'    => $this->contact,
		];
	}


}