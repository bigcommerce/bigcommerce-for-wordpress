<?php


namespace BigCommerce\Customizer\Panels;

/**
 * Class Primary
 *
 * Represents the primary customizer panel for configuring the BigCommerce store.
 */
class Primary {
    /**
     * The name of the panel used for identification.
	 * @var string
     */
    const NAME = 'bigcommerce';

    /**
     * Register the primary panel in the WordPress Customizer.
     *
     * Adds a new panel to the WordPress Customizer for editing the appearance of the BigCommerce store.
     *
     * @param \WP_Customize_Manager $wp_customize The WordPress Customizer manager instance.
     *
     * @return void
     */
    public function register( $wp_customize ) {
        $panel = new \WP_Customize_Panel( $wp_customize, self::NAME, [
            'title'       => __( 'BigCommerce', 'bigcommerce' ),
            'description' => __( 'Edit the appearance of your BigCommerce store', 'bigcommerce' ),
        ] );
        $wp_customize->add_panel( $panel );
    }
}