<?php

namespace BigCommerce\Import\Importers\Products;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Import\Importers\Products\Product_Builder;

class Product_Creator extends Product_Saver {
	public function do_import() {
		$this->create_default_post();
		return parent::do_import();
	}

	private function create_default_post() {
		$this->post_id = wp_insert_post([
			'post_title'  => __( 'Auto Draft' ),
			'post_type'   => Product::NAME,
			'post_status' => 'auto-draft',
		]);
	}

	/**
	 * Get the data that will be saved to the WordPress post
	 *
	 * @param Product_Builder $builder
	 *
	 * @return array
	 */
	protected function get_post_array( Product_Builder $builder ) {
		$postarr = parent::get_post_array( $builder );
		if ( ! array_key_exists( 'comment_status', $postarr ) ) {
			$postarr[ 'comment_status' ] = get_default_comment_status( Product::NAME );
		}

		return $postarr;
	}

	protected function send_notifications() {
		/**
		 * A product has been created by the import process
		 *
		 * @param int           $post_id The Post ID of the created product
		 * @param Model\Product $product The product data
		 * @param Model\Listing $listing The channel listing data
		 * @param CatalogApi    $catalog The Catalog API instance
		 */
		do_action( 'bigcommerce/import/product/created', $this->post_id, $this->product, $this->listing, $this->catalog );
		parent::send_notifications();
	}
}