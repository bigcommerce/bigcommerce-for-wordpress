<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

/**
 * Handles the customization options for the Checkout section in the WordPress Customizer.
 * Allows users to modify the colors of the embedded checkout page including background, text,
 * link, and error colors.
 *
 * @package BigCommerce\Customizer\Sections
 */
class Checkout {
	/**
	 * The unique identifier for the Checkout section.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce_checkout';

	/**
	 * The setting key for the background color in the checkout section.
	 *
	 * @var string
	 */
	const BACKGROUND_COLOR = 'bigcommerce_checkout_background_color';

	/**
	 * The setting key for the text color in the checkout section.
	 *
	 * @var string
	 */
	const TEXT_COLOR       = 'bigcommerce_checkout_text_color';

	/**
	 * The setting key for the link color in the checkout section.
	 *
	 * @var string
	 */
	const LINK_COLOR       = 'bigcommerce_checkout_link_color';

	/**
	 * The setting key for the error color in the checkout section.
	 *
	 * @var string
	 */
	const ERROR_COLOR      = 'bigcommerce_checkout_error_color';

	/**
	 * The default value for the black color.
	 *
	 * @var string
	 */
	const COLOR_BLACK   = '#000000';

	/**
	 * The default value for the white color.
	 *
	 * @var string
	 */
	const COLOR_WHITE   = '#FFFFFF';

	/**
	 * The default value for BigCommerce blue color.
	 *
	 * @var string
	 */
	const COLOR_BC_BLUE = '#5273f4';

	/**
	 * The default value for BigCommerce red color.
	 *
	 * @var string
	 */
	const COLOR_BC_RED  = '#ed1f00';

	/**
	 * Registers settings and controls for Checkout customization.
	 * 
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer manager instance.
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