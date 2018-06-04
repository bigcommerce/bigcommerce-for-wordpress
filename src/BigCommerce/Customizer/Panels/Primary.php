<?php


namespace BigCommerce\Customizer\Panels;


class Primary {
	const NAME = 'bigcommerce';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$panel = new \WP_Customize_Panel( $wp_customize, self::NAME, [
			'title' => __( 'BigCommerce', 'bigcommerce' ),
			'description' => __( 'Edit the appearance of your BigCommerce store', 'bigcommerce' ),
		] );
		$wp_customize->add_panel( $panel );
	}
}