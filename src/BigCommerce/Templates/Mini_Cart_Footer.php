<?php


namespace BigCommerce\Templates;


class Mini_Cart_Footer extends Cart_Footer {

	protected $template = 'components/cart/mini-cart-footer.php';

	protected function get_actions( $cart ) {
		$component = Mini_Cart_Actions::factory( [
			Mini_Cart_Actions::CART => $cart,
		] );

		return $component->render();
	}

	protected function get_summary( $cart ) {
		$component = Mini_Cart_Summary::factory( [
			Mini_Cart_Summary::CART => $cart,
		] );

		return $component->render();
	}

}
