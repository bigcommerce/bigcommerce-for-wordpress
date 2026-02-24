<?php


namespace BigCommerce\Customizer\Sections;


use BigCommerce\Customizer\Panels;

/**
 * Represents the Customizer section for managing banner settings in the BigCommerce store.
 */
class Banners {
    /**
     * The identifier for the banners section.
     */
    const NAME = 'bigcommerce_banners';

    /**
     * The identifier for the "Enable Banners" setting.
     */
    const ENABLE_BANNERS = 'bigcommerce_enable_banners';

    /**
     * Registers the banners section in the WordPress Customizer.
     *
     * Adds a new section for managing banners under the primary BigCommerce panel.
     *
     * @param \WP_Customize_Manager $wp_customize The WordPress Customizer manager instance.
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
            'description' => __( 'Enable Banners.', 'bigcommerce' ),
            'label'       => __( 'Banners', 'bigcommerce' ),
            'choices'     => [
                'yes' => __( 'Enabled', 'bigcommerce' ),
                'no'  => __( 'Disabled', 'bigcommerce' ),
            ],
        ] );
    }

}