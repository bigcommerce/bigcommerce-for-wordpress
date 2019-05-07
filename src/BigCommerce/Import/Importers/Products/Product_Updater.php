<?php

namespace BigCommerce\Import\Importers\Products;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Import\Importers\Products\Product_Builder;

class Product_Updater extends Product_Saver {

	protected function send_notifications() {
		/**
		 * A product has been updated by the import process
		 *
		 * @param int           $post_id The Post ID of the updated product
		 * @param Model\Product $product The product data
		 * @param Model\Listing $listing The channel listing data
		 * @param CatalogApi    $catalog The Catalog API instance
		 */
		do_action( 'bigcommerce/import/product/updated', $this->post_id, $this->product, $this->catalog );
		parent::send_notifications();
	}
}