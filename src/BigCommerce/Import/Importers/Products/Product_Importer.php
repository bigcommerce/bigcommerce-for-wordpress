<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Api\v3\Model\Listing;
use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Import\Importers\Products\Product_Strategy_Factory;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Logging\Error_Log;

class Product_Importer {
	/**
	 * @var Product The Product from the BigCommerce catalog API
	 */
	private $product;

	/**
	 * @var Listing the Listing from the BigCommerce channel API
	 */
	private $listing;

	/**
	 * @var CatalogApi
	 */
	private $catalog_api;

	/**
	 * @var ChannelsApi
	 */
	private $channels_api;

	/**
	 * @var int
	 */
	private $channel_id;

	/**
	 * Product_Importer constructor.
	 *
	 * @param Product         $product
	 * @param Listing         $listing
	 * @param CatalogApi  $catalog_api
	 * @param ChannelsApi $channels_api
	 * @param int         $channel_id
	 */
	public function __construct( Product $product, Listing $listing, CatalogApi $catalog_api, ChannelsApi $channels_api, $channel_id ) {
		$this->product      = $product;
		$this->listing      = $listing;
		$this->catalog_api  = $catalog_api;
		$this->channels_api = $channels_api;
		$this->channel_id   = $channel_id;
	}

	/**
	 * @return int The ID of the imported post
	 */
	public function import() {
		$strategy_factory = new Product_Strategy_Factory( $this->product, $this->listing, $this->catalog_api, Import_Strategy::VERSION );

		$strategy = $strategy_factory->get_strategy();

		return $strategy->do_import();
	}
}