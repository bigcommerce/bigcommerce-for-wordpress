<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Accounts\Customer;
use BigCommerce\Cache\Cache_Handler;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Import\Import_Type;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;

class Cleanup implements Import_Processor {

	const CLEAN_USERS_TRANSIENT    = 'bigcommerce_users_transient_clean';
	const PURGE_PRODUCTS           = 'bigcommerce_purge_products_headless';
	const CLEAN_PRODUCTS_TRANSIENT = 'bigcommerce_products_transient_clean';
	const CLEAN_POSTS_PER_PAGE     = 25;

	/** @var int */
	private $batch;

	/**
	 * @var Cache_Handler
	 */
	private $cache_handler;

	/**
	 * Cleanup constructor.
	 *
	 * @param int $batch How many records to clean up per batch
	 */
	public function __construct( Cache_Handler $cache_handler, $batch = self::CLEAN_POSTS_PER_PAGE ) {
		$this->cache_handler = $cache_handler;
		$this->batch         = $batch;
	}

	public function run( $abort = false, $pre_import = false ) {
		$status = new Status();
		$status->set_status( Status::CLEANING );

		$this->clean_tasks( $pre_import );
		delete_option( Term_Import::BRANDS_CHECKPOINT );

		if ( $pre_import ) {
			$status->set_status( Status::STARTED );

			return;
		}

		delete_option( Listing_Fetcher::PRODUCT_LISTING_MAP );
		delete_option( Product_Data_Fetcher::FILTERED_LISTING_MAP );
		delete_option( Import_Type::IMPORT_TYPE );

		if ( $abort ) {
			$status->set_status( Status::ABORTED );
		} else {
			$status->set_status( Status::COMPLETED );
		}

		if ( ! Import_Type::is_traditional_import() ) {
			wp_schedule_single_event( time(), self::PURGE_PRODUCTS );
		}

		wp_schedule_single_event( time(), self::CLEAN_USERS_TRANSIENT );

		wp_unschedule_hook( Cron_Runner::START_CRON );
		wp_unschedule_hook( Cron_Runner::CONTINUE_CRON );

		$status->rotate_logs(); // must rotate _after_ status set to complete

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Import complete', 'bigcommerce' ), [] );
	}

	/**
	 * Clean Queue_Task::NAME posts before/after import
	 *
	 * @param boolean $pre_import
	 */
	private function clean_tasks( $pre_import = false ) {
		global $wpdb;
		$sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type ='%s'";

		if ( empty( $pre_import ) ) {
			$sql           .= " AND post_status = '%s'";
			$prepared_query = $wpdb->prepare( $sql, Queue_Task::NAME, 'trash' );
		} else {
			$prepared_query = $wpdb->prepare( $sql, Queue_Task::NAME );
		}

		$tasks = $wpdb->get_results( $prepared_query );

		if ( empty( $tasks ) || is_wp_error( $tasks ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Clean tasks queue is empty', 'bigcommerce' ), [] );

			if ( is_wp_error( $tasks ) ) {
				do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Error is occurred in the cleanup task query', 'bigcommerce' ), [
					'error' => $tasks,
				] );
			}

			return;
		}

		foreach ( $tasks as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

	/**
	 * Remove customers group transient cache after sync in order to retrieve fresh groups data
	 */
	public function clean_customer_group_transients(): void {
		$users_ids = get_users( [ 'fields' => 'ID' ] );
		foreach ( $users_ids as $users_id ) {
			$customer_id = get_user_option( Customer::CUSTOMER_ID_META, $users_id );
			$customer    = new Customer( $users_id );
			delete_transient( sprintf( 'bccustgroupinfo%d', $customer->get_group_id() ) );
			$transient_key = sprintf( 'bccustomergroup%d', $customer_id );
			delete_transient( $transient_key );
			delete_transient( sprintf( 'bccustomervisibleterms%d', $users_id ) );
		}
	}

	/**
	 * Flush products transient cache
	 *
	 * @param int  $offset
	 * @param bool $partially
	 */
	public function refresh_products_transient( $offset = 0, $partially = false ): void {
		$posts = get_posts( [
			'post_type'      => Product::NAME,
			'posts_per_page' => self::CLEAN_POSTS_PER_PAGE,
			'offset'         => $offset,
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );

		if ( empty( $posts ) ) {
			return;
		}


		foreach ( $posts as $post ) {
			$product_id = get_post_meta( $post->ID, Product::BIGCOMMERCE_ID, true ) ?? 0;

			if ( empty( $product_id ) ) {
				continue;
			}

			$this->cache_handler->flush_product_catalog_object_cache( $product_id );

			if ( Import_Type::is_traditional_import() ) {
				continue;
			}

			delete_transient( sprintf( '%s', Headless_Product_Processor::HEADLESS_CHANNEL ) );
			delete_transient( sprintf( '%s%d', Product::OPTIONS_DATA_TRANSIENT, $post->ID ) );
			delete_transient( sprintf( '%s%d', Product::BRAND_TRANSIENT, $post->ID ) );
			$this->refresh_product_source( $post->ID );
		}

		if ( ! $partially ) {
			wp_schedule_single_event( time(), Cleanup::CLEAN_PRODUCTS_TRANSIENT, [
					'offset' => $offset + self::CLEAN_POSTS_PER_PAGE,
			] );
		}
	}

	/**
	 * Refresh product cache or make a pre-cache in order to make initial load smooth
	 *
	 * @param int $post_id
	 */
	public function refresh_product_source( $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			return;
		}

		delete_transient( sprintf( 'bigcommerce_gql_source%d', $post_id ) );
		$product = new Product( $post_id );
		$product->get_source_data();
	}

}
