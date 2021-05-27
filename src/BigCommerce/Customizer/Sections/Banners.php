<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

class Banners {
	const NAME = 'bigcommerce_banners';

	const ENABLE_BANNERS = 'bigcommerce_enable_banners';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Banners', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->enable_banners( $wp_customize );
	}

	private function enable_banners( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_BANNERS, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_BANNERS, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'description' => __( 'Enable Banners. ', 'bigcommerce' ),
			'label'       => __( 'Banners', 'bigcommerce' ),
			'choices'     => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
	
}