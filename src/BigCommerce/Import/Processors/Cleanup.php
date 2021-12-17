<?php


namespace BigCommerce\Import\Processors;


use BigCommerce\Accounts\Customer;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Import\Import_Type;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;

class Cleanup implements Import_Processor {

	/** @var int */
	private $batch;

	/**
	 * Cleanup constructor.
	 *
	 * @param int $batch How many records to clean up per batch
	 */
	public function __construct( $batch = 100 ) {
		$this->batch = $batch;
	}

	public function run( $abort = false ) {
		$status = new Status();
		$status->set_status( Status::CLEANING );

		$query = new \WP_Query();
		$tasks = $query->query( [
			'post_type'      => Queue_Task::NAME,
			'post_status'    => 'trash',
			'posts_per_page' => $this->batch,
			'fields'         => 'ids',
		] );

		foreach( $tasks as $post_id ) {
			wp_delete_post( $post_id, true );
		}

		if ( $query->found_posts > count( $tasks ) ) {
			return; // delete more in the next batch
		}

		delete_option( Listing_Fetcher::PRODUCT_LISTING_MAP );
		delete_option( Product_Data_Fetcher::FILTERED_LISTING_MAP );
		delete_option( Import_Type::IMPORT_TYPE );

		if ( $abort ) {
			$status->set_status( Status::ABORTED );
		} else {
			$status->set_status( Status::COMPLETED );
		}


		$this->clean_customer_group_transients();

		wp_unschedule_hook( Cron_Runner::START_CRON );
		wp_unschedule_hook( Cron_Runner::CONTINUE_CRON );

		$status->rotate_logs(); // must rotate _after_ status set to complete

		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Import complete', 'bigcommerce' ), [] );
	}

	/**
	 * Remove customers group transient cache after sync in order to retrieve fresh groups data
	 */
	private function clean_customer_group_transients(): void {
		$users_ids = get_users( [ 'fields' => 'ID' ] );

		foreach ( $users_ids as $users_id ) {
			$customer_id   = get_user_option( Customer::CUSTOMER_ID_META, $users_id );
			$transient_key = sprintf( 'bccustomergroup%d', $customer_id );
			delete_transient( $transient_key );
		}
	}
}
