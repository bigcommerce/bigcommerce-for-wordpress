<?php


namespace BigCommerce\Templates;


class Mini_Cart_Actions extends Cart_Actions {
	protected function get_actions( $cart ) {
		$view     = Cart_Action_View::factory( [
			Cart_Action_View::CART => $cart,
		] );
		$checkout = Cart_Action_Checkout::factory( [
			Cart_Action_Checkout::CART => $cart,
		] );

		return [
			'view'     => $view->render(),
			'checkout' => $checkout->render(),
		];
	}

}
