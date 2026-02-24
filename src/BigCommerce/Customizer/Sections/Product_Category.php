<?php

namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;

/**
 * Handles the customization of product category settings and controls.
 *
 * @package BigCommerce\Customizer\Sections
 */
class Product_Category {

    /** 
     * The section name for product categories in the customizer.
     * 
     * @var string 
     */
    const NAME = 'bigcommerce_product_categories';

    /** 
     * The option key for showing child items in the navigation menu.
     * 
     * @var string 
     */
    const CHILD_ITEM_SHOW = 'bigcommerce_show_child_items';

    /** 
     * The option key for the "categories is visible" flag.
     * 
     * @var string 
     */
    const CATEGORIES_IS_VISIBLE = 'bigcommerce_categories_is_visible';

    /**
     * Register Product Category customize section and related controls.
     *
     * @param \WP_Customize_Manager $wp_customize The WordPress Customizer instance.
     *
     * @return void
     */
    public function register( $wp_customize ) {
        $wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
            'title' => __( 'Product Category', 'bigcommerce' ),
            'panel' => Panels\Primary::NAME,
        ] ) );

        $this->add_sub_categories_control( $wp_customize );
        $this->add_is_visible_control( $wp_customize );
    }

    /**
     * Register radio box control for categories "is visible" flag.
     *
     * @param \WP_Customize_Manager $wp_customize The WordPress Customizer instance.
     *
     * @return void
     */
    protected function add_is_visible_control( \WP_Customize_Manager $wp_customize ) {
        $wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CATEGORIES_IS_VISIBLE, [
            'type'      => 'option',
            'default'   => 'no',
            'transport' => 'refresh',
        ] ) );

        $wp_customize->add_control( self::CATEGORIES_IS_VISIBLE, [
            'section'     => self::NAME,
            'type'        => 'radio',
            'label'       => __( 'Categories `is_visible` option', 'bigcommerce' ),
            'choices'     => [
                'yes' => __( 'Enable categories is_visible flag support', 'bigcommerce' ),
                'no'  => __( 'Disable categories is_visible flag support', 'bigcommerce' ),
            ],
            'description' => __( 'If enabled, only categories marked as visible in the BigCommerce Dashboard will be displayed on the store.', 'bigcommerce' ),
        ] );
    }

    /**
     * Register controls for showing child items in the navigation menu.
     *
     * @param \WP_Customize_Manager $wp_customize The WordPress Customizer instance.
     *
     * @return void
     */
    protected function add_sub_categories_control( \WP_Customize_Manager $wp_customize ) {
        $wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CHILD_ITEM_SHOW, [
            'type'      => 'option',
            'default'   => 'no',
            'transport' => 'refresh',
        ] ) );

        $wp_customize->add_control( self::CHILD_ITEM_SHOW, [
            'section'     => self::NAME,
            'type'        => 'radio',
            'label'       => __( 'Menus Sub-Categories', 'bigcommerce' ),
            'choices'     => [
                'yes' => __( 'Show Product Sub-Categories in Menu', 'bigcommerce' ),
                'no'  => __( 'Hide Product Sub-Categories in Menu', 'bigcommerce' ),
            ],
            'description' => __( 'Toggle the ability to display sub-categories in the menu.', 'bigcommerce' ),
        ] );
    }
}
