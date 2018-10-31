<?php


namespace BigCommerce\CLI;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Import\Runner\CLI_Runner;
use BigCommerce\Import\Runner\Lock;

class Import_Products extends Command {
	protected function command() {
		return 'import products';
	}

	protected function description() {
		return __( 'Imports products from the connected BigCommerce store', 'bigcommerce' );
	}

	protected function arguments() {
		return [
			[
				'type'        => 'flag',
				'name'        => 'force',
				'optional'    => true,
				'description' => __( 'Force all products to refresh, even if they have up-to-date data. Defaults to false.', 'bigcommerce' ),
				'default'     => false,
			],
		];
	}

	public function run( $args, $assoc_args ) {

		if ( ! empty( $assoc_args[ 'force' ] ) ) {
			add_filter( 'bigcommerce/import/strategy/needs_refresh', '__return_true' );
		}

		$this->hook_messages();

		$runner = new CLI_Runner();

		switch ( $runner->run() ) {
			case CLI_Runner::RESPONSE_SUCCESS:
				\WP_CLI::success( __( 'Import complete!', 'bigcommerce' ) );
				break;
			case CLI_Runner::RESPONSE_LOCKED:
				\WP_CLI::warning( sprintf( __( 'Import already in progress. Cannot proceed while the lock is in place. Delete it with: wp option delete %s', 'bigcommerce' ), Lock::OPTION ) );
				break;
			case CLI_Runner::RESPONSE_ERROR:
				\WP_CLI::warning( __( 'Unable to complete import.', 'bigcommerce' ) );
				break;
		}
	}

	private function hook_messages() {
		add_action( 'bigcommerce/import/before', function ( $status ) {
			\WP_CLI::debug( sprintf( __( 'Starting import phase. Status: %s', 'bigcommerce' ), $status ) );
		}, 10, 1 );

		add_action( 'bigcommerce/import/after', function ( $status ) {
			\WP_CLI::debug( sprintf( __( 'Finished import phase. Status: %s', 'bigcommerce' ), $status ) );
		}, 10, 1 );

		add_action( 'bigcommerce/import/set_status', function ( $status ) {
			\WP_CLI::debug( sprintf( __( 'Status set to: %s', 'bigcommerce' ), $status ) );
		}, 10, 1 );

		add_action( 'bigcommerce/import/start', function () {
			\WP_CLI::log( __( 'Starting import.', 'bigcommerce' ) );
		}, 10, 0 );

		add_action( 'bigcommerce/import/fetched_ids', function ( $count ) {
			\WP_CLI::debug( sprintf( __( 'Added %d products to the queue', 'bigcommerce' ), $count ) );
		}, 10, 1 );

		add_action( 'bigcommerce/import/marked_deleted', function ( $count ) {
			\WP_CLI::debug( sprintf( __( 'Marked %d products to be deleted', 'bigcommerce' ), $count ) );
		}, 10, 1 );

		add_action( 'bigcommerce/import/product/created', function ( $post_id, $data ) {
			\WP_CLI::log( sprintf( __( 'Created post %d for product %d', 'bigcommerce' ), $post_id, $data[ 'id' ] ) );
		}, 10, 2 );

		add_action( 'bigcommerce/import/product/updated', function ( $post_id, $data ) {
			\WP_CLI::log( sprintf( __( 'Updated post %d for product %d', 'bigcommerce' ), $post_id, $data[ 'id' ] ) );
		}, 10, 2 );

		add_action( 'bigcommerce/import/product/skipped', function ( $post_id, $data ) {
			\WP_CLI::log( sprintf( __( 'Skipped post %d for product %d. Already up to date.', 'bigcommerce' ), $post_id, $data[ 'id' ] ) );
		}, 10, 2 );

		add_action( 'bigcommerce/import/fetched_currency', function ( $currency_code ) {
			\WP_CLI::log( sprintf( __( 'Set currency code to %s', 'bigcommerce' ), $currency_code ) );
		}, 10, 1 );

		add_action( 'bigcommerce/import/could_not_fetch_store_settings', function () {
			\WP_CLI::log( __( 'Unable to fetch store settings', 'bigcommerce' ) );
		}, 10, 0 );

		add_action( 'bigcommerce/import/error', function ( $message = '', $data = [] ) {
			if ( $data ) {
				\WP_CLI::debug( print_r( $data, true ) );
			}
			\WP_CLI::error( sprintf( __( 'Import failed with message: %s', 'bigcommerce' ), $message ) ?: __( 'Import failed.', 'bigcommerce' ), false );
		}, 10, 2 );

		add_action( 'bigcommerce/import/product/error', function ( $product_id, CatalogApi $catalog_api, \Exception $exception ) {
			\WP_CLI::warning( sprintf( __( 'Failed to import product with ID %d. Error: %s', 'bigcommerce' ), $product_id, $exception->getMessage() ) );
		}, 10, 3 );
	}

}