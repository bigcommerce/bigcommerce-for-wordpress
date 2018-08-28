<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Post_Types\Product\Product;

class Import_Strategy_Factory {
	private $data;
	private $api;
	private $version;

	public function __construct( \BigCommerce\Api\v3\Model\Product $data, CatalogApi $api, $version ) {
		$this->data    = $data;
		$this->api     = $api;
		$this->version = $version;
	}

	public function get_strategy() {
		$matching_post_id = $this->get_matching_post();
		if ( empty( $matching_post_id ) ) {
			return new Product_Creator( $this->data, $this->api );
		}

		if ( ! $this->needs_refresh( $matching_post_id ) ) {
			return new Product_Ignorer( $this->data, $this->api, $matching_post_id );
		}

		return new Product_Updater( $this->data, $this->api, $matching_post_id );

	}

	private function get_matching_post() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$sql = "SELECT post_id FROM {$wpdb->bc_products} WHERE bc_id=%d";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $this->data[ 'id' ] ) );
	}

	private function needs_refresh( $post_id ) {
		if ( get_post_meta( $post_id, Product::REQUIRES_REFRESH_META_KEY, true ) ) {
			$response = true;
		} elseif ( get_post_meta( $post_id, Product::IMPORTER_VERSION_META_KEY, true ) != $this->version ) {
			$response = true;
		} else {

			$product    = new Product( $post_id );
			$serializer = new ObjectSerializer();

			$response = $product->get_source_data() != $serializer->sanitizeForSerialization( $this->data );
		}
		return apply_filters( 'bigcommerce/import/strategy/needs_refresh', $response, $post_id, $this->data, $this->version );
	}
}