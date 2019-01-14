<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Api\v3\Model\Modifier;
use BigCommerce\Api\v3\Model\ProductImage;
use BigCommerce\Import\Image_Importer;
use BigCommerce\Import\Mappers\Brand_Mapper;
use BigCommerce\Import\Mappers\Product_Category_Mapper;
use BigCommerce\Import\Record_Builder;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;

class Product_Builder extends Record_Builder {
	/**
	 * @var Model\Product
	 */
	private $product;
	/**
	 * @var Model\Listing
	 */
	private $listing;
	/**
	 * @var CatalogApi
	 */
	private $catalog;

	/**
	 * Product_Builder constructor.
	 *
	 * @param Model\Product $product
	 * @param Model\Listing $listing
	 * @param CatalogApi    $api
	 */
	public function __construct( Model\Product $product, Model\Listing $listing, CatalogApi $api ) {
		$this->product = $product;
		$this->catalog = $api;
		$this->listing = $listing;
	}

	public function build_post_array() {
		$created = $this->product[ 'date_created' ];
		$data    = [
			'post_type'    => Product::NAME,
			'post_title'   => $this->get_post_title(),
			'post_content' => $this->get_post_content(),
			'post_name'    => $this->get_post_slug(),
			'post_date'    => get_date_from_gmt( $created->format( 'Y-m-d H:i:s' ) ),
			'post_status'  => $this->get_post_status(),
		];

		return $data;
	}

	private function get_post_title() {
		$title = $this->listing->getName() ?: $this->product->getName();
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
		return wp_kses_post( $content );
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
				$unsupported = array_filter( $modifiers, function( Modifier $modifier ) {
					return $modifier->getType() == 'file';
				});
				if ( count( $unsupported ) > 0 ) {
					return 'draft';
				}
			}
		} catch ( ApiException $e ) {
			// presume that there are no modifiers
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

	public function build_taxonomy_terms() {
		$terms = [];

		// term IDs
		$terms[ Product_Category::NAME ] = array_filter( $this->map_product_categories( $this->product[ 'categories' ] ) );
		$terms[ Brand::NAME ]            = array_filter( $this->map_brand( [ $this->product[ 'brand_id' ] ] ) );

		// strings
		$terms[ Product_Type::NAME ] = [ $this->product[ 'type' ] ];
		$terms[ Availability::NAME ] = [ $this->product[ 'availability' ] ];
		$terms[ Condition::NAME ]    = [ $this->product[ 'condition' ] ];
		$terms[ Flag::NAME ]         = $this->flags();

		return $terms;
	}

	/**
	 * @param int $parent_id The post that will be set as the attachment parent
	 *
	 * @return array
	 */
	public function build_images( $parent_id ) {
		$response = [
			'thumbnail' => 0,
			'gallery'   => [],
		];

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
				$importer = new Image_Importer( $image[ 'url_zoom' ], $parent_id );
				$post_id  = $importer->import();
			}
			if ( ! empty( $post_id ) ) {
				update_post_meta( $post_id, 'bigcommerce_id', $image[ 'id' ] );
				$response[ 'gallery' ][] = $post_id;
				if ( $image[ 'is_thumbnail' ] ) {
					$response[ 'thumbnail' ] = $post_id;
				}
			}
		}

		return $response;
	}

	private function map_product_categories( array $bc_category_ids ) {
		$mapper = new Product_Category_Mapper( $this->catalog );

		return array_map( [ $mapper, 'map' ], $bc_category_ids );
	}

	private function map_brand( array $bc_brand_ids ) {
		$mapper = new Brand_Mapper( $this->catalog );

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

	public function build_post_meta() {
		$meta = [];

		$meta[ Product::IMPORTER_VERSION_META_KEY ] = Import_Strategy::VERSION;
		$meta[ 'bigcommerce_id' ]                   = $this->product[ 'id' ];
		$meta[ Product::RATING_META_KEY ]           = $this->get_avg_rating();
		$meta[ Product::SALES_META_KEY ]            = $this->sanitize_int( $this->product[ 'total_sold' ] );
		$meta[ Product::PRICE_META_KEY ]            = $this->sanitize_double( $this->product[ 'calculated_price' ] );

		return $meta;
	}

	private function get_avg_rating() {
		$sum   = $this->sanitize_int( $this->product[ 'reviews_rating_sum' ] );
		$count = $this->sanitize_int( $this->product[ 'reviews_count' ] );

		if ( $count < 1 ) {
			return 0;
		}

		return $sum / $count;
	}
}