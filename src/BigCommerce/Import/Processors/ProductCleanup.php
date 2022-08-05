<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Purge BC products
 *
 * @class ProductCleanup
 */
class ProductCleanup {

	/**
	 * Purge products that were deleted on Bigcommerce side and refresh existing products cache
	 */
	public function run() {
		$terms = $this->get_terms();

		if ( empty( $terms ) ) {
			return;
		}

		wp_schedule_single_event( time(), Cleanup::CLEAN_PRODUCTS_TRANSIENT, [
			'offset'    => 0,
			'partially' => true,
		] );

		foreach ( $terms as $term ) {
			$existing_products = get_option( $this->get_option_name( $term->term_id ), [] );

			if ( empty( $existing_products ) ) {
				continue;
			}

			$this->process_single_channel( $term->term_id, $existing_products );
		}
	}

	protected function process_single_channel( $term_id, $existing_products ) {
		$query = new \WP_Query();
		$tasks = $query->query( [
			'post_type'              => Product::NAME,
			'post_status'            => ['publish', 'draft', 'trash'],
			'posts_per_page'         => -1,
			'post__not_in'           => $existing_products,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => [
				[
					'taxonomy' => Channel::NAME,
					'field'    => 'term_id',
					'terms'    => $term_id,
				],
			],
		] );

		foreach ( $tasks as $post_id ) {
			$result = wp_delete_post( $post_id, true );

			if ( ! empty( $result ) ) {
				continue;
			}

			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Could not delete product data', 'bigcommerce' ), [
				'post_id' => $post_id,
			] );
		}

		delete_option( $this->get_option_name( $term_id ) );

		do_action( 'bigcommerce/log', Error_Log::INFO, __( sprintf( 'Products data purge completed for channel %d', $term_id ), 'bigcommerce' ), [] );
	}

	protected function get_terms() {
		$connections = new Connections();

		return $connections->active();
	}

	private function get_option_name( $term_id ) {
		return sprintf( '%s-%d', Headless_Product_Processor::HEADLESS_PRODUCTS, $term_id );
	}
}
