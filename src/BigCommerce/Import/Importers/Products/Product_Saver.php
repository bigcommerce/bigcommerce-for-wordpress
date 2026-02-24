<?php

namespace BigCommerce\Import\Importers\Products;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Import\Image_Importer;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;

/**
 * Handles storing a product in the database, including saving product details,
 * taxonomy terms, images, and sending notifications after the product has been imported.
 */
abstract class Product_Saver implements Import_Strategy {
    /**
     * @var Model\Product The product object containing data from the BigCommerce API.
     */
    protected $product;

    /**
     * @var Model\Listing The listing object containing data from the BigCommerce Channel API.
     */
    protected $listing;

    /**
     * @var int The WordPress post ID for the imported product.
     */
    protected $post_id;

    /**
     * @var CatalogApi Instance of the BigCommerce Catalog API.
     */
    protected $catalog;

    /**
     * @var \WP_Term The WordPress term representing the channel associated with the product.
     */
    private $channel_term;

    /**
     * Product_Saver constructor.
     *
     * @param Model\Product $product Product data from the BigCommerce API.
     * @param Model\Listing $listing Listing data from the BigCommerce Channel API.
     * @param \WP_Term      $channel_term The WordPress term representing the channel.
     * @param CatalogApi    $catalog Instance of the BigCommerce Catalog API.
     * @param int           $post_id The WordPress post ID for the imported product.
     */
    public function __construct( Model\Product $product, Model\Listing $listing, \WP_Term $channel_term, CatalogApi $catalog, $post_id = 0 ) {
        $this->product      = $product;
        $this->listing      = $listing;
        $this->catalog      = $catalog;
        $this->post_id      = $post_id;
        $this->channel_term = $channel_term;
    }

    /**
     * Imports the product into WordPress by saving the product data, terms, post meta, 
     * and images. Also sends notifications once the import is complete.
     *
     * @return int The post ID of the imported product.
     */
    public function do_import() {
        $builder = new Product_Builder( $this->product, $this->listing, $this->channel_term, $this->catalog );

		$this->save_terms( $builder );
		$this->save_wp_postmeta( $builder );
		$this->save_wp_post( $builder );

		$product = new Product( $this->post_id );
		$product->update_source_data( $this->product );
		$product->update_listing_data( $this->listing );

		$this->save_modifiers( $product );
		$this->save_options( $product );
		$this->save_custom_fields( $product );
		$this->save_images( $builder );

		$this->send_notifications();

        return $this->post_id;
    }

    /**
     * Saves the WordPress post data for the imported product.
     *
     * @param Product_Builder $builder The product builder instance used to create post data.
     *
     * @return void
     */
    protected function save_wp_post( Product_Builder $builder ) {
		$postarr = $this->get_post_array( $builder );
		kses_remove_filters();
		wp_update_post( $postarr );
		kses_init();
    }

    /**
     * Builds and retrieves the post data array for the product.
     *
     * @param Product_Builder $builder The product builder instance used to create post data.
     *
     * @return array The post array to be saved in WordPress.
     */
    protected function get_post_array( Product_Builder $builder ) {
		$postarr = $builder->build_post_array();
		if ( $this->post_id ) {
			$postarr['ID']        = $this->post_id;
			$postarr['edit_date'] = true;
        }

        /**
         * Filters the product post array before saving.
         *
         * @param array $postarr The post data array.
         */
        return apply_filters( 'bigcommerce/import/product/post_array', $postarr );
    }

    /**
     * Saves the product post meta data.
     *
     * @param Product_Builder $builder The product builder instance used to create post meta.
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
     * Saves product modifier information to the WordPress post.
     *
     * @param Product $product The product instance.
     *
     * @return void
     */
    protected function save_modifiers( Product $product ) {
        $product->update_modifier_data( $this->product->getModifiers() ?: [] );
    }

    /**
     * Saves product option information to the WordPress post.
     *
     * @param Product $product The product instance.
     *
     * @return void
     */
    protected function save_options( Product $product ) {
        $product->update_options_data( $this->product->getOptions() ?: [] );
    }

    /**
     * Saves custom fields for the product to the WordPress post.
     *
     * @param Product $product The product instance.
     *
     * @return void
     */
    protected function save_custom_fields( Product $product ) {
        $custom_fields = isset( $this->product['custom_fields'] ) ? (array) $this->product['custom_fields'] : [];
        $custom_fields = array_map( function ( $field ) {
            return [
                'name'  => $field['name'],
                'value' => $field['value'],
            ];
        }, $custom_fields );
        $product->update_custom_field_data( $custom_fields );
    }

    /**
     * Saves taxonomy terms associated with the product to WordPress.
     *
     * @param Product_Builder $builder The product builder instance used to create terms.
     *
     * @return void
     */
    protected function save_terms( Product_Builder $builder ) {
		$terms = $builder->build_taxonomy_terms();
		foreach ( [ Availability::NAME, Condition::NAME, Product_Type::NAME, Flag::NAME ] as $taxonomy ) {
			wp_set_object_terms( $this->post_id, $terms[ $taxonomy ], $taxonomy, false );
		}

		foreach ( [ Brand::NAME, Product_Category::NAME, Channel::NAME ] as $taxonomy ) {
			wp_set_object_terms( $this->post_id, array_map( 'intval', $terms[ $taxonomy ] ), $taxonomy, false );
		}
    }

    /**
     * Saves feature image and gallery images for the product.
     *
     * @param Product_Builder $builder The product builder instance used to create images.
     *
     * @return void
     */
    protected function save_images( Product_Builder $builder ) {
		$images            = $builder->build_images( $this->post_id );
		$is_local_featured = Image_Importer::has_local_featured_image( $this->post_id );

		if ( array_key_exists( 'thumbnail', $images ) && ! $is_local_featured ) {
			update_post_meta( $this->post_id, '_thumbnail_id', $images['thumbnail'] );
		} elseif ( ! $is_local_featured ) {
			delete_post_meta( $this->post_id, '_thumbnail_id' );
		}

		if ( array_key_exists( 'gallery', $images ) ) {
			update_post_meta( $this->post_id, Product::GALLERY_META_KEY, $images['gallery'] );
		} else {
			delete_post_meta( $this->post_id, Product::GALLERY_META_KEY );
		}
		if ( array_key_exists( 'variants', $images ) ) {
			update_post_meta( $this->post_id, Product::VARIANT_IMAGES_META_KEY, $images['variants'] );
		} else {
			delete_post_meta( $this->post_id, Product::VARIANT_IMAGES_META_KEY );
        }
    }

    /**
     * Sends notifications after the product import has been completed.
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
