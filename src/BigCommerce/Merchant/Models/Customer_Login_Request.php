<?php


namespace BigCommerce\Merchant\Models;


class Customer_Login_Request implements \JsonSerializable {
	/**
	 * @var string
	 */
	private $store_hash;
	/**
	 * @var string
	 */
	private $redirect_to;

	/**
	 * Connect_Account_Request constructor.
	 *
	 * @param string $store_hash
	 * @param string $redirect_to
	 */
	public function __construct( $store_hash, $redirect_to = '' ) {
		$this->store_hash  = $store_hash;
		$this->redirect_to = $redirect_to;
	}

	public function jsonSerialize() {
		return array_filter( [
			'store_hash'  => $this->store_hash,
			'redirect_to' => $this->redirect_to,
		] );
	}


}