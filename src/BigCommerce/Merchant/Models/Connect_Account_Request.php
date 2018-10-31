<?php


namespace BigCommerce\Merchant\Models;


class Connect_Account_Request implements \JsonSerializable {
	/**
	 * @var string
	 */
	private $secret_key;
	/**
	 * @var string
	 */
	private $store_url;

	/**
	 * Connect_Account_Request constructor.
	 *
	 * @param string $secret_key
	 * @param string $store_url
	 */
	public function __construct( $secret_key, $store_url ) {
		$this->secret_key = $secret_key;
		$this->store_url  = $store_url;
	}

	public function jsonSerialize() {
		return [
			'secret_key' => $this->secret_key,
			'store_url'  => $this->store_url,
		];
	}


}