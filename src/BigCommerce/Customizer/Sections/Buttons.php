<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

class Buttons {
	const NAME = 'bigcommerce_buttons';

	const ADD_TO_CART      = 'bigcommerce_add_to_cart_button_label';
	const PREORDER_TO_CART = 'bigcommerce_preorder_add_to_cart_button_label';
	const BUY_NOW          = 'bigcommerce_buy_now_button_label';
	const PREORDER_NOW     = 'bigcommerce_preorder_now_button_label';
	const CHOOSE_OPTIONS   = 'bigcommerce_choose_options_button_label';
	const VIEW_PRODUCT     = 'bigcommerce_view_product_button_label';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Buttons', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->add_to_cart( $wp_customize );
		$this->preorder_add_to_cart( $wp_customize );
		$this->buy_now( $wp_customize );
		$this->preorder_buy_now( $wp_customize );
		$this->choose_options( $wp_customize );
		$this->view_product( $wp_customize );
	}

	private function add_to_cart( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ADD_TO_CART, [
			'type'              => 'option',
			'default'           => __( 'Add to Cart', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::ADD_TO_CART, [
			'section' => self::NAME,
			'label'   => __( '"Add to Cart" Button Label', 'bigcommerce' ),
		] ) );
	}

	private function preorder_add_to_cart( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::PREORDER_TO_CART, [
			'type'              => 'option',
			'default'           => __( 'Add to Cart', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::PREORDER_TO_CART, [
			'section' => self::NAME,
			'label'   => __( '"Pre-Order Add to Cart" Button Label', 'bigcommerce' ),
		] ) );
	}

	private function buy_now( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::BUY_NOW, [
			'type'              => 'option',
			'default'           => __( 'Buy Now', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::BUY_NOW, [
			'section' => self::NAME,
			'label'   => __( '"Buy Now" Button Label', 'bigcommerce' ),
		] ) );
	}

	private function preorder_buy_now( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::PREORDER_NOW, [
			'type'              => 'option',
			'default'           => __( 'Pre-Order Now', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::PREORDER_NOW, [
			'section' => self::NAME,
			'label'   => __( '"Pre-Order Now" Button Label', 'bigcommerce' ),
		] ) );
	}

	private function choose_options( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CHOOSE_OPTIONS, [
			'type'              => 'option',
			'default'           => __( 'Choose Options', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::CHOOSE_OPTIONS, [
			'section' => self::NAME,
			'label'   => __( '"Choose Options" Button Label', 'bigcommerce' ),
		] ) );
	}

	private function view_product( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::VIEW_PRODUCT, [
			'type'              => 'option',
			'default'           => __( 'View Product', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::VIEW_PRODUCT, [
			'section' => self::NAME,
			'label'   => __( '"View Product" Button Label', 'bigcommerce' ),
		] ) );
	}
}