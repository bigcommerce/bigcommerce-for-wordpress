<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\GraphQL\BaseGQL;
use BigCommerce\GraphQL\GraphQL_Processor;
use BigCommerce\Import\Mappers\Brand_Mapper;
use BigCommerce\Import\Mappers\Product_Category_Mapper;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Headless_Product_Processor {

	const HEADLESS_CURSOR   = 'bigcommerce_gql_next_cursor';
	const HEADLESS_PRODUCTS = 'bigcommerce_gql_products_process';
	const HEADLESS_CHANNEL  = 'bigcommerce_gql_active_channel';

	/**
	 * @var \BigCommerce\Import\Runner\Status
	 */
	private $status;

	/**
	 * @var \BigCommerce\GraphQL\GraphQL_Processor
	 */
	private $requester;

	/**
	 * @var \WP_Term
	 */
	private $channel_term;

	/**
	 * @var int|mixed
	 */
	private $batch;

	/**
	 * @var \BigCommerce\Api\v3\Api\CatalogApi
	 */
	private $api;

	public function __construct( CatalogApi $api, Status $status, GraphQL_Processor $requester, $channel_term, $batch = 50 ) {
		$this->api          = $api;
		$this->status       = $status;
		$this->requester    = $requester;
		$this->batch        = $batch;
		$this->channel_term = $channel_term;
	}

	/**
	 * Fetch products data via GraphQL and process it
	 */
	public function run(): void {
		$suffix = sprintf( '-%d', $this->channel_term->term_id );
		$this->status->set_status( Status::FETCHING_PRODUCTS . $suffix );
		// Store active channel
		$key = sprintf( '%s', self::HEADLESS_CHANNEL );

		if ( get_site_transient( $key ) !== $this->channel_term->term_id ) {
			set_site_transient( $key , $this->channel_term->term_id );
			delete_site_transient( BaseGQL::GQL_TOKEN );
		}

		$data = $this->requester->request_paginated_products( $this->batch );

		if ( empty( $data ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Headless processing request is empty', 'bigcommerce' ), [] );
			$this->status->set_status( Status::FETCHED_PRODUCTS . $suffix );
			delete_option( self::HEADLESS_CURSOR );
			delete_option( Product_Data_Fetcher::STATE_OPTION );

			return;
		}

		$this->process_data( $data );
	}

	protected function process_data( $data ): void {
		$products_data = $data->data->site->products;
		$page_info     = $products_data->pageInfo;
		$edges         = $products_data->edges;

		if ( empty( $edges ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Products not found. Headless processing finished', 'bigcommerce' ), [] );
			$suffix = sprintf( '-%d', $this->channel_term->term_id );
			$this->status->set_status( Status::FETCHED_PRODUCTS  . $suffix );
			delete_option( self::HEADLESS_CURSOR );
			delete_option( Product_Data_Fetcher::STATE_OPTION );

			return;
		}

		update_option( self::HEADLESS_CURSOR, $page_info->endCursor );

		$processed_ids = get_option( $this->get_option_name(), [] );
		$current_ids   = [];
		foreach ( $edges as $edge ) {
			$product_id      = $edge->node->entityId;
			$title           = $edge->node->name;
			$current_ids[]   = $product_id;
			$processed_ids[] = $this->process_single_edge( $edge, $product_id, $title );
		}

		$slugs = $this->get_bc_slugs( $current_ids );
		foreach ( $slugs as $product_id => $slug ) {
			$post_id = $this->is_product_exist( $product_id );
			if ( empty( $post_id ) ) {
				continue;
			}

			$this->update_product_slug( $post_id, $slug );
		}
		// Store imported products. Later on cleanup delete non-processed products. GraphQL returns only existing items
		update_option( $this->get_option_name(), $processed_ids );
	}

	protected function get_bc_slugs( $product_ids ): array {
		try {
			$response = $this->api->getProducts( [
				'id:in'          => [ $product_ids ],
				'limit'          => 250,
				'include_fields' => 'custom_url',
			] );
		}  catch ( ApiException $e ) {
			do_action( 'bigcommerce/import/error', $e->getMessage(), [
				'response' => $e->getResponseBody(),
				'headers'  => $e->getResponseHeaders(),
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			return [];
		}

		return array_values( array_filter( array_map( function ( \BigCommerce\Api\v3\Model\Product $product )  {
			return [ $product->getId() => $product->getCustomUrl()->getUrl() ];
		}, $response->getData() ) ) );
	}

	protected function update_product_slug( $post_id, $slug ) {
		$slug = str_replace( '/', '', $slug );

		wp_update_post( [
			'ID'        => $post_id,
			'post_name' => $slug,
		] );
	}

	protected function process_single_edge( $edge, $product_id, $title ) {
		$post_id = $this->is_product_exist( $product_id );
		if ( ! empty( $post_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Skip processing. Product exists', 'bigcommerce' ), [
				'product_id' => $product_id,
				'term_id' => $this->channel_term->term_id
			] );

			return $post_id;
		}

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Process single product', 'bigcommerce' ), [
			'product_id' => $product_id,
		] );

		$post_id = wp_insert_post([
			'post_title'  => $title,
			'post_type'   => Product::NAME,
			'post_status' => 'publish',
		]);

		if ( is_wp_error( $post_id ) ) {
			do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Unable to create product', 'bigcommerce' ), [
					'product_id' => $product_id,
			] );

			return;
		}

		update_post_meta( $post_id, Product::BIGCOMMERCE_ID, $product_id );
		$this->save_terms( $edge, $post_id );

		return $post_id;
	}

	/**
	 * @param $edge
	 * @param $post_id
	 */
	protected function save_terms( $edge, $post_id ) {
		$brand_id = null;

		if ( ! empty( $edge->node->brand ) ) {
			$brand_id = $edge->node->brand->entityId;
		}

		$categories_ids = [];
		if ( ! empty( $edge->node->categories ) ) {
			foreach ( $edge->node->categories->edges as $item ) {
				$categories_ids[] = $item->node->entityId;
			}
		}

		$terms = $this->build_terms( $categories_ids, $brand_id );

		foreach ( [ Brand::NAME, Product_Category::NAME, Channel::NAME ] as $taxonomy ) {
			if ( empty( $terms[ $taxonomy ] ) ) {
				continue;
			}

			wp_set_object_terms( $post_id, array_map( 'intval', $terms[ $taxonomy ] ), $taxonomy, false );
		}
	}

	/**
	 * @param $categories
	 * @param $brand_id
	 *
	 * @return array
	 */
	private function build_terms( $categories, $brand_id ) {
		$terms = [];

		if ( ! empty( $categories ) ) {
			$terms[ Product_Category::NAME ] = array_filter( $this->map_product_categories( $categories ) );
		}

		if ( ! empty( $brand_id ) ) {
			$terms[ Brand::NAME ] = array_filter( $this->map_brand( [ $brand_id ] ) );
		}

		$terms[ Channel::NAME ] = [ (int) $this->channel_term->term_id ];

		return $terms;
	}

	/**
	 * @param array $bc_category_ids
	 *
	 * @return array
	 */
	private function map_product_categories( array $bc_category_ids ) {
		$mapper = new Product_Category_Mapper();

		return array_map( [ $mapper, 'map' ], $bc_category_ids );
	}

	/**
	 * @param array $bc_brand_ids
	 *
	 * @return array
	 */
	private function map_brand( array $bc_brand_ids ) {
		$mapper = new Brand_Mapper();

		return array_map( [ $mapper, 'map' ], $bc_brand_ids );
	}

	/**
	 * Check if product is already imported
	 *
	 * @param $product_id
	 *
	 * @return false|int
	 */
	protected function is_product_exist( $product_id ) {
		global $wpdb;
		$query = $wpdb->prepare(
			"SELECT tpm.post_id FROM `$wpdb->postmeta` tpm INNER JOIN `$wpdb->term_relationships` ttr ON ttr.object_id = tpm.post_id INNER JOIN `$wpdb->term_taxonomy` ttt ON ttt.`term_taxonomy_id` = ttr.term_taxonomy_id WHERE ttt.term_id = %d AND tpm.meta_key = %s AND tpm.meta_value = %d",
			$this->channel_term->term_id,
				Product::BIGCOMMERCE_ID,
				$product_id
		);
		$post_id = $wpdb->get_var( $query );

		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			return false;
		}

		return $post_id;
	}

	private function get_option_name() {
		return sprintf( '%s-%d', self::HEADLESS_PRODUCTS, $this->channel_term->term_id );
	}

}
