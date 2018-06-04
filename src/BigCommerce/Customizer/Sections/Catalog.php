<?php


namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;

class Catalog {
	const NAME = 'bigcommerce_catalog';

	const GRID_COLUMNS = 'bigcommerce_catalog_grid_columns';
	const PER_PAGE     = 'bigcommerce_products_per_page';

	const PER_PAGE_DEFAULT = 24;

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Catalog Pages', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->columns( $wp_customize );
		$this->per_page( $wp_customize );
	}

	private function columns( \WP_Customize_Manager $wp_customize ) {
		$range = range( 2, 5 );
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::GRID_COLUMNS, [
			'type'              => 'option',
			'default'           => 4,
			'transport'         => 'refresh',
			'validate_callback' => function ( \WP_Error $validity, $value ) use ( $range ) {
				$value = absint( $value );
				if ( ! in_array( $value, $range ) ) {
					$validity->add( 'invalid_value', sprintf( __( 'Column selection must be between %d and %d', 'bigcommerce' ), min( $range ), max( $range ) ) );
				}

				return $validity;
			},
			'sanitize_callback' => 'absint',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::GRID_COLUMNS, [
			'section' => self::NAME,
			'label'   => __( 'Grid Columns', 'bigcommerce' ),
			'type'    => 'select',
			'choices' => array_combine( $range, $range ),
		] ) );
	}

	private function per_page( \WP_Customize_Manager $wp_customize ) {
		$range = range( 1, 100 );
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::PER_PAGE, [
			'type'              => 'option',
			'default'           => self::PER_PAGE_DEFAULT,
			'transport'         => 'refresh',
			'validate_callback' => function ( \WP_Error $validity, $value ) use ( $range ) {
				$value = absint( $value );
				if ( ! in_array( $value, $range ) ) {
					$validity->add( 'invalid_value', sprintf( __( 'Choose between %d and %d products per page', 'bigcommerce' ), min( $range ), max( $range ) ) );
				}

				return $validity;
			},
			'sanitize_callback' => 'absint',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::PER_PAGE, [
			'section'     => self::NAME,
			'label'       => __( 'Products per Page', 'bigcommerce' ),
			'type'        => 'number',
			'input_attrs' => [
				'min' => min( $range ),
				'max' => max( $range ),
			],
		] ) );
	}
}