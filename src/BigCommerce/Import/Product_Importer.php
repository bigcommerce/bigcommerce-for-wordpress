<?php


namespace BigCommerce\Import;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Logging\Error_Log;

class Product_Importer {
	/**
	 * @var int The ID of the product on BigCommerce
	 */
	private $product_id;

	/**
	 * @var int the Listing ID of the product in the BigCommerce channel
	 */
	private $listing_id;

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
	 * @param int         $product_id
	 * @param int         $listing_id
	 * @param CatalogApi  $catalog_api
	 * @param ChannelsApi $channels_api
	 * @param int         $channel_id
	 */
	public function __construct( $product_id, $listing_id, CatalogApi $catalog_api, ChannelsApi $channels_api, $channel_id ) {
		$this->product_id   = $product_id;
		$this->listing_id   = $listing_id;
		$this->catalog_api  = $catalog_api;
		$this->channels_api = $channels_api;
		$this->channel_id   = $channel_id;
	}

	/**
	 * @return int The ID of the imported post
	 */
	public function import() {
		try {
			$product_response = $this->catalog_api->getProductById(
				$this->product_id, // $id
				[ 'variants', 'custom_fields', 'images', 'bulk_pricing_rules' ] // $include
			);
			$listing_response = $this->channels_api->getChannelListing( $this->channel_id, $this->listing_id );
		} catch ( \Exception $e ) {
			do_action( 'bigcommerce/import/log', Error_Log::WARNING, __( 'Product import error', 'bigcommerce' ), [
				'product_id' => $this->product_id,
				'error'      => $e,
			] );
			do_action( 'bigcommerce/import/product/error', $this->product_id, $this->catalog_api, $e );

			return 0;
		}
		$strategy_factory = new Import_Strategy_Factory( $product_response->getData(), $listing_response->getData(), $this->catalog_api, Post_Import_Strategy::VERSION );

		$strategy = $strategy_factory->get_strategy();

		return $strategy->do_import();
	}
}