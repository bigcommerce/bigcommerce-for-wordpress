<?php


namespace BigCommerce\Container;


use BigCommerce\Import\Processors;
use BigCommerce\Import\Runner;
use BigCommerce\Settings\Sections\Import as Import_Settings;
use Pimple\Container;

class Import extends Provider {
	const CRON_MONITOR = 'cron.monitor';
	const CRON_RUNNER  = 'cron.runner';
	const TIMEOUT      = 'timeout';

	const START   = 'import.start';
	const LISTING = 'import.listings';
	const CHANNEL = 'import.channel';
	const FETCH   = 'import.fetch_ids';
	const MARK    = 'import.mark_deleted';
	const QUEUE   = 'import.queue';
	const STORE   = 'import.store';
	const CLEANUP = 'import.cleanup';
	const ERROR   = 'import.error';

	public function register( Container $container ) {
		$this->cron( $container );
		$this->process( $container );
	}

	private function cron( Container $container ) {
		$container[ self::TIMEOUT ] = function ( Container $container ) {
			/**
			 * Filter the timeout for an import job. If a step in the import
			 * takes more than this amount of time, it will be considered stalled
			 * and a new job will take it over.
			 *
			 * @param int $timeout The timeout in seconds
			 */
			return apply_filters( 'bigcommerce/import/timeout', 5 * MINUTE_IN_SECONDS );
		};

		$container[ self::CRON_MONITOR ] = function ( Container $container ) {
			return new Runner\Cron_Monitor( $container[ self::TIMEOUT ] );
		};

		$container[ self::CRON_RUNNER ] = function ( Container $container ) {
			return new Runner\Cron_Runner();
		};

		add_action( 'init', $this->create_callback( 'cron_init', function () use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				$container[ self::CRON_MONITOR ]->check_for_scheduled_crons();
			}
		} ), 10, 0 );

		add_action( 'update_option_' . Import_Settings::OPTION_FREQUENCY, $this->create_callback( 'cron_schedule_update', function ( $old_value, $new_value ) use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				$container[ self::CRON_MONITOR ]->listen_for_changed_schedule( $old_value, $new_value );
			}
		} ), 10, 2 );

		add_action( 'bigcommerce/import/run/status=' . Runner\Status::STARTED, $this->create_callback( 'cron_unschedule_start', function () use ( $container ) {
			$container[ self::CRON_MONITOR ]->listen_for_import_start();
		} ), 9, 1 );

		add_action( Runner\Cron_Runner::START_CRON, $this->create_callback( 'cron_start', function () use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				$container[ self::CRON_RUNNER ]->start_import();
			}
		} ), 10, 0 );

		add_action( Runner\Cron_Runner::CONTINUE_CRON, $this->create_callback( 'cron_continue', function () use ( $container ) {
			$container[ self::CRON_RUNNER ]->continue_import();
		} ), 10, 0 );
	}

	private function process( Container $container ) {
		$container[ self::START ] = function ( Container $container ) {
			return new Processors\Start_Import();
		};

		$container[ self::LISTING ] = function ( Container $container ) {
			return new Processors\Listing_ID_Fetcher( $container[ Api::FACTORY ]->channels() );
		};

		$container[ self::CHANNEL ] = function ( Container $container ) {
			return new Processors\Channel_Initializer( $container[ Api::FACTORY ]->channels(), $container[ Api::FACTORY ]->catalog() );
		};

		$container[ self::FETCH ] = function ( Container $container ) {
			return new Processors\Product_ID_Fetcher( $container[ Api::FACTORY ]->channels() );
		};

		$container[ self::MARK ] = function ( Container $container ) {
			return new Processors\Deleted_Product_Marker();
		};

		$container[ self::QUEUE ] = function ( Container $container ) {
			return new Processors\Queue_Runner( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels(), 5, 10 );
		};

		$container[ self::STORE ] = function ( Container $container ) {
			return new Processors\Store_Settings( $container[ Api::FACTORY ]->store() );
		};

		$container[ self::CLEANUP ] = function ( Container $container ) {
			return new Processors\Cleanup();
		};

		$container[ self::ERROR ] = function ( Container $container ) {
			return new Processors\Error_Handler();
		};

		$start = $this->create_callback( 'process_start', function () use ( $container ) {
			$container[ self::START ]->run();
		} );
		add_action( 'bigcommerce/import/start', $start, 10, 0 );

		// Step: Get a list of all products already listed in the channel

		$channel = $this->create_callback( 'process_listings', function () use ( $container ) {
			$container[ self::LISTING ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::STARTED, $channel, 10, 0 );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::FETCHING_LISTING_IDS, $channel, 10, 0 );

		// Step: Make sure that the channel is fully initialized with products. May take multiple batches

		$channel = $this->create_callback( 'process_channel', function () use ( $container ) {
			$container[ self::CHANNEL ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::FETCHED_LISTING_IDS, $channel, 10, 0 );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::INITIALIZING_CHANNEL, $channel, 10, 0 );

		// Step: Import product IDs. May take multiple batches

		$fetch = $this->create_callback( 'process_fetch', function () use ( $container ) {
			$container[ self::FETCH ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::INITIALIZED_CHANNEL, $fetch, 10, 0 );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::FETCHING_PRODUCT_IDS, $fetch, 10, 0 );

		// Step: Any products no longer coming from the api should be deleted

		$mark = $this->create_callback( 'process_mark', function () use ( $container ) {
			$container[ self::MARK ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::FETCHED_PRODUCT_IDS, $mark, 10, 0 );
		// in theory this should never be able to run, as the marking process is a single step. Leave it as a safeguard.
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::MARKING_DELETED_PRODUCTS, $mark, 10, 0 );

		// Step: Process the queue we've established.

		$queue = $this->create_callback( 'process_queue', function () use ( $container ) {
			$container[ self::QUEUE ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::MARKED_DELETED_PRODUCTS, $queue, 10, 0 );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::PROCESSING_QUEUE, $queue, 10, 0 );

		// Step: Get the store info (currency, units).

		$store = $this->create_callback( 'fetch_store', function () use ( $container ) {
			$container[ self::STORE ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::PROCESSED_QUEUE, $store, 10, 0 );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::FETCHING_STORE, $store, 10, 0 );

		// Step: Cleanup

		$cleanup = $this->create_callback( 'process_cleanup', function () use ( $container ) {
			$container[ self::CLEANUP ]->run();
		} );
		add_action( 'bigcommerce/import/run/status=' . Runner\Status::FETCHED_STORE, $cleanup, 10, 0 );

		$error = $this->create_callback( 'process_error', function () use ( $container ) {
			$container[ self::ERROR ]->run();
		} );
		add_action( 'bigcommerce/import/error', $error, 10, 0 );

	}
}