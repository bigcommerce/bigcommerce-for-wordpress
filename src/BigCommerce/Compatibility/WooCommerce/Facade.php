<?php

namespace BigCommerce\Compatibility\WooCommerce;

/**
 * This class acts as a facade for the WooCommerce cart, providing a simplified interface for interacting with the BigCommerce cart.
 *
 * @package BigCommerce
 * @subpackage Compatibility\WooCommerce
 */
class Facade {

	/**
	 * The instance of the BigCommerce WooCommerce Cart class.
	 *
	 * @var Cart
	 */
	public $cart;

	/**
	 * Facade constructor.
	 *
	 * @param Cart $cart The Cart instance used to interact with the BigCommerce cart.
	 */
	public function __construct( Cart $cart ) {
		$this->cart = $cart;
	}

}