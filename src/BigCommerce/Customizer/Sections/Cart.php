<?php

namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;

class Cart {
	const NAME = 'bigcommerce_cart';

	const ENABLE_MINI_CART = 'bigcommerce_enable_mini_cart';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Cart', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->mini_cart( $wp_customize );
	}

	private function mini_cart( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_MINI_CART, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_MINI_CART, [
			'section' => self::NAME,
			'type'    => 'radio',
			'description' => __( 'Show a mini-cart when a visitor clicks on the cart nav menu item.', 'bigcommerce' ),
			'label'   => __( 'Mini-Cart', 'bigcommerce' ),
			'choices' => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
}
