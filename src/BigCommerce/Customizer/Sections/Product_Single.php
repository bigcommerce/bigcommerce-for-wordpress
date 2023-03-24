<?php


namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Panels;
use BigCommerce\Settings\Sections\Import;

class Product_Single {
	const NAME = 'bigcommerce_product_single';

	const RELATED_COUNT       = 'bigcommerce_max_related_products';
	const DEFAULT_IMAGE       = 'bigcommerce_default_image_id';
	const PRICE_DISPLAY       = 'bigcommerce_default_price_display';
	const INVENTORY_DISPLAY   = 'bigcommerce_inventory_display';
	const VARIANTS_DISABLED   = 'bigcommerce_variants_disabled';
	const META_DESC_DISABLE   = 'bigcommerce_meta_description_disabled';
	const GALLERY_SIZE        = 'bigcommerce_gallery_image_size';
	const HEADLESS_IMAGE_SIZE = 'bigcommerce_gallery_headless_image_size';
	const ENABLE_ZOOM         = 'bigcommerce_enable_zoom';
	const ENABLE_PRICE_NONCE  = 'bigcommerce_enable_price_nonce';
	const SIZE_DEFAULT        = 'default';
	const SIZE_LARGE          = 'large';
	const SIZE_ORIGINAL       = 'original';
	const SIZE_CDN_STD        = 'standard';
	const SIZE_CDN_THUMB      = 'thumbnail';

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
		$this->default_image( $wp_customize );
		$this->gallery_size( $wp_customize );
		$this->headless_cdn_image_size( $wp_customize );
		$this->zoom( $wp_customize );
		$this->pricing( $wp_customize );
		$this->pricing_nonce( $wp_customize );
		$this->inventory( $wp_customize );
		$this->variants( $wp_customize );
		$this->meta_description( $wp_customize );
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

	private function default_image( \WP_Customize_Manager $wp_customize ) {
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

	private function gallery_size( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::GALLERY_SIZE, [
			'type'      => 'option',
			'transport' => 'refresh',
			'default'   => 'default',
		] ) );
		$wp_customize->add_control( self::GALLERY_SIZE, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'label'       => __( 'Image Gallery Size', 'bigcommerce' ),
			'choices'     => [
				self::SIZE_DEFAULT  => __( 'Default', 'bigcommerce' ),
				self::SIZE_LARGE    => __( 'Large', 'bigcommerce' ),
				self::SIZE_ORIGINAL => __( 'Original', 'bigcommerce' ),
			],
		] );
	}

	private function headless_cdn_image_size( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::HEADLESS_IMAGE_SIZE, [
			'type'      => 'option',
			'transport' => 'refresh',
			'default'   => self::SIZE_CDN_STD,
		] ) );
		$wp_customize->add_control( self::HEADLESS_IMAGE_SIZE, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'label'       => __( 'Image Size(headless)', 'bigcommerce' ),
			'choices'     => [
				self::SIZE_CDN_STD   => __( 'Standard', 'bigcommerce' ),
				self::SIZE_CDN_THUMB => __( 'Thumbnail', 'bigcommerce' ),
			],
			'description' => __( 'Toggle size of images while headless import is on', 'bigcommerce' ),
		] );
	}

	private function zoom( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_ZOOM, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_ZOOM, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'label'       => __( 'Image Zoom', 'bigcommerce' ),
			'choices'     => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
			'description' => __( 'Toggle the ability to zoom in on product gallery images', 'bigcommerce' ),
		] );
	}

	private function pricing( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::PRICE_DISPLAY, [
			'type'      => 'option',
			'default'   => 'yes',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::PRICE_DISPLAY, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'label'       => __( 'Price display', 'bigcommerce' ),
			'choices'     => [
				'yes' => __( 'Show default price', 'bigcommerce' ),
				'no'  => __( 'Hide default price', 'bigcommerce' ),
			],
			'description' => __( 'Control how default prices display while waiting for Pricing API responses', 'bigcommerce' ),
		] );
	}

	private function inventory( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::INVENTORY_DISPLAY, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::INVENTORY_DISPLAY, [
			'section' => self::NAME,
			'type'    => 'radio',
			'label'   => __( 'Inventory display', 'bigcommerce' ),
			'choices' => [
				'yes' => __( 'Always show inventory', 'bigcommerce' ),
				'no'  => __( 'Only show low inventory', 'bigcommerce' ),
			],
		] );
	}

	private function variants( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::VARIANTS_DISABLED, [
			'type'      => 'option',
			'default'   => 'yes',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::VARIANTS_DISABLED, [
			'section' => self::NAME,
			'type'    => 'radio',
			'label'   => __( 'Product variants out of stock behavior', 'bigcommerce' ),
			'choices' => [
					'yes' => __( 'Disable variants when they are not purchasable or out of stock', 'bigcommerce' ),
					'no'  => __( 'Do nothing', 'bigcommerce' ),
			],
		] );
	}

	protected function meta_description( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::META_DESC_DISABLE, [
			'type'      => 'option',
			'default'   => 'yes',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::META_DESC_DISABLE, [
			'section' => self::NAME,
			'type'    => 'radio',
			'label'   => __( 'Meta Description display', 'bigcommerce' ),
			'choices' => [
				'yes' => __( 'Show meta description set in BC admin on product page', 'bigcommerce' ),
				'no'  => __( 'Disable meta description from BC admin', 'bigcommerce' ),
			],
		] );
	}

	/**
	 * Register pricing nonce option setting
	 *
	 * @param \WP_Customize_Manager $wp_customize
	 */
	protected function pricing_nonce( \WP_Customize_Manager $wp_customize ): void {
		if ( ( int ) get_option( Import::HEADLESS_FLAG, 0 ) !== 1 ) {
			return;
		}

		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ENABLE_PRICE_NONCE, [
				'type'      => 'option',
				'default'   => 'yes',
				'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::ENABLE_PRICE_NONCE, [
			'section' => self::NAME,
			'type'    => 'radio',
			'label'   => __( 'Pricing nonce field(for advanced use only)', 'bigcommerce' ),
			'choices' => [
				'yes' => __( 'Enable pricing nonce', 'bigcommerce' ),
				'no'  => __( 'Disable pricing nonce', 'bigcommerce' ),
			],
			'description' => __( 'Control pricing request option by adding or removing pricing nonce. The feature can be used on heavy cached environments to prevent issues with expired nonce when "Fast - Headless" import is on', 'bigcommerce' ),
		] );
	}
}
