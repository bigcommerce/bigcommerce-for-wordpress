<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\Api_Data_Sanitizer;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model;
use BigCommerce\Api\v3\Model\Modifier;
use BigCommerce\Api\v3\Model\ProductImage;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Import\Image_Importer;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Import\Mappers\Brand_Mapper;
use BigCommerce\Import\Mappers\Product_Category_Mapper;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;

/**
 * Responsible for constructing product-related data structures
 * during the import process for BigCommerce products.
 *
 * @package BigCommerce\Import\Importers\Products
 */
class Product_Builder {
	use Api_Data_Sanitizer;

    /**
     * @var Model\Product The product model being imported.
     */
    private $product;

    /**
     * @var Model\Listing The listing associated with the product.
     */
    private $listing;

    /**
     * @var CatalogApi The catalog API for BigCommerce.
     */
    private $catalog;

    /**
     * @var \WP_Term The channel term associated with the product.
     */
    private $channel_term;

    /**
     * Product_Builder constructor.
     *
     * @param Model\Product $product The product to import.
     * @param Model\Listing $listing The listing for the product.
     * @param \WP_Term $channel_term The term representing the channel.
     * @param CatalogApi $api The BigCommerce catalog API instance.
     */
	public function __construct( Model\Product $product, Model\Listing $listing, \WP_Term $channel_term, CatalogApi $api ) {
		$this->product = $product;
		$this->catalog = $api;
		$this->listing = $listing;
		$this->channel_term = $channel_term;
	}

    /**
     * Builds the post array for creating/updating the WordPress post.
     *
     * @return array The post data array.
     */
	public function build_post_array() {
		$created = $this->product[ 'date_created' ];
		$data    = [
			'post_type'    => Product::NAME,
			'post_title'   => $this->get_post_title(),
			'post_content' => $this->get_post_content(),
			'post_name'    => $this->get_post_slug(),
			'post_date'    => get_date_from_gmt( $created->format( 'Y-m-d H:i:s' ) ),
			'post_status'  => $this->get_post_status(),
			'menu_order'   => $this->get_menu_order(),
		];

		return $data;
	}

	private function get_post_title() {
		$title =  $this->listing->getName() ?: $this->product->getName();

		return $this->sanitize_title( $title );
	}

	private function sanitize_title( $title ) {
		return wp_strip_all_tags( $title );
	}

	private function get_post_content() {
		$content = $this->listing->getDescription() ?: $this->product->getDescription();

		return $this->sanitize_content( $content );
	}

	private function sanitize_content( $content ) {
		/**
		 * Remove contents of `script` and `style` tags.
		 * @see https://developer.wordpress.org/reference/functions/wp_strip_all_tags/
		 */
		$content = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $content );
		$content = html_entity_decode( $content );

		return wp_kses( $content, 'bigcommerce/product_description' );
	}

	private function get_post_slug() {
		$custom_url = $this->product->getCustomUrl();
		if ( $custom_url ) {
			$slug = trim( $custom_url->getUrl(), '/' );

			return sanitize_title( $slug );
		}

		return sanitize_title( $this->get_post_title() );
	}

	private function get_post_status() {
		try {
			$modifier_response = $this->catalog->getModifiers( $this->product[ 'id' ] );
			$modifiers         = $modifier_response->getData();
			if ( is_array( $modifiers ) ) {
				$unsupported = array_filter( $modifiers, function ( Modifier $modifier ) {
					return $modifier->getType() == 'file';
				} );
				if ( count( $unsupported ) > 0 ) {
					return 'draft';
				}
			}
		} catch ( ApiException $e ) {
			// presume that there are no modifiers
		}

		if ( ! $this->product->getIsVisible() ) {
			return 'draft';
		}

		$state = $this->listing->getState();
		switch ( $state ) {
			case 'active':
				return 'publish';
			case 'pending':
				return 'pending';
			case 'pending_delete':
				return 'trash';
			case 'deleted':
				return 'trash'; // we shouldn't ever reach this
			default:
				return 'draft';
		}
	}

	private function get_menu_order() {
		$sort_order = $this->sanitize_int( $this->product['sort_order'] );

		/**
		 * Filter the menu order assigned to the product, based on the
		 * source product's sort_order property.
		 *
		 * @param int           $menu_order The menu order to assign
		 * @param Model\Product $product    The source product
		 */
		return apply_filters( 'bigcommerce/import/product/menu_order', $sort_order, $this->product );
	}

    /**
     * Builds an array representing the product data.
     *
     * @return array The product data array.
     */
	public function build_product_array() {
		$data = [
			'bc_id'              => $this->sanitize_int( $this->product[ 'id' ] ),
			'is_featured'        => $this->sanitize_bool( $this->product[ 'is_featured' ] ),
			'sku'                => $this->sanitize_string( $this->product[ 'sku' ] ),
			'upc'                => $this->sanitize_string( $this->product[ 'upc' ] ),
			'mpn'                => $this->sanitize_string( $this->product[ 'mpn' ] ),
			'gtin'               => $this->sanitize_string( $this->product[ 'gtin' ] ),
			'weight'             => $this->sanitize_double( $this->product[ 'weight' ] ),
			'width'              => $this->sanitize_double( $this->product[ 'width' ] ),
			'depth'              => $this->sanitize_double( $this->product[ 'depth' ] ),
			'height'             => $this->sanitize_double( $this->product[ 'height' ] ),
			'price'              => $this->sanitize_double( $this->product[ 'price' ] ),
			'cost_price'         => $this->sanitize_double( $this->product[ 'cost_price' ] ),
			'retail_price'       => $this->sanitize_double( $this->product[ 'retail_price' ] ),
			'sale_price'         => $this->sanitize_double( $this->product[ 'sale_price' ] ),
			'calculated_price'   => $this->sanitize_double( $this->product[ 'calculated_price' ] ),
			'product_tax_code'   => $this->sanitize_string( $this->product[ 'product_tax_code' ] ),
			'inventory_level'    => $this->sanitize_int( $this->product[ 'inventory_level' ] ),
			'inventory_tracking' => $this->sanitize_string( $this->product[ 'inventory_tracking' ] ),
		];

		return $data;
	}

	/**
	 * Builds variants for the product using sanitized data.
	 *
	 * @return array The array of variant data.
	 */
	public function build_variants() {
		$bc_id = $this->product[ 'id' ];

		return array_map( function ( $data ) use ( $bc_id ) {
			$variant = [
				'variant_id'          => $this->sanitize_int( $data[ 'id' ] ),
				'bc_id'               => $this->sanitize_int( $bc_id ),
				'sku'                 => $this->sanitize_string( $data[ 'sku' ] ),
				'upc'                 => $this->sanitize_string( $data[ 'upc' ] ),
				'mpn'                 => $this->sanitize_string( $data[ 'mpn' ] ),
				'gtin'                => $this->sanitize_string( $data[ 'gtin' ] ),
				'weight'              => $this->sanitize_double( $data[ 'weight' ] ),
				'width'               => $this->sanitize_double( $data[ 'width' ] ),
				'depth'               => $this->sanitize_double( $data[ 'depth' ] ),
				'height'              => $this->sanitize_double( $data[ 'height' ] ),
				'price'               => $this->sanitize_double( $data[ 'price' ] ),
				'cost_price'          => $this->sanitize_double( $data[ 'cost_price' ] ),
				'calculated_price'    => $this->sanitize_double( $data[ 'calculated_price' ] ),
				'inventory_level'     => $this->sanitize_int( $data[ 'inventory_level' ] ),
				'purchasing_disabled' => $this->sanitize_bool( $data[ 'purchasing_disabled' ] ),
			];

			return $variant;
		}, (array) $this->product[ 'variants' ] );
	}

	/**
	 * Builds taxonomy terms for the product.
	 *
	 * @return array The taxonomy terms associated with the product.
	 */
	public function build_taxonomy_terms() {
		$terms = [];

		// term IDs
		$terms[ Product_Category::NAME ] = array_filter( $this->map_product_categories( $this->product[ 'categories' ] ) );
		$terms[ Brand::NAME ]            = array_filter( $this->map_brand( [ $this->product[ 'brand_id' ] ] ) );
		$terms[ Channel::NAME ]          = [ (int) $this->channel_term->term_id ];

		// strings
		$terms[ Product_Type::NAME ] = [ $this->product[ 'type' ] ];
		$terms[ Availability::NAME ] = [ $this->product[ 'availability' ] ];
		$terms[ Condition::NAME ]    = [ $this->product[ 'condition' ] ];
		$terms[ Flag::NAME ]         = $this->flags();

		return $terms;
	}

	/**
	 * Builds product images and associates them with a post parent.
	 *
	 * @param int $parent_id The post that will be set as the attachment parent.
	 * @return array The response containing thumbnails, galleries, and variant image mappings.
	 */
	public function build_images( $parent_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$response = [
			'thumbnail' => 0,
			'gallery'   => [],
			'variants'  => [],
		];

		/**
		 * Filters whether to import product images or not.
		 *
		 * @param bool $import_images True or false.
		 */
		if ( ! apply_filters( 'bigcommerce/import/product/import_images', true ) ) {
			return $response;
		}

		$images = $this->product[ 'images' ];

		usort( $images, function ( $a, $b ) {
			if ( $a[ 'sort_order' ] == $b[ 'sort_order' ] ) {
				return 0;
			}

			return ( $a[ 'sort_order' ] < $b[ 'sort_order' ] ) ? - 1 : 1;
		} );
		foreach ( $images as $image ) {
			/** @var ProductImage $image */

			// find an existing image
			$existing = get_posts( [
				'post_type'      => 'attachment',
				'meta_query'     => [
					[
						'key'     => 'bigcommerce_id',
						'value'   => $image[ 'id' ],
						'compare' => '=',
					],
				],
				'fields'         => 'ids',
				'posts_per_page' => 1,
			] );
			if ( ! empty( $existing ) ) {
				$post_id = reset( $existing );
			} else {
				$importer = new Image_Importer( $image[ Image_Importer::URL_ZOOM ], $parent_id );
				$post_id  = $importer->import();
			}
			if ( ! empty( $post_id ) ) {
				update_post_meta( $post_id, 'bigcommerce_id', $image[ 'id' ] );
				foreach ( [ Image_Importer::URL_ZOOM, Image_Importer::URL_STD, Image_Importer::URL_THUMB, Image_Importer::URL_TINY ] as $key ) {
					$derivative_url = $image[ $key ];
					if ( $derivative_url ) {
						update_post_meta( $post_id, $key, $derivative_url );
					}
				}
				if ( ! empty( $image['description'] ) ) {
					update_post_meta( $post_id, '_wp_attachment_image_alt', $image['description'] );
				}
				$response[ 'gallery' ][] = $post_id;
				if ( $image[ 'is_thumbnail' ] ) {
					$response[ 'thumbnail' ] = $post_id;
				}
			}
		}

		$variants = $this->product->getVariants();
		foreach ( $variants as $var ) {
			$image_url = $var->getImageUrl();
			if ( $image_url ) {
				$existing = $wpdb->get_var( $wpdb->prepare(
					"SELECT p.ID FROM {$wpdb->posts} p
					 INNER JOIN {$wpdb->postmeta} m ON p.ID=m.post_id
					 WHERE p.post_type='attachment'
					   AND m.meta_key IN ( 'bigcommerce_source_url', 'url_zoom', 'url_standard', 'url_thumbnail', 'url_tiny' )
					   AND m.meta_value=%s ORDER BY p.ID ASC LIMIT 1",
					$image_url
				) );
				if ( ! empty( $existing ) ) {
					$post_id = (int) $existing;
				} else {
					$importer = new Image_Importer( $image_url, $parent_id );
					$post_id  = $importer->import();
				}
				if ( ! empty( $post_id ) ) {
					$response['variants'][ $var->getId() ] = $post_id;
				}
			}
		}

		return $response;
	}

	private function map_product_categories( array $bc_category_ids ) {
		$mapper = new Product_Category_Mapper();

		return array_map( [ $mapper, 'map' ], $bc_category_ids );
	}

	private function map_brand( array $bc_brand_ids ) {
		$mapper = new Brand_Mapper();

		return array_map( [ $mapper, 'map' ], $bc_brand_ids );
	}

	private function flags() {
		$flags = [];
		if ( $this->sanitize_bool( $this->product[ 'is_visible' ] ) ) {
			$flags[] = Flag::VISIBLE;
		}
		if ( $this->sanitize_bool( $this->product[ 'is_featured' ] ) ) {
			$flags[] = Flag::FEATURED;
		}
		if ( $this->sanitize_bool( $this->product[ 'is_free_shipping' ] ) ) {
			$flags[] = Flag::FREE_SHIPPING;
		}
		if ( $this->sanitize_bool( $this->product[ 'is_condition_shown' ] ) ) {
			$flags[] = Flag::SHOW_CONDITION;
		}
		if ( $this->sanitize_bool( $this->product[ 'is_preorder_only' ] ) ) {
			$flags[] = Flag::PREORDER;
		}
		if ( $this->sanitize_bool( $this->product[ 'is_price_hidden' ] ) ) {
			$flags[] = Flag::HIDE_PRICE;
		}
		if ( $this->sanitize_double( $this->product[ 'sale_price' ] ) ) {
			$flags[] = Flag::SALE;
		}

		$flags = array_merge( $flags, $this->inventory_flags() );

		return $flags;
	}

	/**
	 * @return array Flags related to inventory calculations
	 */
	private function inventory_flags() {
		$tracking = $this->product[ 'inventory_tracking' ];
		if ( $tracking == 'none' ) {
			return [];
		}
		if ( $tracking == 'product' ) {
			$warning_level   = $this->sanitize_int( $this->product[ 'inventory_warning_level' ] );
			$inventory_level = $this->sanitize_int( $this->product[ 'inventory_level' ] );
			if ( $inventory_level < 1 ) {
				return [ Flag::OUT_OF_STOCK ];
			}
			if ( $inventory_level <= $warning_level ) {
				return [ Flag::LOW_INVENTORY ];
			}

			return [];
		}
		if ( $tracking == 'variant' ) {
			return []; // might revisit later
		}

		return [];
	}

	/**
	 * Builds and returns an array of metadata for a product post.
	 *
	 * This function generates metadata for a product by extracting and processing
	 * various attributes such as ID, SKU, ratings, inventory, pricing, and more.
	 * The metadata can be used for storing additional information in the database.
	 *
	 * @return array Associative array containing product metadata with keys:
	 *               - `Product::IMPORTER_VERSION_META_KEY` (string): Importer version.
	 *               - `Product::BIGCOMMERCE_ID` (int): BigCommerce product ID.
	 *               - `Product::SKU` (string): Product SKU.
	 *               - `Product::SKU_NORMALIZED` (string): Normalized product SKU.
	 *               - `Product::RATING_META_KEY` (float): Average product rating.
	 *               - `Product::REVIEW_COUNT_META_KEY` (int): Number of product reviews.
	 *               - `Product::RATING_SUM_META_KEY` (int): Sum of product ratings.
	 *               - `Product::SALES_META_KEY` (int): Total product sales.
	 *               - `Product::PRICE_META_KEY` (float): Product calculated price.
	 *               - `Product::INVENTORY_META_KEY` (int): Inventory level.
	 *               - `Product::PRICE_RANGE_META_KEY` (array): Price range information.
	 *               - `Product::DATA_HASH_META_KEY` (string): Hash of product and listing data.
	 */
	public function build_post_meta() {
		$meta = [];

		$meta[ Product::IMPORTER_VERSION_META_KEY ] = Import_Strategy::VERSION;
		$meta[ Product::BIGCOMMERCE_ID ]            = $this->sanitize_int( $this->product['id'] );
		$meta[ Product::SKU ]                       = $this->sanitize_string( $this->product['sku'] );
		$meta[ Product::SKU_NORMALIZED ]            = $this->normalize_sku( $this->product['sku'] );
		$meta[ Product::RATING_META_KEY ]           = $this->get_avg_rating();
		$meta[ Product::REVIEW_COUNT_META_KEY ]     = $this->sanitize_int( $this->product[ 'reviews_rating_sum' ] );
		$meta[ Product::RATING_SUM_META_KEY ]       = $this->sanitize_int( $this->product[ 'reviews_count' ] );
		$meta[ Product::SALES_META_KEY ]            = $this->sanitize_int( $this->product['total_sold'] );
		$meta[ Product::PRICE_META_KEY ]            = $this->sanitize_double( $this->product['calculated_price'] );
		$meta[ Product::INVENTORY_META_KEY ]        = $this->sanitize_int( $this->product['inventory_level'] );
		$meta[ Product::PRICE_RANGE_META_KEY ]      = $this->calculate_price_ranges();
		$meta[ Product::DATA_HASH_META_KEY ]        = self::hash( $this->product, $this->listing );

		return $meta;
	}

	private function normalize_sku( $sku ) {
		/**
		 * Filters normalized SKU segment's no of chars.
		 *
		 * @param int $num_of_characters No of chars.
		 */
		$num_of_characters = apply_filters( 'bigcommerce/sku/normalized/segment/num_of_characters', 10 );
		if ( empty( $sku ) ) {
			/**
			 * Filters normalized empty SKU.
			 *
			 * @param string $repeated_string empty sku.
			 */
			return apply_filters( 'bigcommerce/sku/normalized/empty', str_repeat( 'z', $num_of_characters ) );
		}

		/**
		 * Filters SKU normalized.
		 *
		 * @param array  $normalized_sku_segments SKU segments.
		 * @param string $sku                     SKU.
		 */
		return apply_filters( 'bigcommerce/sku/normalized', $this->normalize_sku_segments( $this->segment_sku( $sku ), $num_of_characters ), $sku );
	}

	/**
	 * Segment SKU by type (num, alpha)
	 *
	 * @param string $sku
	 * @return array
	 */
	private function segment_sku( $sku ) {
		$sku = preg_replace( "/[^A-Za-z0-9 ]/", '', $sku );

		// Group characters by type
		$previous_type = 'alpha';
		$segments = [[]];
		foreach ( str_split( $sku ) as $char ) {
			end( $segments );
			$key = key( $segments );

			$current_type = 'num';

			if ( ctype_alpha( $char ) ) {
				$current_type = 'alpha';
			}

			if ( $current_type !== $previous_type ) {
				$key++;
				$segments[ $key ] = [];
			}
			$segments[ $key ][] = $char;

			$previous_type = $current_type;
		}

		// Flatten segments
		return array_filter( array_map( function( $segment ) {
			return implode( '', $segment );
		}, $segments ) );
	}

	/**
	 * Pad sku segments with 0
	 *
	 * Numbers are padded left, alpha right
	 * 12345 -> 00000123245
	 * aabcc -> aabcc00000
	 * 12345aabcc -> 00000123245-aabcc00000
	 *
	 * @param array $segments
	 * @param int $num_of_characters
	 * @return string
	 */
	private function normalize_sku_segments( $segments, $num_of_characters ) {
		$segments = array_map( function( $segment ) use ( $num_of_characters ) {
			$pad_type = STR_PAD_LEFT;
			if ( ctype_alpha( $segment[0] ) ) {
				$pad_type = STR_PAD_RIGHT;
			}
			return str_pad( $segment, $num_of_characters, '0', $pad_type );
		}, $segments );

		return implode( '-', $segments );
	}

	private function get_avg_rating() {
		$sum   = $this->sanitize_int( $this->product[ 'reviews_rating_sum' ] );
		$count = $this->sanitize_int( $this->product[ 'reviews_count' ] );

		if ( $count < 1 ) {
			return 0;
		}

		return $sum / $count;
	}

	private function calculate_price_ranges() {
		$variants = array_map( function ( $data ) {
			$variant = [
				'price'            => $this->sanitize_double( $data['price'] ),
				'calculated_price' => $this->sanitize_double( $data['calculated_price'] ),
			];

			return $variant;
		}, (array) $this->product['variants'] );

		$prices   = wp_list_pluck( $variants, 'price' );
		$prices[] = $this->product['price'];
		$prices   = array_filter( $prices );

		$calculated   = wp_list_pluck( $variants, 'calculated_price' );
		$calculated[] = $this->product['calculated_price'];
		$calculated   = array_filter( $calculated );

		$ranges = [
			'price'      => [
				'max' => ! empty( $prices ) ? max( $prices ) : 0,
				'min' => ! empty( $prices ) ? min( $prices ) : 0,
			],
			'calculated' => [
				'max' => ! empty( $calculated ) ? max( $calculated ) : 0,
				'min' => ! empty( $calculated ) ? min( $calculated ) : 0,
			],
		];

		return $ranges;
	}

	/**
	 * Generates a hash for a product and listing.
	 *
	 * This function serializes and combines the provided product and listing data,
	 * then computes an MD5 hash. It is used to create a unique identifier
	 * representing the state of the product and listing.
	 *
	 * @param Model\Product $product The product model to hash.
	 * @param Model\Listing $listing The listing model to hash.
	 * 
	 * @return string MD5 hash of the combined product and listing data.
	 */
	public static function hash( Model\Product $product, Model\Listing $listing ) {
		$product_string = wp_json_encode( ObjectSerializer::sanitizeForSerialization( $product ) );
		$listing_string = wp_json_encode( ObjectSerializer::sanitizeForSerialization( $listing ) );
		return md5( $product_string . $listing_string );
	}
}
