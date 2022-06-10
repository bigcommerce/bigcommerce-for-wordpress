<?php


namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Controls\Multiple_Checkboxes;
use BigCommerce\Customizer\Panels;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Product_Archive {
	const NAME = 'bigcommerce_product_archive';

	const ARCHIVE_TITLE       = 'bigcommerce_product_archive_title';
	const ARCHIVE_SLUG        = 'bigcommerce_product_archive_slug';
	const ARCHIVE_DESCRIPTION = 'bigcommerce_product_archive_description';
	const SORT_OPTIONS        = 'bigcommerce_product_archive_sort_options';
	const FILTER_OPTIONS      = 'bigcommerce_product_archive_filter_options';

	const CATEGORY_SLUG = 'bigcommerce_category_archive_slug';
	const BRAND_SLUG    = 'bigcommerce_brand_archive_slug';

	const SORT_FEATURED        = 'featured';
	const SORT_DATE            = 'date';
	const SORT_SALES           = 'sales';
	const SORT_TITLE_ASC       = 'title_asc';
	const SORT_TITLE_DESC      = 'title_desc';
	const SORT_REVIEWS         = 'reviews';
	const SORT_PRICE_ASC       = 'price_asc';
	const SORT_PRICE_DESC      = 'price_desc';
	const SORT_INVENTORY_COUNT = 'inventory_count';
	const SORT_SKU             = 'sku';

	const FILTER_CATEGORY  = Product_Category::NAME;
	const FILTER_BRAND     = Brand::NAME;
	const PER_PAGE_DEFAULT = 24;
	const PER_PAGE         = 'bigcommerce_products_per_page';
	const GRID_COLUMNS     = 'bigcommerce_catalog_grid_columns';
	const QUICK_VIEW       = 'bigcommerce_enable_quick_view';
	const SEARCH_FIELD     = 'bigcommerce_catalog_enable_search_field';

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @return void
	 */
	public function register( $wp_customize ) {
		$wp_customize->add_section( new \WP_Customize_Section( $wp_customize, self::NAME, [
			'title' => __( 'Product Catalog', 'bigcommerce' ),
			'panel' => Panels\Primary::NAME,
		] ) );

		$this->title( $wp_customize );
		$this->product_post_type_slug( $wp_customize );
		$this->category_taxonomy_slug( $wp_customize );
		$this->brand_taxonomy_slug( $wp_customize );
		$this->description( $wp_customize );
		$this->sorting( $wp_customize );
		$this->filtering( $wp_customize );
		$this->columns( $wp_customize );
		$this->per_page( $wp_customize );
		$this->quick_view( $wp_customize );
		$this->search_field( $wp_customize );
	}

	private function title( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ARCHIVE_TITLE, [
			'type'              => 'option',
			'default'           => __( 'All Products', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::ARCHIVE_TITLE, [
			'section' => self::NAME,
			'label'   => __( 'Catalog Page Title', 'bigcommerce' ),
		] ) );
	}

	private function product_post_type_slug( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ARCHIVE_SLUG, [
			'type'              => 'option',
			'default'           => _x( 'products', 'default product post type archive slug', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::ARCHIVE_SLUG, [
			'section' => self::NAME,
			'label'   => __( 'Catalog Page Slug', 'bigcommerce' ),
		] ) );
	}

	private function description( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::ARCHIVE_DESCRIPTION, [
			'type'              => 'option',
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::ARCHIVE_DESCRIPTION, [
			'section' => self::NAME,
			'label'   => __( 'Catalog Page Description', 'bigcommerce' ),
		] ) );
	}

	private function category_taxonomy_slug( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::CATEGORY_SLUG, [
			'type'              => 'option',
			'default'           => _x( 'categories', 'default taxonomy archive slug', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::CATEGORY_SLUG, [
			'section' => self::NAME,
			'label'   => __( 'Category Page Slug', 'bigcommerce' ),
		] ) );
	}

	private function brand_taxonomy_slug( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::BRAND_SLUG, [
			'type'              => 'option',
			'default'           => _x( 'brands', 'default taxonomy archive slug', 'bigcommerce' ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		] ) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, self::BRAND_SLUG, [
			'section' => self::NAME,
			'label'   => __( 'Brand Page Slug', 'bigcommerce' ),
		] ) );
	}

	private function sorting( \WP_Customize_Manager $wp_customize ) {
		$choices = $this->sort_choices();
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::SORT_OPTIONS, [
			'type'              => 'option',
			'default'           => implode( ',', array_keys( $choices ) ),
			'transport'         => 'refresh',
			'sanitize_callback' => [ $this, 'sanitize_sort' ],
		] ) );
		$wp_customize->add_control( new Multiple_Checkboxes( $wp_customize, self::SORT_OPTIONS, [
			'section' => self::NAME,
			'label'   => __( 'Sorting', 'bigcommerce' ),
			'choices' => $choices,
		] ) );
	}

	public static function sort_choices() {
		$choices = [
			self::SORT_FEATURED        => __( 'Featured', 'bigcommerce' ),
			self::SORT_DATE            => __( 'Newest', 'bigcommerce' ),
			self::SORT_SALES           => __( 'Best Selling', 'bigcommerce' ),
			self::SORT_TITLE_ASC       => __( 'Product Title A–Z', 'bigcommerce' ),
			self::SORT_TITLE_DESC      => __( 'Product Title Z–A', 'bigcommerce' ),
			self::SORT_REVIEWS         => __( 'Reviews', 'bigcommerce' ),
			self::SORT_PRICE_ASC       => __( 'Price (low to high)', 'bigcommerce' ),
			self::SORT_PRICE_DESC      => __( 'Price (high to low)', 'bigcommerce' ),
			self::SORT_INVENTORY_COUNT => __( 'Inventory Count', 'bigcommerce' ),
			self::SORT_SKU             => __( 'SKU', 'bigcommerce' ),
		];

		/**
		 * Filter the sorting options available in the BigCommerce catalog
		 *
		 * @param array $choices The sorting options to use
		 */
		return apply_filters( 'bigcommerce/product/archive/sort_options', $choices );
	}

	public function sanitize_sort( $values ) {
		if ( empty( $values ) ) {
			return $values;
		}
		if ( ! is_array( $values ) ) {
			$values = explode( ',', $values );
		}
		$choices = $this->sort_choices();
		$values  = array_filter( $values, function ( $value ) use ( $choices ) {
			return isset( $choices[ $value ] );
		} );

		return $values;
	}

	private function filtering( \WP_Customize_Manager $wp_customize ) {
		$choices = $this->filter_choices();
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::FILTER_OPTIONS, [
			'type'              => 'option',
			'default'           => implode( ',', array_keys( $choices ) ),
			'transport'         => 'refresh',
			'sanitize_callback' => [ $this, 'sanitize_filter' ],
		] ) );
		$wp_customize->add_control( new Multiple_Checkboxes( $wp_customize, self::FILTER_OPTIONS, [
			'section' => self::NAME,
			'label'   => __( 'Filters', 'bigcommerce' ),
			'choices' => $choices,
		] ) );
	}

	public static function filter_choices() {
		$choices = [
			self::FILTER_CATEGORY => __( 'Categories', 'bigcommerce' ),
			self::FILTER_BRAND    => __( 'Brands', 'bigcommerce' ),
		];

		/**
		 * Filter the filtering options available in the BigCommerce catalog
		 *
		 * @param array $choices The filtering options to use
		 */
		return apply_filters( 'bigcommerce/product/archive/filter_options', $choices );
	}

	public function sanitize_filter( $values ) {
		if ( empty( $values ) ) {
			return $values;
		}
		if ( ! is_array( $values ) ) {
			$values = explode( ',', $values );
		}
		$choices = $this->filter_choices();
		$values  = array_filter( $values, function ( $value ) use ( $choices ) {
			return isset( $choices[ $value ] );
		} );

		return $values;
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

	private function quick_view( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::QUICK_VIEW, [
			'type'      => 'option',
			'default'   => 'yes',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::QUICK_VIEW, [
			'section' => self::NAME,
			'type'    => 'radio',
			'label'   => __( 'Quick View', 'bigcommerce' ),
			'choices' => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
	
	private function search_field( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::SEARCH_FIELD, [
			'type'      => 'option',
			'default'   => 'yes',
			'transport' => 'refresh',
		] ) );
		$wp_customize->add_control( self::SEARCH_FIELD, [
			'section' => self::NAME,
			'type'    => 'radio',
			'label'   => __( 'Search Field', 'bigcommerce' ),
			'choices' => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
}
