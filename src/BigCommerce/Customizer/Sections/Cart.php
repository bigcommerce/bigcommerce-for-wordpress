<?php

namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;

/**
 * Handles the customization options for the Cart section in the WordPress Customizer.
 * Allows users to enable or disable the mini-cart, set the empty cart link destination,
 * and control the visibility of shipping info and coupon codes in the cart.
 *
 * @package BigCommerce\Customizer\Sections
 */
class Cart {
	/**
	 * The unique identifier for the Cart section.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce_cart';

	/**
	 * The setting key for enabling the mini-cart.
	 *
	 * @var string
	 */
	const ENABLE_MINI_CART     = 'bigcommerce_enable_mini_cart';

	/**
	 * The setting key for the empty cart link destination.
	 *
	 * @var string
	 */
	const EMPTY_CART_LINK      = 'bigcommerce_empty_cart_link_destination';

	/**
	 * The setting key for the text displayed in the empty cart link.
	 *
	 * @var string
	 */
	const EMPTY_CART_LINK_TEXT = 'bigcommerce_empty_cart_link_destination_text';

	/**
	 * The value for the homepage link destination.
	 *
	 * @var string
	 */
	const LINK_HOME            = 'home';

	/**
	 * The value for the product catalog link destination.
	 *
	 * @var string
	 */
	const LINK_CATALOG         = 'catalog';

	/**
	 * The setting key for enabling shipping info in the cart.
	 *
	 * @var string
	 */
	const ENABLE_SHIPPING_INFO = 'bigcommerce_enable_shipping_info';

	/**
	 * The setting key for enabling the coupon code in the cart.
	 *
	 * @var string
	 */
	const ENABLE_COUPON_CODE   = 'bigcommerce_enable_coupon_code';

	/**
	 * Registers settings and controls for Cart customization.
	 * 
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer manager instance.
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
		$this->shipping_info( $wp_customize );
		$this->coupon_code( $wp_customize );
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

	private function shipping_info( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_SHIPPING_INFO, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_SHIPPING_INFO, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'description' => __( 'Enable shipping calculation in Cart. ', 'bigcommerce' ),
			'label'       => __( 'Shipping Info', 'bigcommerce' ),
			'choices'     => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
	
	private function coupon_code( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_COUPON_CODE, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_COUPON_CODE, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'description' => __( 'Enable coupon code in Cart. ', 'bigcommerce' ),
			'label'       => __( 'Coupon Code', 'bigcommerce' ),
			'choices'     => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
}
