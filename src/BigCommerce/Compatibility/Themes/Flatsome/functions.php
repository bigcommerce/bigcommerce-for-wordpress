<?php

/**
 * Set to true so Flatsome theme loads proper templates.
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		return true;
	}
}
