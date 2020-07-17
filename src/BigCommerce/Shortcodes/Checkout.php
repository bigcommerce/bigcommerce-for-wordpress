<?php


namespace BigCommerce\Shortcodes;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Cart\Add_To_Cart;
use BigCommerce\Cart\Cart_Mapper;
use BigCommerce\Customizer\Sections\Checkout as Checkout_Colors;
use BigCommerce\Customizer\Sections\Colors;
use BigCommerce\Templates;

class Checkout implements Shortcode {

	const NAME = 'bigcommerce_checkout';

	/**
	 * @var CartApi
	 */
	private $cart_api;

	public function __construct( CartApi $cart_api ) {
		$this->cart_api = $cart_api;
	}

	public function render( $attr, $instance ) {
		if ( ( (bool) get_option( \BigCommerce\Settings\Sections\Cart::OPTION_EMBEDDED_CHECKOUT, true ) ) == false ) {
			return ''; // render nothing if embedded checkout is disabled
		}

		$attr = shortcode_atts( [
		], $attr, self::NAME );

		$cart = new \BigCommerce\Cart\Cart( $this->cart_api );

		$checkout_config = [
			'containerId' => 'bc-embedded-checkout',
			'url'         => $cart->get_embedded_checkout_url( '' ),
			'styles'      => [
				'body'           => [
					'backgroundColor' => sanitize_hex_color( get_theme_mod( Checkout_Colors::BACKGROUND_COLOR, Checkout_Colors::COLOR_WHITE ) ),
				],
				'text'           => [
					'color' => sanitize_hex_color( get_theme_mod( Checkout_Colors::TEXT_COLOR, Checkout_Colors::COLOR_BLACK ) ),
				],
				'link'           => [
					'color' => sanitize_hex_color( get_theme_mod( Checkout_Colors::LINK_COLOR, Checkout_Colors::COLOR_BC_BLUE ) ),
				],
				'label'          => [
					'error' => [
						'color' => sanitize_hex_color( get_theme_mod( Checkout_Colors::ERROR_COLOR, Checkout_Colors::COLOR_BC_RED ) ),
					],
				],
				'button'         => [
					'backgroundColor' => sanitize_hex_color( get_theme_mod( Colors::BUTTON_COLOR, Colors::COLOR_BC_BLUE ) ),
					'borderColor'     => sanitize_hex_color( get_theme_mod( Colors::BUTTON_COLOR, Colors::COLOR_BC_BLUE ) ),
				],
				'discountBanner' => [
					'backgroundColor' => sanitize_hex_color( get_theme_mod( Colors::SALE_COLOR, Colors::COLOR_BC_GREEN ) ),
				],
			],
		];

		/**
		 * Filter the config used to render the embedded checkout.
		 * For more details, @see https://github.com/bigcommerce/checkout-sdk-js/blob/master/docs/interfaces/embeddedcheckoutoptions.md
		 *
		 * @param array $checkout_config
		 */
		$checkout_config = apply_filters( 'bigcommerce/checkout/config', $checkout_config );

		return sprintf( '<div id="bc-embedded-checkout" data-js="bc-embedded-checkout" data-config="%s"></div>', esc_attr( wp_json_encode( $checkout_config ) ) );
	}

}