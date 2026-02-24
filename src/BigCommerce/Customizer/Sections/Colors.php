<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

/**
 * Handles the customization constants for colors and themes in the BigCommerce plugin.
 */
class Colors {
    /**
     * Section name for the WordPress Customizer.
     * 
     * @var string
     */
    const NAME = 'bigcommerce_colors';

    /**
     * Setting for enabling or disabling plugin CSS.
     * 
     * @var string
     */
    const CSS = 'bigcommerce_use_css';

    /**
     * Use plugin styles for the theme.
     * 
     * @var string
     */
    const CSS_FULL = 'default';

    /**
     * Disable plugin styles and rely on custom CSS.
     * 
     * @var string
     */
    const CSS_OFF = 'disabled';

    /**
     * Default blue color for BigCommerce.
     * 
     * @var string
     */
    const COLOR_BC_BLUE = '#5273f4';

    /**
     * Default green color for success or positive indicators.
     * 
     * @var string
     */
    const COLOR_BC_GREEN = '#65c68c';

    /**
     * Light grey color for neutral elements.
     * 
     * @var string
     */
    const COLOR_BC_LIGHT_GREY = '#e0e3e9';

    /**
     * Black color for text or dark themes.
     * 
     * @var string
     */
    const COLOR_BC_BLACK = '#34313f';

    /**
     * Grey color for banners or muted elements.
     * 
     * @var string
     */
    const COLOR_BANNER_GREY = '#757575';

    /**
     * White color for backgrounds or light themes.
     * 
     * @var string
     */
    const COLOR_WHITE = '#ffffff';

    /**
     * Dark text option for contrast with light backgrounds.
     * 
     * @var string
     */
    const TEXT_DARK = 'dark';

    /**
     * Light text option for contrast with dark backgrounds.
     * 
     * @var string
     */
    const TEXT_LIGHT = 'light';

    /**
     * Setting for the button background color.
     * 
     * @var string
     */
    const BUTTON_COLOR = 'bigcommerce_button_color';

    /**
     * Setting for the button text color.
     * 
     * @var string
     */
    const BUTTON_TEXT = 'bigcommerce_button_text_color';

    /**
     * Setting for the sale price color.
     * 
     * @var string
     */
    const SALE_COLOR = 'bigcommerce_sale_color';

    /**
     * Setting for the sale icon text color.
     * 
     * @var string
     */
    const SALE_TEXT = 'bigcommerce_sale_text_color';

    /**
     * Setting for the product availability color.
     * 
     * @var string
     */
    const AVAILABILITY_COLOR = 'bigcommerce_availability_color';

    /**
     * Setting for the product condition color.
     * 
     * @var string
     */
    const CONDITION_COLOR = 'bigcommerce_condition_color';

    /**
     * Setting for the product condition text color.
     * 
     * @var string
     */
    const CONDITION_TEXT = 'bigcommerce_condition_text_color';

    /**
     * Setting for the banner background color.
     * 
     * @var string
     */
    const BANNER_COLOR = 'bigcommerce_banner_color';

    /**
     * Setting for the banner text color.
     * 
     * @var string
     */
    const BANNER_TEXT = 'bigcommerce_banner_text_color';

    /**
     * Registers the Colors section and related settings in the WordPress Customizer.
     *
     * @param \WP_Customize_Manager $wp_customize Instance of the WordPress Customizer.
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
		$this->banners( $wp_customize );
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
			'label'   => __( 'Product Condition Color', 'bigcommerce' ),
		] ) );

		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CONDITION_TEXT, [
			'type'              => 'theme_mod',
			'default'           => self::TEXT_DARK,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::CONDITION_TEXT, [
			'section' => self::NAME,
			'label'   => __( 'Product Condition Text Color', 'bigcommerce' ),
			'type'    => 'select',
			'choices' => [
				self::TEXT_LIGHT => __( 'Light', 'bigcommerce' ),
				self::TEXT_DARK  => __( 'Dark', 'bigcommerce' ),
			],
		] ) );
	}
	private function banners( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::BANNER_COLOR, [
			'type'              => 'theme_mod',
			'default'           => self::COLOR_BANNER_GREY,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_hex_color',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, self::BANNER_COLOR, [
			'section' => self::NAME,
			'label'   => __( 'Banner Background Color', 'bigcommerce' ),
		] ) );

		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::BANNER_TEXT, [
			'type'              => 'theme_mod',
			'default'           => self::COLOR_WHITE,
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, self::BANNER_TEXT, [
			'section' => self::NAME,
			'label'   => __( 'Banner Text Color', 'bigcommerce' ),
		] ) );
	}

}
