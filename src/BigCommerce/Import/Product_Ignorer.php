<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;

class Product_Ignorer implements Post_Import_Strategy {
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
	 * @var int
	 */
	private $post_id;

	/**
	 * Product_Ignorer constructor.
	 *
	 * @param Model\Product $product
	 * @param Model\Listing $listing
	 * @param CatalogApi    $catalog
	 * @param int           $post_id
	 */
	public function __construct( Model\Product $product, Model\Listing $listing, CatalogApi $catalog, $post_id ) {
		$this->product = $product;
		$this->listing = $listing;
		$this->catalog = $catalog;
		$this->post_id = $post_id;
	}

	public function do_import() {
		/**
		 * A product has been skipped for import
		 *
		 * @param int           $post_id The Post ID of the skipped product
		 * @param Model\Product $product The product data
		 * @param Model\Listing $listing The channel listing data
		 * @param CatalogApi    $catalog The Catalog API instance
		 */
		do_action( 'bigcommerce/import/product/skipped', $this->post_id, $this->product, $this->listing, $this->catalog );

		return $this->post_id;
	}
}