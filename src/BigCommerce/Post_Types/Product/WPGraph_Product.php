<?php

namespace BigCommerce\Post_Types\Product;

/**
 * @class WPGraph_Product
 *
 * Add support GraphQL queries on Bigcommerce Products with WPGraphql plugin
 */
class WPGraph_Product {
	const SINGULAR = 'BCProduct';
	const PLURAL   = 'BCProducts';

	private $config;

	public function __construct( WPGraph_Config $config) {
		$this->config = $config;
	}

	public function register() {
		// Exit if WPGraphql plugin is absent
		if ( ! function_exists( 'register_graphql_object_type' ) ) {
			return;
		}

		$this->register_field_types();
		$this->register_product_properties();
		$this->register_variants();
		$this->register_variants_options();
	}

	/**
	 * Add support for collection fields like BCVariants and BCVariantsOptions
	 */
	protected function register_field_types(): void {
		register_graphql_object_type( 'BCVariants', [
			'description' => __( 'BC Product variants data', 'bigcommerce' ),
			'interfaces'  => ['Node'],
			'fields'      => $this->get_variants_fields_args(),
		] );

		register_graphql_object_type( 'BCVariantsOptions', [
			'description' => __( 'BC Product variants options data', 'bigcommerce' ),
			'interfaces'  => ['Node'],
			'fields'      => $this->get_variants_options_args(),
		] );
	}

	/**
	 * Process single product and register fields for it
	 */
	protected function register_product_properties(): void {
		foreach ( $this->config->get_product_props_description() as $prop => $type ) {
			register_graphql_field( self::SINGULAR, $prop, [
				'type'        => $type,
				'description' => __( sprintf( 'BC product %s', $prop ), 'bigcommerce' ),
				'resolve'     => static function ( $post ) use ( $prop, $type ) {
					$product = new Product( $post->ID );

					if ( empty( $product ) ) {
						switch ( $type ) {
							case WPGraph_Config::INTEGER_TYPE:
							case WPGraph_Config::FLOAT_TYPE:
								return 0;

							case WPGraph_Config::BOOLEAN_TYPE:
								return false;

							case WPGraph_Config::STRING_TYPE:
							default:
								return '';
						}
					}

					if ( $prop === 'bc_id' ) {
						$prop = 'id';
					}

					return $product->get_property( $prop );
				},
			] );
		}
	}

	/**
	 * Register variants collection field
	 */
	protected function register_variants(): void {
		register_graphql_field( self::SINGULAR, 'variants', [
			'type'        => [ 'list_of' => 'BCVariants', ],
			'description' => __( 'BC Variants', 'bigcommerce' ),
			'resolve'     => static function( $post ) {
				$product = new Product( $post->ID );

				if ( empty( $product ) ) {
					return [];
				}

				return array_map( function ( $variant ) {
					return [
						'variant_id'       => $variant->id,
						'inventory'        => $variant->inventory_level,
						'disabled'         => (bool) $variant->purchasing_disabled,
						'disabled_message' => $variant->purchasing_disabled ? $variant->purchasing_disabled_message : '',
						'sku'              => $variant->sku,
						'price'            => $variant->calculated_price,
						'options'          => $variant->option_values,
					];
				}, $product->get_source_data()->variants );
			},
		] );
	}

	/**
	 * Register single variant option values field and tie it to the BCVariants field
	 */
	protected function register_variants_options(): void {
		register_graphql_field( 'BCVariants', 'option_values', [
			'type'        => [ 'list_of' => 'BCVariantsOptions', ],
			'description' => __( 'BC Variants Options', 'bigcommerce' ),
			'resolve'     => static function ( $variant ) {
				if ( empty( $variant ) ) {
					return [];
				}

				return $variant['options'];
			},
		] );
	}

	/**
	 * @param array $source
	 *
	 * @return array
	 */
	private function register_fields( $source = [] ): array {
		$fields = [];

		foreach ( $source as $prop => $type ) {
			$fields[ $prop ] = [
				'type'        => $type,
				'description' => __( sprintf( 'Variant %s field', $prop ), 'bigcommerce' ),
			];
		}

		return $fields;
	}

	/**
	 * @return array
	 */
	private function get_variants_options_args(): array {
		return $this->register_fields( $this->config->get_options_fields_description() );
	}

	/**
	 * @return array
	 */
	private function get_variants_fields_args(): array {
		return $this->register_fields( $this->config->get_variants_fields_description() );
	}

}
