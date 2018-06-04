<?php


namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;

class Product_Single {
	const NAME = 'bigcommerce_product_single';

	const RELATED_COUNT = 'bigcommerce_max_related_products';
	const DEFAULT_IMAGE = 'bigcommerce_default_image_id';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Product Single', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->related( $wp_customize );
		$this->image( $wp_customize );
	}

	private function related( \WP_Customize_Manager $wp_customize ) {
		$range = range( 0, 4 );
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::RELATED_COUNT, [
			'type'              => 'option',
			'default'           => 4,
			'transport'         => 'refresh',
			'validate_callback' => function ( \WP_Error $validity, $value ) use ( $range ) {
				$value = absint( $value );
				if ( ! in_array( $value, $range ) ) {
					$validity->add( 'invalid_value', sprintf( __( 'Related product selection must be between %d and %d', 'bigcommerce' ), min( $range ), max( $range ) ) );
				}

				return $validity;
			},
			'sanitize_callback' => 'absint',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::RELATED_COUNT, [
			'section' => self::NAME,
			'label'   => __( 'Display Related Products', 'bigcommerce' ),
			'type'    => 'select',
			'choices' => array_combine( $range, $range ),
		] ) );
	}

	private function image( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::DEFAULT_IMAGE, [
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'absint',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Media_Control( $wp_customize, self::DEFAULT_IMAGE, [
			'section'   => self::NAME,
			'label'     => __( 'Default Product Image', 'bigcommerce' ),
			'mime_type' => 'image',
		] ) );
	}
}