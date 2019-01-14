<?php


namespace BigCommerce\Assets\Theme;


use BigCommerce\Rest\Cart_Controller;
use BigCommerce\Settings\Sections\Cart;

class JS_Config {
	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var string
	 */
	private $directory;

	/**
	 * @var Cart_Controller
	 */
	private $cart_controller;


	public function __construct( $asset_directory, Cart_Controller $cart_controller ) {
		$this->directory       = trailingslashit( $asset_directory );
		$this->cart_controller = $cart_controller;
	}

	public function get_data() {
		if ( ! isset( $this->data ) ) {
			$this->data = [
				'images_url' => $this->directory . 'img/admin/',
				'cart'       => [
					'api_url'         => $this->cart_controller->get_base_url(),
					'ajax_enabled'    => (bool) get_option( Cart::OPTION_AJAX_CART, true ),
					'ajax_cart_nonce' => wp_create_nonce( 'wp_rest' ),
				],
				'product'    => [
					'messages' => [
						'not_available' => __( 'The selected product combination is currently unavailable.', 'bigcommerce' ),
					],
				],
			];
			$this->data = apply_filters( 'bigcommerce/js_config', $this->data );
		}

		return $this->data;
	}
}