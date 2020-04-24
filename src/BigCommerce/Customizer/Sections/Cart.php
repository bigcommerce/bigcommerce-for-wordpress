<?php

namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;

class Cart {
	const NAME = 'bigcommerce_cart';

	const ENABLE_MINI_CART      = 'bigcommerce_enable_mini_cart';
	const EMPTY_CART_LINK       = 'bigcommerce_empty_cart_link_destination';
	const EMPTY_CART_LINK_TEXT  = 'bigcommerce_empty_cart_link_destination_text';
	const LINK_HOME             = 'home';
	const LINK_CATALOG          = 'catalog';

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
		$this->empty_cart_link( $wp_customize );
	}

	private function mini_cart( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_MINI_CART, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_MINI_CART, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'description' => __( 'Show a mini-cart when a visitor clicks on the cart nav menu item.', 'bigcommerce' ),
			'label'       => __( 'Mini-Cart', 'bigcommerce' ),
			'choices'     => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}

	private function empty_cart_link( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::EMPTY_CART_LINK, [
			'type'      => 'option',
			'default'   => self::LINK_HOME,
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::EMPTY_CART_LINK, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'description' => __( 'Where the link within the empty cart message goes.', 'bigcommerce' ),
			'label'       => __( 'Empty Cart Link Destination', 'bigcommerce' ),
			'choices'     => [
				self::LINK_HOME    => __( 'Homepage', 'bigcommerce' ),
				self::LINK_CATALOG => __( 'Product Catalog', 'bigcommerce' ),
			],
		] );
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::EMPTY_CART_LINK_TEXT, [
			'type'              => 'option',
			'default'           => __( 'Take a look around', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::EMPTY_CART_LINK_TEXT, [
			'section' => self::NAME,
			'label'   => __( 'Empty Cart Link Text', 'bigcommerce' ),
		] ) );
	}
}
