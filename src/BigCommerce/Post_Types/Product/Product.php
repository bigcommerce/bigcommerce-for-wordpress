<?php


namespace BigCommerce\Post_Types\Product;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Container\Api;
use BigCommerce\Container\GraphQL;
use BigCommerce\Currency\With_Currency;
use BigCommerce\Customizer\Sections\Buttons;
use BigCommerce\Customizer\Sections\Cart as Cart_Settings;
use BigCommerce\Customizer\Sections\Cart as CustomizerCart;
use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Customizer\Sections\Product_Single;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Import\Import_Type;
use BigCommerce\Import\Image_Importer;
use BigCommerce\Import\Processors\Storefront_Processor;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Settings\Sections\Cart;
use BigCommerce\Settings\Sections\Channels;
use BigCommerce\Settings\Sections\Import;
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
	const BRAND_TRANSIENT           = 'bigcommerce_brand_transient';
	const LISTING_ID                = 'bigcommerce_listing_id';
	const SKU                       = 'bigcommerce_sku';
	const SKU_NORMALIZED            = 'bigcommerce_sku_normalized';
	const SOURCE_DATA_META_KEY      = 'bigcommerce_source_data';
	const LISTING_DATA_META_KEY     = 'bigcommerce_listing_data';
	const MODIFIER_DATA_META_KEY    = 'bigcommerce_modifier_data';
	const OPTIONS_DATA_META_KEY     = 'bigcommerce_options_data';
	const OPTIONS_DATA_TRANSIENT    = 'bigcommerce_options_transient';
	const CUSTOM_FIELDS_META_KEY    = 'bigcommerce_custom_fields';
	const REQUIRES_REFRESH_META_KEY = 'bigcommerce_force_refresh';
	const IMPORTER_VERSION_META_KEY = 'bigcommerce_importer_version';
	const DATA_HASH_META_KEY        = 'bigcommerce_data_hash';
	const GALLERY_META_KEY          = 'bigcommerce_gallery';
	const VARIANT_IMAGES_META_KEY   = 'bigcommerce_variant_images';
	const RATING_META_KEY           = 'bigcommerce_rating';
	const RATING_SUM_META_KEY       = 'bigcommerce_review_rating_sum';
	const REVIEW_COUNT_META_KEY     = 'bigcommerce_review_count';
	const REVIEWS_APPROVED_META_KEY = 'bigcommerce_approved_review_count';
	const REVIEW_CACHE              = 'bigcommerce_reviews';
	const SALES_META_KEY            = 'bigcommerce_sales';
	const PRICE_META_KEY            = 'bigcommerce_calculated_price';
	const PRICE_RANGE_META_KEY      = 'bigcommerce_price_range';
	const INVENTORY_META_KEY        = 'bigcommerce_inventory_level';

	private $post_id;
	private $source_cache;
	private $is_headless;

	public function __construct( $post_id ) {
		$this->post_id     = $post_id;
		$this->is_headless = ! Import_Type::is_traditional_import();
	}

	public function get_redirect_product_link() {
		$should_respect_main_settings = get_option( Product_Archive::GENERAL_INVENTORY, 'no' ) !== 'no';
		if ( ! $should_respect_main_settings || ! $this->out_of_stock() ) {
			return '';
		}

		$product_behaviour = get_option( Store_Settings::PRODUCT_OUT_OF_STOCK, 'do_nothing' );

		if ( $product_behaviour === 'hide_product_and_redirect' ) {
			$categories = get_the_terms( $this->post_id(), Product_Category::NAME );

			if ( empty( $categories ) ) {
				return '';
			}

			return get_term_link( $categories[0], Product_Category::NAME );
		}

		if ( $product_behaviour === 'hide_product' ) {
			$setting = get_option( CustomizerCart::EMPTY_CART_LINK, Cart_Settings::LINK_HOME );
			switch ( $setting ) {
				case Cart_Settings::LINK_CATALOG:
					return get_post_type_archive_link( Product::NAME );
					break;
				case Cart_Settings::LINK_HOME:
				default:
					return home_url( '/' );
					break;
			}

		}

		return '';
	}

	public function __get( $property ) {
		return $this->get_property( $property );
	}

	public function is_headless() {
		return $this->is_headless;
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
		if ( $this->is_headless() ) {
			$source = $this->get_source_data();

			return $source->sku ?? null;
		}

		return $this->get_property( 'sku' );
	}

	public function get_reviews_sum() {
		if ( $this->is_headless() ) {
			$source = $this->get_source_data();

			return ! empty( $source->reviews_rating_sum ) ? $source->reviews_rating_sum : 0;
		}

		return $this->get_property( 'reviews_rating_sum' );
	}

	public function get_reviews_count() {
		if ( $this->is_headless() ) {
			$source = $this->get_source_data();

			return ! empty( $source->reviews_count ) ? (int) $source->reviews_count : 0;
		}

		return $this->get_property( 'reviews_count' );
	}

	private function get_product_cache_expiration() {
		return get_option( Import::PRODUCT_TRANSIENT, 15 * MINUTE_IN_SECONDS );
	}

	public function brand() {
		if ( $this->is_headless() ) {
			$transient_key = sprintf( '%s%d', self:: BRAND_TRANSIENT, $this->post_id );
			$transient     = get_transient( $transient_key );

			if ( ! empty( $transient ) ) {
				return $transient;
			}

			$source = $this->get_source_data();
			if ( empty( $source->brand_id ) ) {
				return '';
			}


			$container   = bigcommerce()->container();
			$catalog_api = $container[ Api::FACTORY ]->catalog();
			try {
				 $name = $catalog_api->getBrandById( $source->brand_id )->getData()->getName();
				 set_transient( $transient_key, $name, $this->get_product_cache_expiration() );

				 return $name;
			} catch ( ApiException $exception ) {
				return '';
			}
		}

		$brands = get_the_terms( $this->post_id, Brand::NAME );

		if ( $brands && ! is_wp_error( $brands ) ) {
			return reset( $brands )->name;
		}

		return '';
	}

	public function condition() {
		$terms = get_the_terms( $this->post_id, Condition::NAME );
		if ( empty( $terms ) || is_bool( $terms ) ) {
			return '';
		}

		return reset( $terms )->name;
	}

	public function show_condition() {
		return has_term( Flag::SHOW_CONDITION, Flag::NAME, $this->post_id );
	}

	public function on_sale() {
		if ( $this->is_headless() ) {
			$source = $this->get_source_data();
			if ( class_exists( 'BigCommerceReactTemplates\Plugin' ) ) {
				return false;
			}
			$sale = $source->sale_price;

			return ! empty( $sale );
		}

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

	public function get_product_options() {
		$transient_key = sprintf( '%s%d', self::OPTIONS_DATA_TRANSIENT, $this->post_id );
		$transient     = get_transient( $transient_key );

		if ( ! empty( $transient ) ) {
			return $transient;
		}

		$catalog_api = bigcommerce()->container()[ Api::FACTORY ]->catalog();

		try {
			$response = $catalog_api->getOptions( $this->bc_id() );
			$data     = array_map( function ( $object ) {
				$config        = $object->getConfig();
				$option_values = $object->getOptionValues();
				$values        = [];
				foreach ( $option_values as $option ) {
					$values[] = $option->get();
				}

				return [
					'id'            => $object->getId(),
					'product_id'    => $object->getProductId(),
					'display_name'  => $object->getDisplayName(),
					'type'          => $object->getType(),
					'sort_order'    => $object->getSortOrder(),
					'config'        => $config->get(),
					'option_values' => $values,
				];
			}, $response->getData() );
		} catch ( ApiException $exception ) {
			return [];
		}

		set_transient( $transient_key, $data, $this->get_product_cache_expiration() );

		return $data;
	}

	public function options() {
		if ( $this->is_headless() ) {
			$data = $this->get_product_options();
		} else {
			$data = json_decode( get_post_meta( $this->post_id(), self::OPTIONS_DATA_META_KEY, true ), true );
		}

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

		if ( empty( $this->post_id ) ) {
			return new \stdClass();
		}

		if ( $this->is_headless ) {
			$transient_key = sprintf( 'bigcommerce_gql_source%d', $this->post_id );
			$transient     = get_transient( $transient_key );

			if ( ! empty( $transient ) ) {
				$this->source_cache = $transient;

				return $transient;
			}

			// Addon is enabled
			if ( class_exists( 'BigCommerceReactTemplates\Plugin' ) ) {
				global $wp_query;

				if ( empty( $wp_query->query['name'] ) ) {
					return new \stdClass();
				}
				$slug      = $wp_query->query['name'];
				$container = bigcommerce()->container();
				$data      = $container[ GraphQL::GRAPHQL_REQUESTOR ]->request_product( $slug );
				$data      = $data->data->site->route->node;
			} else {
				$container  = bigcommerce()->container();
				$api        = $container[ Api::FACTORY ]->catalog();
				$product_id = get_post_meta( $this->post_id(), self::BIGCOMMERCE_ID, true );
				try {
					$product = $api->getProductById( $product_id, [
							'include' => [ 'variants', 'custom_fields', 'images', 'videos', 'bulk_pricing_rules', 'options', 'modifiers' ],
					] )->getData();

					$data = $this->json_encode_maybe_from_api( $product );
					$data = json_decode( $data );
				} catch ( ApiException $e ) {
					do_action( 'bigcommerce/import/log', $e->getMessage(), [
							'response' => $e->getResponseBody(),
							'headers'  => $e->getResponseHeaders(),
					] );
					do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

					return new \stdClass();
				}
			}

			set_transient( $transient_key, $data, $this->get_product_cache_expiration() );
		} else {
			$data = json_decode( get_post_meta( $this->post_id, self::SOURCE_DATA_META_KEY, true ) );
		}

		if ( empty( $data ) ) {
			return new \stdClass();
		}

		$this->source_cache = $data;

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
	 * @param string $size
	 *
	 * @return string|null
	 */
	public function get_headless_featured_image( $size = '80' ) {
		$source = $this->get_source_data();

		if ( empty( $source->images ) ) {
			return null;
		}
		$is_standard_img = get_option( Product_Single::HEADLESS_IMAGE_SIZE, Product_Single::SIZE_CDN_STD ) === Product_Single::SIZE_CDN_STD;

		if ( ! $is_standard_img ) {
			$thumb = array_reduce( $source->images, static function( $found, $item ) {
				return $item->is_thumbnail ? $item : $found;
			} );
		}

		if ( empty( $thumb ) ) {
			$thumb = $source->images[0];
		}

		$class = 'attachment-thumbnail size-thumbnail wp-post-image';
		$width = ! empty( $size ) ? sprintf( 'width="%s"', $size ) : '';

		return sprintf( '<img src="%s" class="%s" %s />', $is_standard_img ? $thumb->url_standard : $thumb->url_thumbnail, $class, $width );

	}

	public static function get_thumb_from_cdn( $post_ID, $format = 'html', $size = '80' ) {
		$data = get_post_meta( $post_ID, Product::GALLERY_META_KEY, true );

		$gallery = is_array( $data ) ? array_filter( array_map( 'intval', $data ) ) : [];

		if ( empty( $gallery ) ) {
			return null;
		}

		if ( $format === 'id' ) {
			return $gallery[0];
		}

		$thumb_url = get_post_meta( $gallery[0], Image_Importer::URL_THUMB, true );

		if ( empty( $thumb_url ) ) {
			return null;
		}

		$class = 'attachment-thumbnail size-thumbnail wp-post-image';
		$width = ! empty( $size ) ? sprintf( 'width="%s"', $size ) : '';

		return sprintf( '<img src="%s" class="%s" %s />', $thumb_url, $class, $width );
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
		if ( is_archive() && ! Channel::is_msf_channel_prop_on( Storefront_Processor::SHOW_PRODUCT_ADD_TO_CART_LINK ) ) {
			return apply_filters( 'bigcommerce/button/purchase', '', $this->post_id, '' );
		}

		$options  = $this->has_options() || $this->out_of_stock() ? 'disabled="disabled"' : '';
		$preorder = $this->availability() === Availability::PREORDER;
		$cart     = get_option( Cart::OPTION_ENABLE_CART, true );
		$class    = 'bc-btn bc-btn--form-submit';

		/**
		 * Filters purchase button attributes.
		 *
		 * @param array   $attributes Attributes.
		 * @param Product $product    Product.
		 */
		$attributes = apply_filters( 'bigcommerce/button/purchase/attributes', [], $this );
		$attributes = implode( ' ', array_map( function ( $attribute, $value ) {
			$attribute  = sanitize_title_with_dashes( $attribute );
			$value      = esc_attr( $value );

			return sprintf( '%s="%s"', $attribute, $value );
		}, array_keys( $attributes ), $attributes ) );

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
		$button = sprintf( '<button class="%s" type="submit" data-js="%d" %s %s>%s</button>', $class, $this->bc_id(), $options, $attributes, $label );

		/**
		 * Filters purchase button.
		 *
		 * @param string $button  Button html.
		 * @param int    $post_id Post id.
		 * @param string $label   Label.
		 */
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

	/**
	 * Get product variant SKU by variant ID. Returns empty string if variant ID is not provided or wrong
	 *
	 * @param int $variant_id
	 *
	 * @return string
	 */
	public function get_variant_sku( $variant_id = 0 ): string {
		if ( empty( $variant_id ) ) {
			return '';
		}

		$data = $this->get_source_data();
		foreach ( $data->variants as $variant ) {
			if ( $variant->id !== $variant_id ) {
				continue;
			}

			return $variant->sku;
		}

		return '';
	}

	/**
	 * Get selected variant id
	 * @return int
	 */
	public function get_selected_variant_id() {
		$variant_id = (int) filter_input( INPUT_GET, 'variant_id', FILTER_SANITIZE_NUMBER_INT );
		$sku        = filter_input( INPUT_GET, 'sku', FILTER_SANITIZE_STRING );
		$data       = $this->get_source_data();

		if ( empty( $data ) || empty( $data->variants ) ) {
			return 0;
		}

		$variants = $data->variants;

		if ( ! empty( $sku ) ) {
			$key = array_search( $sku, array_column( $variants, 'sku' ) );

			if ( $key !== false ) {
				return $variants[ $key ]->id;
			}
		}

		if ( ! empty( $variant_id ) ) {
			return $variant_id;
		}

		return 0;
	}

	public function get_inventory_level( $variant_id = 0 ) {
		$data = $this->get_source_data();

		if ( class_exists( 'BigCommerceReactTemplates\Plugin' ) || empty( $this->post_id ) ) {
			return -1;
		}

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
		if ( $this->is_headless() ) {
			return $this->get_property( 'availability' );
		}
		$terms               = get_the_terms( $this->post_id, Availability::NAME );
		$terms_empty_boolean = empty( $terms ) || is_bool( $terms );

		if ( $terms_empty_boolean || is_wp_error( $terms ) ) {
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
		$availability = $this->availability();
		if ( $availability === Availability::DISABLED ) {
			return false;
		}

		$source = $this->get_source_data();
		if ( $source->availability === Availability::PREORDER ) {
			return true;
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
	 * @param int $count The number of reviews to return. Will not return more
	 *                   than the number in the review cache.
	 *
	 * @return array The most recent reviews cached for the product
	 */
	public function get_reviews( $count = 12 ) {
		$cached = get_post_meta( $this->post_id, self::REVIEW_CACHE, true );
		if ( is_array( $cached ) ) {
			return array_slice( $cached, 0, $count );
		}
		return [];
	}

	/**
	 * Get the total number of reviews for the product
	 * @return int
	 */
	public function get_review_count() {
		if ( $this->is_headless() ) {
			return $this->get_reviews_count();
		}
		return (int) get_post_meta( $this->post_id, self::REVIEWS_APPROVED_META_KEY, true );
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
		if ( empty( $channels ) || is_bool( $channels ) ) {
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

		return self::by_product_meta( 'bigcommerce_id', absint( $product_id ), $channel, $query_args );
	}

	/**
	 * Gets a BigCommerce Product SKU and returns matching Product object
	 *
	 * @param string        $product_sku
	 *
	 * @param \WP_Term|null $channel
	 *
	 * @param array         $query_args
	 *
	 * @return Product|array
	 */
	public static function by_product_sku( $product_sku, \WP_Term $channel = null, $query_args = [] ) {

		if ( empty( $product_sku ) ) {
			throw new \InvalidArgumentException( __( 'Product SKU is missing', 'bigcommerce' ) );
		}

		return self::by_product_meta( 'bigcommerce_sku', sanitize_text_field( $product_sku ), $channel, $query_args );
	}

	/**
	 * Gets a BigCommerce Product by meta
	 *
	 * @param string        $meta_key
	 * @param mixed         $meta_value
	 *
	 * @param \WP_Term|null $channel
	 *
	 * @param array         $query_args
	 *
	 * @return Product|array
	 */
	private static function by_product_meta( $meta_key, $meta_value, \WP_Term $channel = null, $query_args = [] ) {

		$args = [
			'meta_query'     => [
				[
					'key'   => $meta_key,
					'value' => $meta_value,
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
			throw new Product_Not_Found_Exception( sprintf( __( 'No product found matching %s %s', 'bigcommerce' ), strtoupper( $meta_key ), $meta_value ) );
		}

		return new Product( $posts[0]->ID );
	}
}
