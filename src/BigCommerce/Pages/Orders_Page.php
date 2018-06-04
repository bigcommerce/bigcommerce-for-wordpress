<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Orders_Page extends Required_Page {
	const NAME = 'bigcommerce_orders_page_id';

	protected function get_title() {
		return _x( 'Order History', 'title of the orders page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'order-history', 'slug of the orders page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Order_History::NAME );
	}

	public function get_post_state_label() {
		return __( 'Order History Page', 'bigcommerce' );
	}

}