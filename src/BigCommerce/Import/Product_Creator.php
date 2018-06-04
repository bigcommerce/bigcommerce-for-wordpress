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

class Product_Creator implements Post_Import_Strategy {
	private $data;
	private $api;

	public function __construct( $data, CatalogApi $api ) {
		$this->data = $data;
		$this->api  = $api;
	}

	public function do_import() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$builder = new Product_Builder( $this->data, $this->api );
		$postarr = apply_filters( 'bigcommerce/import/product/post_array', $builder->build_post_array() );
		$post_id = wp_insert_post( $postarr );

		$product_array              = $builder->build_product_array();
		$product_array[ 'post_id' ] = $post_id;

		// avoid errors from stray data that may have been left by careless DB admins (e.g., manually deleted posts)
		$wpdb->delete( $wpdb->bc_products, [ 'bc_id' => $this->data[ 'id' ] ], [ '%d' ] );
		$wpdb->delete( $wpdb->bc_variants, [ 'bc_id' => $this->data[ 'id' ] ], [ '%d' ] );

		$wpdb->insert( $wpdb->bc_products, $product_array );

		foreach ( $builder->build_post_meta() as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

		$variants = $builder->build_variants();
		foreach ( $variants as $variant ) {
			$wpdb->insert( $wpdb->bc_variants, $variant );
		}

		$product = new Product( $post_id );
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
			wp_set_object_terms( $post_id, $terms[ $taxonomy ], $taxonomy, false );
		}

		foreach ( [ Brand::NAME, Product_Category::NAME ] as $taxonomy ) {
			wp_set_object_terms( $post_id, array_map( 'intval', $terms[ $taxonomy ] ), $taxonomy, false );
		}

		$images = $builder->build_images( $post_id );
		if ( array_key_exists( 'thumbnail', $images ) ) {
			update_post_meta( $post_id, '_thumbnail_id', $images[ 'thumbnail' ] );
		}
		if ( array_key_exists( 'gallery', $images ) ) {
			update_post_meta( $post_id, Product::GALLERY_META_KEY, $images[ 'gallery' ] );
		}

		do_action( 'bigcommerce/import/product/created', $post_id, $this->data, $this->api );

		return $post_id;
	}
}