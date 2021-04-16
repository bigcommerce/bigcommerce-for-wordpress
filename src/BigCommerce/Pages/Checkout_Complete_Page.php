<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Checkout_Complete_Page extends Required_Page {
	const NAME = 'bigcommerce_checkout_complete_page_id';

	protected function get_title() {
		return _x( 'Checkout Complete', 'title of the checkout complete page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'checkout-complete', 'slug of the checkout complete page', 'bigcommerce' );
	}

	public function get_content() {
		$content = [
			__( 'Thanks for your order!', 'bigcommerce' ),
			__( 'We\'ve received your order and will begin processing it right away. For your records, an email confirmation has been sent. It contains details of the items you purchased and also a tracking number (if applicable).', 'bigcommerce' ),
		];
		return implode( "\n\n", $content );
	}

	public function get_post_state_label() {
		return __( 'Checkout Complete Page', 'bigcommerce' );
	}

    public function filter_content( $post_id, $content ) {
		return $content;
	}

}