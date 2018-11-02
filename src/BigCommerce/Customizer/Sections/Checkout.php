<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

class Checkout {
	const NAME = 'bigcommerce_checkout';

	const BACKGROUND_COLOR = 'bigcommerce_checkout_background_color';
	const TEXT_COLOR       = 'bigcommerce_checkout_text_color';
	const LINK_COLOR       = 'bigcommerce_checkout_link_color';
	const ERROR_COLOR      = 'bigcommerce_checkout_error_color';

	const COLOR_BLACK   = '#000000';
	const COLOR_WHITE   = '#FFFFFF';
	const COLOR_BC_BLUE = '#5273f4';
	const COLOR_BC_RED  = '#ed1f00';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Embedded Checkout', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$colors = [
			self::BACKGROUND_COLOR => [
				'label'   => __( 'Background Color', 'bigcommerce' ),
				'default' => self::COLOR_WHITE,
			],
			self::TEXT_COLOR       => [
				'label'   => __( 'Text Color', 'bigcommerce' ),
				'default' => self::COLOR_BLACK,
			],
			self::LINK_COLOR       => [
				'label'   => __( 'Link Color', 'bigcommerce' ),
				'default' => self::COLOR_BC_BLUE,
			],
			self::ERROR_COLOR      => [
				'label'   => __( 'Error Color', 'bigcommerce' ),
				'default' => self::COLOR_BC_RED,
			],
		];

		foreach ( $colors as $key => $value ) {
			$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, $key, [
				'type'              => 'theme_mod',
				'default'           => $value[ 'default' ],
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
			] ) );
			$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, $key, [
				'section' => self::NAME,
				'label'   => $value[ 'label' ],
			] ) );
		}
	}
}