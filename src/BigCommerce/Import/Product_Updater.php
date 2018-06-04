<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\CatalogApi;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Availability\Availability;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Condition\Condition;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;
use BigCommerce\Taxonomies\Product_Type\Product_Type;

class Product_Updater implements Post_Import_Strategy {
	private $data;
	private $post_id;
	private $api;

	public function __construct( $data, CatalogApi $api, $post_id ) {
		$this->data    = $data;
		$this->post_id = $post_id;
		$this->api     = $api;
	}

	public function do_import() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$builder         = new Product_Builder( $this->data, $this->api );
		$postarr         = $builder->build_post_array();
		$postarr[ 'ID' ] = $this->post_id;
		$postarr = apply_filters( 'bigcommerce/import/product/post_array', $postarr );
		wp_update_post( $postarr );


		foreach ( $builder->build_post_meta() as $meta_key => $meta_value ) {
			update_post_meta( $this->post_id, $meta_key, $meta_value );
		}
		delete_post_meta( $this->post_id, Product::REQUIRES_REFRESH_META_KEY );

		$product_array = $builder->build_product_array();
		$wpdb->update( $wpdb->bc_products, $product_array, [ 'post_id' => $this->post_id ] );

		$variants    = $builder->build_variants();
		$variant_ids = array_column( $variants, 'variant_id' );

		$existing_variants = $wpdb->get_col( $wpdb->prepare( "SELECT variant_id FROM {$wpdb->bc_variants} WHERE bc_id=%d", $product_array[ 'bc_id' ] ) );
		$to_remove         = array_diff( $existing_variants, $variant_ids );
		foreach ( $to_remove as $variant_id ) {
			$wpdb->delete( $wpdb->bc_variants, [ 'variant_id' => $variant_id ], [ '%d' ] );
		}

		foreach ( $variants as $variant ) {
			$variant_id = $variant[ 'variant_id' ];
			if ( in_array( $variant_id, $existing_variants ) ) {
				$wpdb->update( $wpdb->bc_variants, $variant, [ 'variant_id' => $variant_id ] );
			} else {
				$wpdb->insert( $wpdb->bc_variants, $variant );
			}
		}

		$product = new Product( $this->post_id );
		$product->update_source_data( $this->data );

		try {
			$modifiers = $this->api->getModifiers( $this->data[ 'id' ] );
			$product->update_modifier_data( $modifiers->getData() );
		} catch ( ApiException $e ) {
			$product->update_modifier_data( [] );
		}
		try {
			$options = $this->api->getOptions( $this->data[ 'id' ] );
			$product->update_options_data( $options->getData() );
		} catch ( ApiException $e ) {
			$product->update_options_data( [] );
		}

		$terms = $builder->build_taxonomy_terms();
		foreach ( [ Availability::NAME, Condition::NAME, Product_Type::NAME, Flag::NAME ] as $taxonomy ) {
			wp_set_object_terms( $this->post_id, $terms[ $taxonomy ], $taxonomy, false );
		}

		foreach ( [ Brand::NAME, Product_Category::NAME ] as $taxonomy ) {
			wp_set_object_terms( $this->post_id, array_map( 'intval', $terms[ $taxonomy ] ), $taxonomy, false );
		}

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

		do_action( 'bigcommerce/import/product/updated', $this->post_id, $this->data, $this->api );

		return $this->post_id;
	}
}