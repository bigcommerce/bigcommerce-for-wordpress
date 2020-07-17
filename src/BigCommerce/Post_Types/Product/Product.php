<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Currency\With_Currency;
use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Customizer\Sections\Product_Single;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Settings\Sections\Cart;
use BigCommerce\Settings\Sections\Channels;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Product {
	use With_Currency;

	const NAME = 'bigcommerce_product';

	const BIGCOMMERCE_ID            = 'bigcommerce_id';
	const LISTING_ID                = 'bigcommerce_listing_id';
	const SKU                       = 'bigcommerce_sku';
	const SOURCE_DATA_META_KEY      = 'bigcommerce_source_data';
	const LISTING_DATA_META_KEY     = 'bigcommerce_listing_data';
	const MODIFIER_DATA_META_KEY    = 'bigcommerce_modifier_data';
	const OPTIONS_DATA_META_KEY     = 'bigcommerce_options_data';
	const CUSTOM_FIELDS_META_KEY    = 'bigcommerce_custom_fields';
	const REQUIRES_REFRESH_META_KEY = 'bigcommerce_force_refresh';
	const IMPORTER_VERSION_META_KEY = 'bigcommerce_importer_version';
	const DATA_HASH_META_KEY        = 'bigcommerce_data_hash';
	const GALLERY_META_KEY          = 'bigcommerce_gallery';
	const VARIANT_IMAGES_META_KEY   = 'bigcommerce_variant_images';
	const RATING_META_KEY           = 'bigcommerce_rating';
	const SALES_META_KEY            = 'bigcommerce_sales';
	const PRICE_META_KEY            = 'bigcommerce_calculated_price';
	const PRICE_RANGE_META_KEY      = 'bigcommerce_price_range';
	const INVENTORY_META_KEY        = 'bigcommerce_inventory_level';

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
		$original_price = $this->get_property( 'price' );
		/**
		 * Filter the price range data for a product
		 *
		 * @param array   $prices  The price range meta for the product
		 * @param Product $product The product object
		 */
		$prices = apply_filters( 'bigcommerce/product/price_range/data', get_post_meta( $this->post_id, self::PRICE_RANGE_META_KEY, true ), $this );
		$low    = isset( $prices['price']['min'] ) ? $prices['price']['min'] : 0;
		$high   = isset( $prices['price']['max'] ) ? $prices['price']['max'] : 0;

		if ( $original_price && $original_price < $low ) {
			$low = $original_price;
		}
		if ( $original_price && $original_price > $high ) {
			$high = $original_price;
		}
		if ( $low == $high ) {
			$range = $this->format_currency( $low, __( 'Free', 'bigcommerce' ) );
		} else {
			$range = sprintf( _x( '%s - %s', 'price range low to high', 'bigcommerce' ), $this->format_currency( $low, __( 'Free', 'bigcommerce' ) ), $this->format_currency( $high, __( 'Free', 'bigcommerce' ) ) );
		}

		/**
		 * Filter the formatted price range for a product
		 *
		 * @param string  $range   The formatted price range
		 * @param Product $product The product object
		 * @param array   $prices  The price range meta for the product
		 */
		return apply_filters( 'bigcommerce/product/price_range/formatted', $range, $this, $prices );
	}

	public function calculated_price_range() {
		if ( has_term( Flag::HIDE_PRICE, Flag::NAME, $this->post_id ) ) {
			return '';
		}
		/**
		 * This filter is documented in src/BigCommerce/Post_Types/Product/Product.php
		 */
		$prices = apply_filters( 'bigcommerce/product/price_range/data', get_post_meta( $this->post_id, self::PRICE_RANGE_META_KEY, true ), $this );
		$low    = isset( $prices['calculated']['min'] ) ? $prices['calculated']['min'] : 0;
		$high   = isset( $prices['calculated']['max'] ) ? $prices['calculated']['max'] : 0;

		if ( $low == $high ) {
			$range = $this->format_currency( $low, __( 'Free', 'bigcommerce' ) );
		} else {
			$range = sprintf( _x( '%s - %s', 'price range low to high', 'bigcommerce' ), $this->format_currency( $low, __( 'Free', 'bigcommerce' ) ), $this->format_currency( $high, __( 'Free', 'bigcommerce' ) ) );
		}

		/**
		 * Filter the formatted calculated price range for a product
		 *
		 * @param string  $range   The formatted price range
		 * @param Product $product The product object
		 * @param array   $prices  The price range meta for the product
		 */
		return apply_filters( 'bigcommerce/product/calculated_price_range/formatted', $range, $this, $prices );
	}

	/**
	 * Get the retail price (MSRP) of the product
	 *
	 * @return string The formatted currency string for the product's retail price
	 */
	public function retail_price() {
		/**
		 * Filter the retail price of the product
		 *
		 * @param float   $retail_price The retail price of the product
		 * @param Product $product      The product object
		 */
		$price = apply_filters( 'bigcommerce/produce/retail_price/data', (float) $this->get_property( 'retail_price' ), $this );
		if ( $price ) {
			/**
			 * Filter the formatted retail price for a product
			 *
			 * @param string  $retail_price The formatted retail price
			 * @param Product $product      The product object
			 */
			return apply_filters( 'bigcommerce/product/retail_price/formatted', $this->format_currency( $price ), $this );
		}

		return '';
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
				'required'      => true,
				'config'        => [],
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
			$valid_values = isset( $variant_options[ $option['id'] ] ) ? $variant_options[ $option['id'] ] : [];

			$option['option_values'] = array_filter( $option['option_values'], function ( $value ) use ( $valid_values ) {
				return in_array( $value['id'], $valid_values );
			} );

			return $option;
		}, $data );

		// respect the sorting set by the user
		usort( $data, function ( $a, $b ) {
			if ( $a['sort_order'] == $b['sort_order'] ) {
				return ( $a['display_name'] < $b['display_name'] ) ? - 1 : 1;
			}

			return ( $a['sort_order'] < $b['sort_order'] ) ? - 1 : 1;
		} );

		return $data;
	}

	/**
	 * Get the product source data cached for this product
	 *
	 * @return object
	 */
	public function get_source_data() {
		if ( isset( $this->source_cache ) ) {
			return $this->source_cache;
		}

		$data = get_post_meta( $this->post_id, self::SOURCE_DATA_META_KEY, true );
		if ( empty( $data ) ) {
			return new \stdClass();
		}

		$this->source_cache = json_decode( $data );

		return $this->source_cache;
	}

	/**
	 * Get the channel listing data cached for this product
	 *
	 * @return object
	 */
	public function get_listing_data() {
		$data = get_post_meta( $this->post_id, self::LISTING_DATA_META_KEY, true );
		if ( empty( $data ) ) {
			return new \stdClass();
		}

		return json_decode( $data );
	}

	/**
	 * Check if a product has options.
	 *
	 * @return bool
	 */
	public function has_options() {
		$options   = $this->options();
		$modifiers = $this->modifiers();
		if ( count( $options ) > 0 || count( $modifiers ) > 0 ) {
			return true;
		}

		return false;
	}


	public function modifiers() {
		$data = json_decode( get_post_meta( $this->post_id(), self::MODIFIER_DATA_META_KEY, true ), true );
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
				'required'      => false,
				'config'        => [],
				'option_values' => [],
			] );
		}, $data );


		// respect the sorting set by the user
		usort( $data, function ( $a, $b ) {
			if ( $a['sort_order'] === $b['sort_order'] ) {
				return ( $a['display_name'] < $b['display_name'] ) ? - 1 : 1;
			}

			return ( $a['sort_order'] < $b['sort_order'] ) ? - 1 : 1;
		} );

		return $data;
	}

	public function update_source_data( $data ) {
		$data = $this->json_encode_maybe_from_api( $data );
		$data = wp_slash( $data ); // WP is going to unslash it before reslashing to add to the DB
		update_post_meta( $this->post_id, self::SOURCE_DATA_META_KEY, $data );
	}

	public function update_listing_data( $data ) {
		$data = $this->json_encode_maybe_from_api( $data );
		$data = wp_slash( $data ); // WP is going to unslash it before reslashing to add to the DB
		update_post_meta( $this->post_id, self::LISTING_DATA_META_KEY, $data );
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
			$data = wp_json_encode( $data );
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

	/**
	 * Get the list of YouTube videos associated with the product
	 *
	 * @return array
	 */
	public function youtube_videos() {
		$videos = $this->get_property( 'videos' ) ?: [];
		$videos = array_filter( $videos, function ( $video ) {
			return ! empty( $video->video_id ) && ! empty( $video->type ) && $video->type === 'youtube';
		} );
		usort( $videos, function ( $a, $b ) {
			if ( $a->sort_order === $b->sort_order ) {
				return ( $a->title < $b->title ) ? - 1 : 1;
			}

			return ( $a->sort_order < $b->sort_order ) ? - 1 : 1;
		} );

		return array_map( function ( $video ) {
			return [
				'url'         => sprintf( 'https://www.youtube.com/watch?v=%s', urlencode( $video->video_id ) ),
				'embed_url'   => sprintf( 'https://www.youtube.com/embed/%s', urlencode( $video->video_id ) ),
				'id'          => $video->video_id,
				'title'       => $video->title,
				'description' => $video->description,
				'length'      => $video->length,
			];
		}, $videos );
	}

	public function purchase_url() {
		if ( get_option( Cart::OPTION_ENABLE_CART, true ) ) {
			return home_url( sprintf( 'bigcommerce/cart/%d', $this->post_id ) );
		}

		return home_url( sprintf( 'bigcommerce/buy/%d', $this->post_id ) );
	}

	public function purchase_button() {
		$options  = $this->has_options() || $this->out_of_stock() ? 'disabled="disabled"' : '';
		$preorder = $this->availability() === Availability::PREORDER;
		$cart     = get_option( Cart::OPTION_ENABLE_CART, true );
		$class    = 'bc-btn bc-btn--form-submit';
		if ( $preorder ) {
			$class .= ' bc-btn--preorder';
		}
		if ( $cart ) {
			$class .= ' bc-btn--add_to_cart';
			$label = $preorder ? get_option( Buttons::PREORDER_TO_CART, __( 'Add to Cart', 'bigcommerce' ) ) : get_option( Buttons::ADD_TO_CART, __( 'Add to Cart', 'bigcommerce' ) );
		} else {
			$class .= ' bc-btn--buy';
			$label = $preorder ? get_option( Buttons::PREORDER_NOW, __( 'Pre-Order Now', 'bigcommerce' ) ) : get_option( Buttons::BUY_NOW, __( 'Buy Now', 'bigcommerce' ) );
		}
		$button = sprintf( '<button class="%s" type="submit" data-js="%d" %s>%s</button>', $class, $this->bc_id(), $options, $label );

		return apply_filters( 'bigcommerce/button/purchase', $button, $this->post_id, $label );
	}

	public function purchase_message() {
		$preorder = $this->availability() === Availability::PREORDER;
		if ( ! $preorder ) {
			return '';
		}
		$source  = $this->get_source_data();
		$date    = isset( $source->preorder_release_date ) ? strtotime( $source->preorder_release_date ) : 0;
		$message = isset( $source->preorder_message ) ? $source->preorder_message : '';
		$message = str_replace( '%%DATE%%', '%s', $message );

		$default_message   = __( 'Available for pre-order.', 'bigcommerce' );
		$default_with_date = __( 'Available for pre-order. Expected release date is %s.', 'bigcommerce' );

		if ( empty( $date ) && strpos( $message, '%s' ) !== false ) {
			$message = '';
		}

		if ( empty( $message ) ) {
			$message = empty( $date ) ? $default_message : $default_with_date;
		}

		$date_string = $date ? date_i18n( get_option( 'date_format', 'Y-m-d' ), $date ) : '';

		return sprintf( $message, $date_string );
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
	 * Get the availability for the product
	 *
	 * @return string
	 */
	public function availability() {
		$terms = get_the_terms( $this->post_id, Availability::NAME );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return Availability::AVAILABLE;
		}

		return reset( $terms )->slug;
	}


	/**
	 * Checks if a product can be purchased, considering
	 * both the purchasability setting and inventory levels
	 *
	 * @param int $variant_id
	 *
	 * @return bool If the product is out of stock
	 */
	public function is_purchasable( $variant_id = 0 ) {
		$availabilty = $this->availability();
		if ( $availabilty === Availability::DISABLED ) {
			return false;
		}

		return ! $this->out_of_stock( $variant_id );
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

		$args['bigcommerce_id__in'] = $related_meta;

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

		$term_ids            = array_map( 'intval', wp_list_pluck( $categories, 'term_id' ) );
		$args['tax_query'][] = [
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

		$per_page = absint( $args['per_page'] ) ?: 12;
		$offset   = ( absint( $args['page'] ) - 1 ) * $per_page;

		$args['order']   = ( strtoupper( $args['order'] ) === 'DESC' ? 'DESC' : 'ASC' );
		$args['orderby'] = in_array( $args['orderby'], [
			'date_reviewed',
			'date_created',
			'date_modified',
			'author_name',
			'title',
			'rating',
		] ) ? sanitize_key( $args['orderby'] ) : 'date_reviewed';

		$orderby = sprintf( "ORDER BY %s %s", $args['orderby'], $args['order'] );
		$limit   = sprintf( "LIMIT %d, %d", $offset, $per_page );

		$sql     = "SELECT * FROM {$wpdb->bc_reviews} WHERE bc_id=%d AND status=%s $orderby $limit";
		$sql     = $wpdb->prepare( $sql, $this->bc_id(), $args['status'] );
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

		$sql   = "SELECT COUNT(*) FROM {$wpdb->bc_reviews} WHERE bc_id=%d AND status=%s";
		$sql   = $wpdb->prepare( $sql, $this->bc_id(), $status );
		$count = $wpdb->get_var( $sql );

		return intval( $count );
	}

	/**
	 * Get the channel ID associated with this post
	 *
	 * @return int The BigCommerce channel ID
	 */
	public function get_channel_id() {
		try {
			$channel    = $this->get_channel();
			$channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );

			return (int) $channel_id;
		} catch ( Channel_Not_Found_Exception $e ) {
			return (int) get_option( Channels::CHANNEL_ID, 0 );
		}
	}

	/**
	 * Get the Channel term associated with this post
	 *
	 * @return \WP_Term The WordPress Channel term
	 */
	public function get_channel() {
		$channels = get_the_terms( $this->post_id(), Channel::NAME );
		if ( empty( $channels ) ) {
			$connections = new Connections();

			return $connections->current();
		}

		return reset( $channels );
	}

	/**
	 * Get the Listing ID associated with this post
	 *
	 * @return int The BigCommerce Listing ID
	 */
	public function get_listing_id() {
		$listing = $this->get_listing_data();
		if ( ! empty( $listing ) && isset( $listing->listing_id ) ) {
			return (int) $listing->listing_id;
		}

		return 0;
	}

	/**
	 * Gets a BigCommerce Product ID and returns matching Product object
	 *
	 * @param int           $product_id
	 *
	 * @param \WP_Term|null $channel
	 * 
	 * @param array         $query_args
	 *
	 * @return Product|array
	 */
	public static function by_product_id( $product_id, \WP_Term $channel = null, $query_args = [] ) {

		if ( empty( $product_id ) ) {
			throw new \InvalidArgumentException( __( 'Product ID must be a positive integer', 'bigcommerce' ) );
		}

		$args = [
			'meta_query'     => [
				[
					'key'   => 'bigcommerce_id',
					'value' => absint( $product_id ),
				],
			],
			'post_type'      => self::NAME,
			'posts_per_page' => 1,
		];

		if ( $channel === null ) {
			// use the current channel
			$connections = new Connections();
			$channel     = $connections->current();
		}

		if ( $channel ) {
			$args['tax_query'] = [
				[
					'taxonomy' => Channel::NAME,
					'field'    => 'term_id',
					'terms'    => [ (int) $channel->term_id ],
					'operator' => 'IN',
				],
			];
		}

		$args = array_merge( $args, $query_args );

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			throw new Product_Not_Found_Exception( sprintf( __( 'No product found matching BigCommerce ID %d', 'bigcommerce' ), $product_id ) );
		}

		return new Product( $posts[0]->ID );
	}
}
