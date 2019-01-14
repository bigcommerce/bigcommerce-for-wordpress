<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Import\Importers\Products\Product_Creator;
use BigCommerce\Import\Importers\Products\Product_Ignorer;
use BigCommerce\Import\Importers\Products\Product_Updater;
use BigCommerce\Post_Types\Product\Product;

class Product_Strategy_Factory {
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
	 * @var string
	 */
	private $version;

	/**
	 * Product_Strategy_Factory constructor.
	 *
	 * @param Model\Product $product
	 * @param Model\Listing $listing
	 * @param CatalogApi    $catalog
	 * @param string        $version
	 */
	public function __construct( Model\Product $product, Model\Listing $listing, CatalogApi $catalog, $version ) {
		$this->product = $product;
		$this->listing = $listing;
		$this->catalog = $catalog;
		$this->version = $version;
	}

	/**
	 * @return Import_Strategy
	 */
	public function get_strategy() {
		$matching_post_id = $this->get_matching_post();
		if ( empty( $matching_post_id ) ) {
			return new Product_Creator( $this->product, $this->listing, $this->catalog );
		}

		if ( ! $this->needs_refresh( $matching_post_id ) ) {
			return new Product_Ignorer( $this->product, $this->listing, $this->catalog, $matching_post_id );
		}

		return new Product_Updater ( $this->product, $this->listing, $this->catalog, $matching_post_id );

	}

	private function get_matching_post() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$sql = "SELECT post_id FROM {$wpdb->bc_products} WHERE bc_id=%d";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $this->product[ 'id' ] ) );
	}

	private function needs_refresh( $post_id ) {
		if ( get_post_meta( $post_id, Product::REQUIRES_REFRESH_META_KEY, true ) ) {
			$response = true;
		} elseif ( get_post_meta( $post_id, Product::IMPORTER_VERSION_META_KEY, true ) != $this->version ) {
			$response = true;
		} else {

			$product    = new Product( $post_id );
			$serializer = new ObjectSerializer();

			$product_changed = $product->get_source_data() != $serializer->sanitizeForSerialization( $this->product );
			$listing_changed = $product->get_listing_data() != $serializer->sanitizeForSerialization( $this->listing );
			$response        = ( $product_changed || $listing_changed );
		}

		/**
		 * Filter whether the product should be refreshed
		 *
		 * @param bool          $response Whether the product should be refreshed
		 * @param int           $post_id  The ID of the product post
		 * @param Model\Product $product  The product data from the API
		 * @param Model\Listing $listing  The channel listing data from the API
		 * @param string        $version  The version of the importer
		 */
		return apply_filters( 'bigcommerce/import/strategy/needs_refresh', $response, $post_id, $this->product, $this->listing, $this->version );
	}
}