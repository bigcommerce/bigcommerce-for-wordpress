<?php


namespace BigCommerce\Customizer\Sections;

use BigCommerce\Customizer\Controls\Multiple_Checkboxes;
use BigCommerce\Customizer\Panels;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Customizer settings and controls for the Product Archive section.
 *
 * This class defines constants and methods to register and manage customizer
 * options for the product catalog, including settings for sorting, filtering,
 * slugs, and layout.
 */
class Product_Archive {
    /** @var string The identifier for the Product Archive section in the customizer. */
    const NAME = 'bigcommerce_product_archive';

    /** @var string Setting key for the product archive title. */
    const ARCHIVE_TITLE = 'bigcommerce_product_archive_title';

    /** @var string Setting key for the product archive slug. */
    const ARCHIVE_SLUG = 'bigcommerce_product_archive_slug';

    /** @var string Setting key for the product archive description. */
    const ARCHIVE_DESCRIPTION = 'bigcommerce_product_archive_description';

    /** @var string Setting key for the available sorting options in the product archive. */
    const SORT_OPTIONS = 'bigcommerce_product_archive_sort_options';

    /** @var string Setting key for the available filtering options in the product archive. */
    const FILTER_OPTIONS = 'bigcommerce_product_archive_filter_options';

    /** @var string Setting key for the category archive slug. */
    const CATEGORY_SLUG = 'bigcommerce_category_archive_slug';

    /** @var string Setting key for the brand archive slug. */
    const BRAND_SLUG = 'bigcommerce_brand_archive_slug';

	/**
	 * Sort by featured products.
	 * @var string
	 */
	const SORT_FEATURED = 'featured';

	/**
	 * Sort by date added (newest first).
	 * @var string
	 */
	const SORT_DATE = 'date';

	/**
	 * Sort by sales performance.
	 * @var string
	 */
	const SORT_SALES = 'sales';

	/**
	 * Sort by title (A-Z).
	 * @var string
	 */
	const SORT_TITLE_ASC = 'title_asc';

	/**
	 * Sort by title (Z-A).
	 * @var string
	 */
	const SORT_TITLE_DESC = 'title_desc';

	/**
	 * Sort by customer reviews.
	 * @var string
	 */
	const SORT_REVIEWS = 'reviews';

	/**
	 * Sort by price (low to high).
	 * @var string
	 */
	const SORT_PRICE_ASC = 'price_asc';

	/**
	 * Sort by price (high to low).
	 * @var string
	 */
	const SORT_PRICE_DESC = 'price_desc';

	/**
	 * Sort by inventory count.
	 * @var string
	 */
	const SORT_INVENTORY_COUNT = 'inventory_count';

	/**
	 * Sort by SKU.
	 * @var string
	 */
	const SORT_SKU = 'sku';

	/** 
	 * The filter key for product categories in the catalog.
	 * 
	 * @var string 
	 */
	const FILTER_CATEGORY = Product_Category::NAME;

	/** 
	 * The filter key for product brands in the catalog.
	 * 
	 * @var string 
	 */
	const FILTER_BRAND = Brand::NAME;

    /** @var int Default number of products per page. */
    const PER_PAGE_DEFAULT = 24;

    /** @var string Setting key for the number of products displayed per page. */
    const PER_PAGE = 'bigcommerce_products_per_page';

    /** @var string Setting key for the number of grid columns in the product catalog. */
    const GRID_COLUMNS = 'bigcommerce_catalog_grid_columns';

    /** @var string Setting key for enabling the Quick View feature in the product catalog. */
    const QUICK_VIEW = 'bigcommerce_enable_quick_view';

    /** @var string Setting key for enabling the search field in the product catalog. */
    const SEARCH_FIELD = 'bigcommerce_catalog_enable_search_field';

    /** @var string Setting key for respecting general inventory settings. */
    const GENERAL_INVENTORY = 'bigcommerce_general_inventory_settings';

    /**
     * Registers customizer settings and controls for the product archive section.
     *
     * @param \WP_Customize_Manager $wp_customize WordPress Customizer manager instance.
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
		$this->respect_general_inventory_settings( $wp_customize );
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

    /**
     * Retrieves the available sorting choices for the product catalog.
     *
     * @return array An associative array of sorting choices with keys and labels.
     */
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

    /**
     * Sanitizes and validates the sorting choices.
     *
     * @param array|string $values The sorting choices input to sanitize.
     * @return array Sanitized sorting choices.
     */
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

	/**
	 * Retrieves filtering choices for the BigCommerce catalog.
	 *
	 * @return array The filtering options available.
	 */
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

	/**
	 * Sanitizes the provided filter values to ensure they match available choices.
	 *
	 * @param array|string $values The filter values to sanitize.
	 * @return array The sanitized filter values.
	 */
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
			'description' => $this->get_field_description(),
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
			'description' => $this->get_field_description(),
			'input_attrs' => [
				'min' => min( $range ),
				'max' => max( $range ),
			],
		] ) );
	}

	/**
	 * Gets the field description for display in the WordPress Customizer.
	 *
	 * @return string The description HTML or an empty string.
	 */
	public function get_field_description() {
		if ( ! ( function_exists( 'et_setup_theme' ) || function_exists( 'et_framework_setup' ) ) ) {
			return '';
		}

		$description = __( 'Divi framework is enabled. The field value may be overwritten with Divi options. Check Divi builder/theme options in order to set proper values', 'bigcommerce' );

		return sprintf( '<cite class="bc-form__error-message">%s</cite>', $description );
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

	private function respect_general_inventory_settings( \WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, self::GENERAL_INVENTORY, [
			'type'      => 'option',
			'default'   => 'no',
			'transport' => 'refresh',
		] ) );

		$description = __( 'Manage on Bigcommerce', 'bigcommerce' );
		$description = sprintf( '<a href="%s" target="_blank">%s</a>', 'https://login.bigcommerce.com/deep-links/manage/settings/inventory', $description );

		$wp_customize->add_control( self::GENERAL_INVENTORY, [
			'section'     => self::NAME,
			'type'        => 'radio',
			'label'       => __( 'Respect General Inventory Settings', 'bigcommerce' ),
			'description' => $description,
			'choices'     => [
				'yes' => __( 'Enabled', 'bigcommerce' ),
				'no'  => __( 'Disabled', 'bigcommerce' ),
			],
		] );
	}
}
