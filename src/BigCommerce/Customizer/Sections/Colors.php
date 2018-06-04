<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

class Colors {
	const NAME = 'bigcommerce_colors';

	const CSS = 'bigcommerce_use_css';

	const CSS_FULL = 'default';
	const CSS_OFF  = 'disabled';

	const COLOR_BC_BLUE       = '#5273f4';
	const COLOR_BC_GREEN      = '#65c68c';
	const COLOR_BC_LIGHT_GREY = '#e0e3e9';
	const COLOR_BC_BLACK      = '#34313f';
	const TEXT_DARK           = 'dark';
	const TEXT_LIGHT          = 'light';

	const BUTTON_COLOR       = 'bigcommerce_button_color';
	const BUTTON_TEXT        = 'bigcommerce_button_text_color';
	const SALE_COLOR         = 'bigcommerce_sale_color';
	const SALE_TEXT          = 'bigcommerce_sale_text_color';
	const AVAILABILITY_COLOR = 'bigcommerce_availability_color';
	const CONDITION_COLOR    = 'bigcommerce_condition_color';
	const CONDITION_TEXT     = 'bigcommerce_condition_text_color';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Colors & Theme', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->css( $wp_customize );
		$this->button( $wp_customize );
		$this->sale( $wp_customize );
		$this->availability( $wp_customize );
		$this->condition( $wp_customize );
	}

	private function css( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CSS, [
			'type'              => 'theme_mod',
			'default'           => self::CSS_FULL,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::CSS, [
			'section'     => self::NAME,
			'label'       => __( 'CSS', 'bigcommerce' ),
			'description' => __( 'Disable the plugin CSS to turn off all plugin styles and use your own', 'bigcommerce' ),
			'type'        => 'select',
			'choices'     => [
				self::CSS_FULL => __( 'Use plugin styles', 'bigcommerce' ),
				self::CSS_OFF  => __( 'Disable plugin styles', 'bigcommerce' ),
			],
		] ) );
	}

	private function button( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::BUTTON_COLOR, [
			'type'              => 'theme_mod',
			'default'           => self::COLOR_BC_BLUE,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_hex_color',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, self::BUTTON_COLOR, [
			'section' => self::NAME,
			'label'   => __( 'Button Color', 'bigcommerce' ),
		] ) );

		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::BUTTON_TEXT, [
			'type'              => 'theme_mod',
			'default'           => self::TEXT_LIGHT,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::BUTTON_TEXT, [
			'section' => self::NAME,
			'label'   => __( 'Button Text Color', 'bigcommerce' ),
			'type'    => 'select',
			'choices' => [
				self::TEXT_LIGHT => __( 'Light', 'bigcommerce' ),
				self::TEXT_DARK  => __( 'Dark', 'bigcommerce' ),
			],
		] ) );
	}

	private function sale( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::SALE_COLOR, [
			'type'              => 'theme_mod',
			'default'           => self::COLOR_BC_GREEN,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_hex_color',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, self::SALE_COLOR, [
			'section' => self::NAME,
			'label'   => __( 'Sale Price Color', 'bigcommerce' ),
		] ) );

		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::SALE_TEXT, [
			'type'              => 'theme_mod',
			'default'           => self::TEXT_LIGHT,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::SALE_TEXT, [
			'section' => self::NAME,
			'label'   => __( 'Sale Icon Text Color', 'bigcommerce' ),
			'type'    => 'select',
			'choices' => [
				self::TEXT_LIGHT => __( 'Light', 'bigcommerce' ),
				self::TEXT_DARK  => __( 'Dark', 'bigcommerce' ),
			],
		] ) );
	}

	private function availability( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::AVAILABILITY_COLOR, [
			'type'              => 'theme_mod',
			'default'           => self::COLOR_BC_BLACK,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_hex_color',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, self::AVAILABILITY_COLOR, [
			'section' => self::NAME,
			'label'   => __( 'Product Availability Color', 'bigcommerce' ),
		] ) );
	}

	private function condition( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CONDITION_COLOR, [
			'type'              => 'theme_mod',
			'default'           => self::COLOR_BC_LIGHT_GREY,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_hex_color',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, self::CONDITION_COLOR, [
			'section' => self::NAME,
			'label'   => __( 'Production Condition Color', 'bigcommerce' ),
		] ) );

		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CONDITION_TEXT, [
			'type'              => 'theme_mod',
			'default'           => self::TEXT_DARK,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::CONDITION_TEXT, [
			'section' => self::NAME,
			'label'   => __( 'Product Condition Color', 'bigcommerce' ),
			'type'    => 'select',
			'choices' => [
				self::TEXT_LIGHT => __( 'Light', 'bigcommerce' ),
				self::TEXT_DARK  => __( 'Dark', 'bigcommerce' ),
			],
		] ) );
	}

}