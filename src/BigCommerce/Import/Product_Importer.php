<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\Api\CatalogApi;

class Product_Importer {
	/**
	 * @var int The ID of the product on BigCommerce
	 */
	private $product_id;

	/**
	 * @var CatalogApi
	 */
	private $catalog_api;

	/**
	 * Product_Importer constructor.
	 *
	 * @param            $product_id
	 * @param CatalogApi $catalog_api
	 */
	public function __construct( $product_id, CatalogApi $catalog_api ) {
		$this->product_id  = $product_id;
		$this->catalog_api = $catalog_api;
	}

	/**
	 * @return int The ID of the imported post
	 */
	public function import() {
		try {
			$response = $this->catalog_api->getProductById(
				$this->product_id, // $id
				[ 'variants', 'custom_fields', 'images', 'bulk_pricing_rules' ] // $include
			);
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/import/product/error', $this->product_id, $this->catalog_api, $e );

			return 0;
		}
		$strategy_factory = new Import_Strategy_Factory( $response->getData(), $this->catalog_api, Post_Import_Strategy::VERSION );

		$strategy = $strategy_factory->get_strategy();

		return $strategy->do_import();
	}
}