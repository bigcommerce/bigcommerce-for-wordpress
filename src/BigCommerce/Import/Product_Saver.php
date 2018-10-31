<?php

namespace BigCommerce\Import;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Api\v3\Model\ProductReview;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;

/**
 * Class Product_Saver
 *
 * Handles storing a product in the database
 */
abstract class Product_Saver implements Post_Import_Strategy {
	/**
	 * @var Model\Product
	 */
	protected $product;

	/**
	 * @var Model\Listing
	 */
	protected $listing;

	/**
	 * @var int
	 */
	protected $post_id;

	/**
	 * @var CatalogApi
	 */
	protected $catalog;

	/**
	 * Product_Saver constructor.
	 *
	 * @param Model\Product $product
	 * @param Model\Listing $listing
	 * @param CatalogApi    $catalog
	 * @param int           $post_id
	 */
	public function __construct( Model\Product $product, Model\Listing $listing, CatalogApi $catalog, $post_id = 0 ) {
		$this->product = $product;
		$this->listing = $listing;
		$this->catalog = $catalog;
		$this->post_id = $post_id;
	}

	/**
	 * Import the product into WordPress
	 *
	 * @return int The imported post ID
	 */
	public function do_import() {
		$builder = new Product_Builder( $this->product, $this->listing, $this->catalog );
		$this->save_wp_post( $builder );
		$this->save_wp_postmeta( $builder );

		$this->save_product_record( $builder );
		$this->save_product_variants( $builder );

		$product = new Product( $this->post_id );
		$product->update_source_data( $this->product );
		$product->update_listing_data( $this->listing );

		$this->save_modifiers( $product );
		$this->save_options( $product );
		$this->save_custom_fields( $product );

		$this->save_terms( $builder );
		$this->save_images( $builder );

		$this->save_reviews();

		$this->send_notifications();

		return $this->post_id;
	}

	/**
	 * Save the product as a WordPress post
	 *
	 * @param Product_Builder $builder
	 *
	 * @return void
	 */
	abstract protected function save_wp_post( Product_Builder $builder );

	/**
	 * Get the data that will be saved to the WordPress post
	 *
	 * @param Product_Builder $builder
	 *
	 * @return array
	 */
	protected function get_post_array( Product_Builder $builder ) {
		$postarr = $builder->build_post_array();
		if ( $this->post_id ) {
			$postarr[ 'ID' ] = $this->post_id;
		}

		return apply_filters( 'bigcommerce/import/product/post_array', $postarr );
	}

	/**
	 * Save post meta for the product
	 *
	 * @param Product_Builder $builder
	 *
	 * @return void
	 */
	protected function save_wp_postmeta( Product_Builder $builder ) {
		foreach ( $builder->build_post_meta() as $meta_key => $meta_value ) {
			update_post_meta( $this->post_id, $meta_key, $meta_value );
		}
		delete_post_meta( $this->post_id, Product::REQUIRES_REFRESH_META_KEY );
	}

	/**
	 * Save the product to the bc_products table
	 *
	 * @param Product_Builder $builder
	 *
	 * @return void
	 */
	abstract protected function save_product_record( Product_Builder $builder );

	/**
	 * Save product variants to the bc_variants table
	 *
	 * @param Product_Builder $builder
	 *
	 * @return void
	 */
	abstract protected function save_product_variants( Product_Builder $builder );

	/**
	 * Save product modifier information to the Product post
	 *
	 * @param Product $product
	 *
	 * @return void
	 */
	protected function save_modifiers( Product $product ) {
		try {
			$modifiers = $this->catalog->getModifiers( $this->product[ 'id' ] );
			$product->update_modifier_data( $modifiers->getData() );
		} catch ( ApiException $e ) {
			$product->update_modifier_data( [] );
		}
	}

	/**
	 * Save product option information to the Product post
	 *
	 * @param Product $product
	 *
	 * @return void
	 */
	protected function save_options( Product $product ) {
		try {
			$options = $this->catalog->getOptions( $this->product[ 'id' ] );
			$product->update_options_data( $options->getData() );
		} catch ( ApiException $e ) {
			$product->update_options_data( [] );
		}
	}

	/**
	 * Save custom fields to the Product post
	 *
	 * @param Product $product
	 *
	 * @return void
	 */
	protected function save_custom_fields( Product $product ) {
		$custom_fields = isset( $this->product[ 'custom_fields' ] ) ? (array) $this->product[ 'custom_fields' ] : [];
		$custom_fields = array_map( function ( $field ) {
			return [
				'name'  => $field[ 'name' ],
				'value' => $field[ 'value' ],
			];
		}, $custom_fields );
		$product->update_custom_field_data( $custom_fields );
	}

	/**
	 * Save taxonomy terms for the Product post
	 *
	 * @param Product_Builder $builder
	 *
	 * @return void
	 */
	protected function save_terms( Product_Builder $builder ) {
		$terms = $builder->build_taxonomy_terms();
		foreach ( [ Availability::NAME, Condition::NAME, Product_Type::NAME, Flag::NAME ] as $taxonomy ) {
			wp_set_object_terms( $this->post_id, $terms[ $taxonomy ], $taxonomy, false );
		}

		foreach ( [ Brand::NAME, Product_Category::NAME ] as $taxonomy ) {
			wp_set_object_terms( $this->post_id, array_map( 'intval', $terms[ $taxonomy ] ), $taxonomy, false );
		}
	}

	/**
	 * Save feature image and gallery for the Product post
	 *
	 * @param Product_Builder $builder
	 *
	 * @return void
	 */
	protected function save_images( Product_Builder $builder ) {
		$images = $builder->build_images( $this->post_id );
		if ( array_key_exists( 'thumbnail', $images ) ) {
			update_post_meta( $this->post_id, '_thumbnail_id', $images[ 'thumbnail' ] );
		} else {
			delete_post_meta( $this->post_id, '_thumbnail_id' );
		}
		if ( array_key_exists( 'gallery', $images ) ) {
			update_post_meta( $this->post_id, Product::GALLERY_META_KEY, $images[ 'gallery' ] );
		} else {
			delete_post_meta( $this->post_id, Product::GALLERY_META_KEY );
		}
	}

	/**
	 * Save product reviews for the product
	 *
	 *
	 * @return void
	 */
	protected function save_reviews() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$fetch   = new Review_Fetcher( $this->catalog, $this->product[ 'id' ] );
		$reviews = array_map( function ( ProductReview $review ) {
			$builder = new Review_Builder( $review );

			return $builder->build_review_array( $this->post_id, $this->product[ 'id' ] );
		}, $fetch->fetch() );

		$existing_reviews = array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT review_id FROM {$wpdb->bc_reviews} WHERE post_id=%d", $this->post_id ) ) );
		$valid_review_ids = array_map( 'intval', wp_list_pluck( $reviews, 'review_id' ) );

		$to_remove = array_diff( $existing_reviews, $valid_review_ids );

		foreach ( $to_remove as $review_id ) {
			$wpdb->delete( $wpdb->bc_reviews, [ 'review_id' => $review_id ], [ '%d' ] );
		}

		foreach ( $reviews as $review ) {
			if ( in_array( $review[ 'review_id' ], $existing_reviews ) ) {
				$wpdb->update( $wpdb->bc_reviews, $review, [ 'review_id' => $review[ 'review_id' ] ] );
			} else {
				$wpdb->insert( $wpdb->bc_reviews, $review );
			}
		}

	}

	/**
	 * Trigger actions to notify listeners that the import completed
	 *
	 * @return void
	 */
	protected function send_notifications() {
		/**
		 * A product has been saved by the import process
		 *
		 * @param int           $post_id The Post ID of the skipped product
		 * @param Model\Product $product The product data
		 * @param Model\Listing $listing The channel listing data
		 * @param CatalogApi    $catalog The Catalog API instance
		 */
		do_action( 'bigcommerce/import/product/saved', $this->post_id, $this->product, $this->listing, $this->catalog );
	}
}