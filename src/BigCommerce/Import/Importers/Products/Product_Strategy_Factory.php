<?php


namespace BigCommerce\Import\Importers\Products;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model;
use BigCommerce\Api\v3\ObjectSerializer;
use BigCommerce\Exceptions\Product_Not_Found_Exception;
use BigCommerce\Import\Import_Strategy;
use BigCommerce\Import\Importers\Products\Product_Creator;
use BigCommerce\Import\Importers\Products\Product_Ignorer;
use BigCommerce\Import\Importers\Products\Product_Updater;
use BigCommerce\Post_Types\Product\Product;

/**
 * Factory class for determining the appropriate import strategy for a product.
 * This class decides whether to create, ignore, or update a product based on its existence and freshness.
 */
class Product_Strategy_Factory {
	/**
	 * @var Model\Product $product The product data from BigCommerce API
	 */
	private $product;

	/**
	 * @var Model\Listing $listing The channel listing data from BigCommerce API
	 */
	private $listing;

	/**
	 * @var CatalogApi $catalog The Catalog API instance
	 */
	private $catalog;

	/**
	 * @var string $version The version of the importer
	 */
	private $version;

	/**
	 * @var \WP_Term $channel_term The channel term for the product import
	 */
	private $channel_term;

	/**
	 * Product_Strategy_Factory constructor.
	 *
	 * @param Model\Product $product The product data from BigCommerce API
	 * @param Model\Listing $listing The channel listing data from BigCommerce API
	 * @param \WP_Term $channel_term The channel term for the product import
	 * @param CatalogApi $catalog The Catalog API instance
	 * @param string $version The version of the importer
	 */
	public function __construct( Model\Product $product, Model\Listing $listing, \WP_Term $channel_term, CatalogApi $catalog, $version ) {
		$this->product      = $product;
		$this->listing      = $listing;
		$this->catalog      = $catalog;
		$this->version      = $version;
		$this->channel_term = $channel_term;
	}

	/**
	 * Returns the appropriate import strategy based on the product and channel data.
	 *
	 * @return Import_Strategy The import strategy (either Product_Creator, Product_Ignorer, or Product_Updater)
	 */
	public function get_strategy() {
		$matching_post_id = $this->get_matching_post();
		if ( empty( $matching_post_id ) ) {
			return new Product_Creator( $this->product, $this->listing, $this->channel_term, $this->catalog );
		}

		if ( ! $this->needs_refresh( $matching_post_id ) ) {
			return new Product_Ignorer( $this->product, $this->listing, $this->channel_term, $this->catalog, $matching_post_id );
		}

		return new Product_Updater ( $this->product, $this->listing, $this->channel_term, $this->catalog, $matching_post_id );

	}

	private function get_matching_post() {

		$args = [
			'meta_query'     => [
				[
					'key'   => 'bigcommerce_id',
					'value' => absint( $this->product->getId() ),
				],
			],
			'tax_query'      => [
				[
					'taxonomy' => $this->channel_term->taxonomy,
					'field'    => 'term_id',
					'terms'    => [ (int) $this->channel_term->term_id ],
					'operator' => 'IN',
				],
			],
			'post_type'      => Product::NAME,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'post_status'    => 'any',
		];

		$posts = get_posts( $args );
		if ( empty( $posts ) ) {
			return 0;
		}

		return absint( reset( $posts ) );
	}

	private function needs_refresh( $post_id ) {
		if ( get_post_meta( $post_id, Product::REQUIRES_REFRESH_META_KEY, true ) ) {
			$response = true;
		} elseif ( get_post_meta( $post_id, Product::IMPORTER_VERSION_META_KEY, true ) != $this->version ) {
			$response = true;
		} else {
			$new_hash = Product_Builder::hash( $this->product, $this->listing );
			$old_hash = get_post_meta( $post_id, Product::DATA_HASH_META_KEY, true );
			$response = $new_hash !== $old_hash;
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