<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Customizer\Sections\Catalog;
use BigCommerce\Customizer\Sections\Colors;
use BigCommerce\Customizer\Sections\Product_Single;
use BigCommerce\Settings\Cart;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Product {
	const NAME = 'bigcommerce_product';

	const SOURCE_DATA_META_KEY      = 'bigcommerce_source_data';
	const MODIFIER_DATA_META_KEY    = 'bigcommerce_modifier_data';
	const OPTIONS_DATA_META_KEY     = 'bigcommerce_options_data';
	const CUSTOM_FIELDS_META_KEY    = 'bigcommerce_custom_fields';
	const REQUIRES_REFRESH_META_KEY = 'bigcommerce_force_refresh';
	const IMPORTER_VERSION_META_KEY = 'bigcommerce_importer_version';
	const GALLERY_META_KEY          = 'bigcommerce_gallery';
	const RATING_META_KEY           = 'bigcommerce_rating';
	const SALES_META_KEY            = 'bigcommerce_sales';
	const PRICE_META_KEY            = 'bigcommerce_calculated_price';

	private $post_id;
	private $source_cache;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	public function __get( $property ) {
		return $this->get_property( $property );
	}

	public function get_property( $property ) {
		$data = $this->get_source_data();
		if ( empty( $data ) ) {
			return null;
		}
		if ( isset( $data->$property ) ) {
			return $data->$property;
		}

		return null;
	}

	public function post_id() {
		return $this->post_id;
	}

	public function bc_id() {
		return (int) get_post_meta( $this->post_id, 'bigcommerce_id', true );
	}

	public function sku() {
		return $this->get_property( 'sku' );
	}

	public function brand() {
		$brands = get_the_terms( $this->post_id, Brand::NAME );
		if ( $brands && ! is_wp_error( $brands ) ) {
			return reset( $brands )->name;
		}

		return '';
	}

	public function condition() {
		$terms = get_the_terms( $this->post_id, Condition::NAME );
		if ( empty( $terms ) ) {
			return '';
		}

		return reset( $terms )->name;
	}

	public function show_condition() {
		return has_term( Flag::SHOW_CONDITION, Flag::NAME, $this->post_id );
	}

	public function on_sale() {
		return has_term( Flag::SALE, Flag::NAME, $this->post_id );
	}

	public function price_range() {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$bc_id          = $this->bc_id();
		$original_price = $this->get_property( 'price' );

		$sql    = "SELECT MIN(price) AS low, MAX(price) AS high FROM {$wpdb->bc_variants} WHERE price > 0 GROUP BY bc_id HAVING bc_id=%d";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $bc_id ) );
		if ( empty( $result ) ) {
			return $this->format_currency( $original_price );
		}
		if ( $original_price && $original_price < $result->low ) {
			$result->low = $original_price;
		}
		if ( $original_price && $original_price > $result->high ) {
			$result->high = $original_price;
		}
		if ( $result->low == $result->high ) {
			return $this->format_currency( $result->low );
		}

		return sprintf( _x( '%s - %s', 'price range low to high', 'bigcommerce' ), $this->format_currency( $result->low ), $this->format_currency( $result->high ) );
	}

	public function calculated_price_range() {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$bc_id  = $this->bc_id();
		$sql    = "SELECT MIN(calculated_price) AS low, MAX(calculated_price) AS high FROM {$wpdb->bc_variants} GROUP BY bc_id HAVING bc_id=%d";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $bc_id ) );
		if ( empty( $result ) ) {
			return '';
		}
		if ( $result->low == $result->high ) {
			return $this->format_currency( $result->low );
		}

		return sprintf( _x( '%s - %s', 'price range low to high', 'bigcommerce' ), $this->format_currency( $result->low ), $this->format_currency( $result->high ) );
	}

	private function format_currency( $value ) {
		if ( empty( $value ) ) {
			return __( 'Free', 'bigcommerce' );
		}

		/**
		 * This filter is documented in src/BigCommerce/Templates/Controller.php
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
	}

	public function options() {
		$data = json_decode( get_post_meta( $this->post_id(), self::OPTIONS_DATA_META_KEY, true ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return [];
		}

		// ensure we have all the fields we expect for each option
		$data = array_map( function ( $option ) {
			return wp_parse_args( $option, [
				'id'            => 0,
				'display_name'  => '',
				'type'          => '',
				'sort_order'    => 0,
				'option_values' => [],
			] );
		}, $data );

		// filter out option values not present on any of the variants
		$source          = $this->get_source_data();
		$variant_options = [];
		foreach ( $source->variants as $variant ) {
			foreach ( $variant->option_values as $value ) {
				$variant_options[ $value->option_id ][] = $value->id;
			}
		}
		$variant_options = array_map( 'array_unique', $variant_options );
		$data            = array_map( function ( $option ) use ( $variant_options ) {
			$valid_values = isset( $variant_options[ $option[ 'id' ] ] ) ? $variant_options[ $option[ 'id' ] ] : [];

			$option[ 'option_values' ] = array_filter( $option[ 'option_values' ], function ( $value ) use ( $valid_values ) {
				return in_array( $value[ 'id' ], $valid_values );
			} );

			return $option;
		}, $data );

		// respect the sorting set by the user
		usort( $data, function ( $a, $b ) {
			if ( $a[ 'sort_order' ] == $b[ 'sort_order' ] ) {
				return ( $a[ 'display_name' ] < $b[ 'display_name' ] ) ? - 1 : 1;
			}

			return ( $a[ 'sort_order' ] < $b[ 'sort_order' ] ) ? - 1 : 1;
		} );

		return $data;
	}

	public function get_source_data() {
		if ( isset( $this->source_cache ) ) {
			return $this->source_cache;
		}

		$data = get_post_meta( $this->post_id, self::SOURCE_DATA_META_KEY, true );
		if ( empty( $data ) ) {
			return [];
		}

		$this->source_cache = json_decode( $data );

		return $this->source_cache;
	}

	/**
	 * Check if a product has options.
	 *
	 * @return bool
	 */
	public function has_options() {
		$data = $this->get_source_data();

		return count( $data->variants ) > 1;
	}

	public function update_source_data( $data ) {
		$data = $this->json_encode_maybe_from_api( $data );
		$data = wp_slash( $data ); // WP is going to unslash it before reslashing to add to the DB
		update_post_meta( $this->post_id, self::SOURCE_DATA_META_KEY, $data );
	}

	public function update_modifier_data( $data ) {
		$data = $this->json_encode_maybe_from_api( $data );
		$data = wp_slash( $data ); // WP is going to unslash it before reslashing to add to the DB
		update_post_meta( $this->post_id, self::MODIFIER_DATA_META_KEY, $data );
	}

	public function update_options_data( $data ) {
		$data = $this->json_encode_maybe_from_api( $data );
		$data = wp_slash( $data ); // WP is going to unslash it before reslashing to add to the DB
		update_post_meta( $this->post_id, self::OPTIONS_DATA_META_KEY, $data );
	}

	public function update_custom_field_data( $data ) {
		update_post_meta( $this->post_id, self::CUSTOM_FIELDS_META_KEY, $data );
	}

	/**
	 * Get custom fields for this Product
	 *
	 * @return array[] An array of associative arrays, with the properties:
	 *               - name: the name to display for the field
	 *               - value: the value to display for the field
	 */
	public function get_custom_fields() {
		$data = get_post_meta( $this->post_id, self::CUSTOM_FIELDS_META_KEY, true );

		return is_array( $data ) ? $data : [];
	}

	private function json_encode_maybe_from_api( $data ) {
		$data = $this->maybe_serialize_from_api( $data );
		if ( ! is_scalar( $data ) ) {
			$data = json_encode( $data );
		}

		return $data;
	}

	private function maybe_serialize_from_api( $data ) {
		if ( is_array( $data ) ) {
			$data = array_map( [ $this, 'maybe_serialize_from_api' ], $data );
		}
		if ( is_object( $data ) && method_exists( $data, 'swaggerTypes' ) ) {
			// assume it's an object from the API library
			$serializer = new ObjectSerializer();
			$data       = $serializer->sanitizeForSerialization( $data );
		}

		return $data;
	}

	/**
	 * @return int[] WP post IDs of gallery images
	 */
	public function get_gallery_ids() {
		$data = get_post_meta( $this->post_id, self::GALLERY_META_KEY, true );

		$gallery = is_array( $data ) ? array_filter( array_map( 'intval', $data ) ) : [];

		if ( empty( $gallery ) ) {
			$default = get_option( Product_Single::DEFAULT_IMAGE, 0 );
			if ( ! empty( $default ) ) {
				$gallery = [ absint( $default ) ];
			}
		}

		/**
		 * Filter the images that display in a product gallery
		 *
		 * @param int[] $gallery The IDs of images in the gallery
		 */
		return apply_filters( 'bigcommerce/product/gallery', $gallery );
	}

	public function purchase_url() {
		if ( get_option( Cart::OPTION_ENABLE_CART, true ) ) {
			return home_url( sprintf( 'bigcommerce/cart/%d', $this->post_id ) );
		}

		return home_url( sprintf( 'bigcommerce/buy/%d', $this->post_id ) );
	}

	public function purchase_button() {
		$options = $this->has_options() ? 'disabled="disabled"' : '';
		if ( get_option( Cart::OPTION_ENABLE_CART, true ) ) {
			$label  = get_option( Buttons::ADD_TO_CART, __( 'Add to Cart', 'bigcommerce' ) );
			$button = sprintf( '<button class="bc-btn bc-btn--form-submit bc-btn--add_to_cart" type="submit" %s>%s</button>', $options, $label );
		} else {
			$label  = get_option( Buttons::BUY_NOW, __( 'Buy Now', 'bigcommerce' ) );
			$button = sprintf( '<button class="bc-btn bc-btn--form-submit bc-btn--buy" type="submit" %s>%s</button>', $options, $label );
		}

		return apply_filters( 'bigcommerce/button/purchase', $button, $this->post_id );
	}

	public function get_inventory_level( $variant_id = 0 ) {
		$data = $this->get_source_data();
		if ( $data->inventory_tracking == 'none' ) {
			return - 1;
		}
		if ( $data->inventory_tracking == 'variant' && ! empty( $variant_id ) ) {
			foreach ( $data->variants as $variant ) {
				if ( $variant_id == $variant->id ) {
					return (int) $variant->inventory_level;
				}
			}
		}

		return (int) $data->inventory_level;
	}

	/**
	 * Checks if a product is out of stock. If a variant ID
	 * is given and the product uses variant-level inventory
	 * tracking, then it will be checked against the specific
	 * variant.
	 *
	 * @param int $variant_id
	 *
	 * @return bool If the product is out of stock
	 */
	public function out_of_stock( $variant_id = 0 ) {
		if ( has_term( Flag::OUT_OF_STOCK, Flag::NAME, $this->post_id ) ) {
			return true;
		}
		$inventory_level = $this->get_inventory_level( $variant_id );
		if ( $inventory_level === 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool Whether the product is below the "low inventory" threshold
	 */
	public function low_inventory() {
		return has_term( Flag::LOW_INVENTORY, Flag::NAME, $this->post_id );
	}

	/**
	 * Get a list of products related to this one
	 *
	 * @param array $args Additional args to pass to WP_Query
	 *
	 * @return int[] The IDs of related products
	 */
	public function related_products( array $args = [] ) {
		$args = wp_parse_args( $args, [
			'posts_per_page' => 10,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'title',
		] );
		$args = array_merge( $args, [
			'post_type'        => Product::NAME,
			'fields'           => 'ids',
			'suppress_filters' => false,
			'post__not_in'     => [ $this->post_id ],
		] );

		$related_meta = $this->get_property( 'related_products' );
		if ( empty( $related_meta ) || ! is_array( $related_meta ) ) {
			// User has explicitly set it to hide related products
			/**
			 * This filter is documented in src/BigCommerce/Post_Types/Product/Product.php
			 */
			return apply_filters( 'bigcommerce/product/related_products', [], $this->post_id );
		}
		$related_meta = array_map( 'intval', $related_meta );

		if ( in_array( - 1, $related_meta ) ) {
			// User has set it to automatically calculate related products
			/**
			 * This filter is documented in src/BigCommerce/Post_Types/Product/Product.php
			 */
			return apply_filters( 'bigcommerce/product/related_products', $this->related_products_by_category( $args ), $this->post_id );
		}

		$args[ 'bigcommerce_id__in' ] = $related_meta;

		$related_products = array_map( 'intval', get_posts( $args ) );

		/**
		 * Filter the related products to display for the current product
		 *
		 * @param int[] $related The IDs of related product posts
		 * @param int   $current The current post ID
		 */
		return apply_filters( 'bigcommerce/product/related_products', $related_products, $this->post_id );
	}

	/**
	 * Identify products that share one or more categories with this one
	 *
	 * @param array $args Args to pass to WP_Query
	 *
	 * @return int[]
	 */
	private function related_products_by_category( array $args ) {
		$categories = get_the_terms( $this->post_id, Product_Category::NAME );
		if ( empty( $categories ) ) {
			return []; // nothing to work with
		}

		$term_ids              = array_map( 'intval', wp_list_pluck( $categories, 'term_id' ) );
		$args[ 'tax_query' ][] = [
			'taxonomy' => Product_Category::NAME,
			'field'    => 'term_id',
			'terms'    => $term_ids,
			'operator' => 'IN',
		];

		return get_posts( $args );
	}

	/**
	 * Get the reviews associated with this product
	 *
	 * @param array $args An array of query parameters
	 *
	 * @return array
	 */
	public function get_reviews( array $args = [] ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$args = wp_parse_args( $args, [
			'per_page' => 12,
			'page'     => 1,
			'status'   => 'approved',
			'orderby'  => 'date_reviewed',
			'order'    => 'DESC',
		] );

		$per_page = absint( $args[ 'per_page' ] ) ?: 12;
		$offset   = ( absint( $args[ 'page' ] ) - 1 ) * $per_page;

		$args[ 'order' ]   = ( strtoupper( $args[ 'order' ] ) === 'DESC' ? 'DESC' : 'ASC' );
		$args[ 'orderby' ] = in_array( $args[ 'orderby' ], [
			'date_reviewed',
			'date_created',
			'date_modified',
			'author_name',
			'title',
			'rating',
		] ) ? sanitize_key( $args[ 'orderby' ] ) : 'date_reviewed';

		$orderby = sprintf( "ORDER BY %s %s", $args[ 'orderby' ], $args[ 'order' ] );
		$limit   = sprintf( "LIMIT %d, %d", $offset, $per_page );

		$sql     = "SELECT * FROM {$wpdb->bc_reviews} WHERE post_id=%d AND status=%s $orderby $limit";
		$sql     = $wpdb->prepare( $sql, $this->post_id, $args[ 'status' ] );
		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results ?: [];
	}

	/**
	 * Get the total number of reviews for the product
	 *
	 * @param string $status
	 *
	 * @return int
	 */
	public function get_review_count( $status = 'approved' ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$sql   = "SELECT COUNT(*) FROM {$wpdb->bc_reviews} WHERE post_id=%d AND status=%s";
		$sql   = $wpdb->prepare( $sql, $this->post_id, $status );
		$count = $wpdb->get_var( $sql );

		return intval( $count );
	}
}