<?php

namespace BigCommerce\Compatibility\WooCommerce;

class Facade {

	/**
	 * @var BigCommerce\Compatibility\WooCommerce\Cart
	 */
	public $cart;

	public function __construct( Cart $cart ) {
		$this->cart = $cart;
	}
	
}