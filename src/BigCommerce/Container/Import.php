<?php


namespace BigCommerce\Container;


use BigCommerce\Import\Processors;
use BigCommerce\Import\Runner;
use BigCommerce\Import\Task_Definition;
use BigCommerce\Import\Task_Manager;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Sections\Import as Import_Settings;
use Pimple\Container;

class Import extends Provider {
	const CRON_MONITOR = 'cron.monitor';
	const CRON_RUNNER  = 'cron.runner';
	const TIMEOUT      = 'timeout';

	const TASK_MANAGER = 'import.task_manager';
	const TASK_LIST    = 'import.task_list';

	const BATCH_SIZE       = 'import.batch_size';
	const LARGE_BATCH_SIZE = 'import.large_batch_size';

	const START      = 'import.start';
	const LISTING    = 'import.listings';
	const CHANNEL    = 'import.channel';
	const CATEGORIES = 'import.categories';
	const BRANDS     = 'import.brands';
	const FETCH      = 'import.fetch_ids';
	const MARK       = 'import.mark_deleted';
	const QUEUE      = 'import.queue';
	const STORE      = 'import.store';
	const CLEANUP    = 'import.cleanup';
	const ERROR      = 'import.error';

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
			if ( $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::CRON_MONITOR ]->check_for_scheduled_crons();
			}
		} ), 10, 0 );

		add_action( 'update_option_' . Import_Settings::OPTION_FREQUENCY, $this->create_callback( 'cron_schedule_update', function ( $old_value, $new_value ) use ( $container ) {
			if ( $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::CRON_MONITOR ]->listen_for_changed_schedule( $old_value, $new_value );
			}
		} ), 10, 2 );

		add_action( 'bigcommerce/import/run', $this->create_callback( 'cron_unschedule_start', function ( $status ) use ( $container ) {
			$container[ self::CRON_MONITOR ]->listen_for_import_start( $status );
		} ), 9, 1 );

		add_action( Runner\Cron_Runner::START_CRON, $this->create_callback( 'cron_start', function () use ( $container ) {
			if ( $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::CRON_RUNNER ]->start_import();
			}
		} ), 10, 0 );

		add_action( Runner\Cron_Runner::CONTINUE_CRON, $this->create_callback( 'cron_continue', function () use ( $container ) {
			$container[ self::CRON_RUNNER ]->continue_import();
		} ), 10, 0 );

		add_action( 'wp_ajax_' . Import_Status::AJAX_ACTION_IMPORT_STATUS, $this->create_callback( 'ajax_continue', function () use ( $container ) {
			$container[ self::CRON_RUNNER ]->ajax_continue_import();
		} ), 5, 0 );
	}

	private function process( Container $container ) {

		$container[ self::BATCH_SIZE ] = function ( Container $container ) {
			$batch = absint( get_option( Import_Settings::BATCH_SIZE, 5 ) );
			if ( $batch < 1 ) {
				return 1;
			}
			if ( $batch > 250 ) {
				return 250;
			}

			return $batch;
		};

		$container[ self::LARGE_BATCH_SIZE ] = function ( Container $container ) {
			return min( $container[ self::BATCH_SIZE ] * 20, 250 );
		};

		$container[ self::START ] = function ( Container $container ) {
			return new Processors\Start_Import();
		};

		$container[ self::LISTING ] = function ( Container $container ) {
			return new Processors\Listing_ID_Fetcher( $container[ Api::FACTORY ]->channels(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::CHANNEL ] = function ( Container $container ) {
			return new Processors\Channel_Initializer( $container[ Api::FACTORY ]->channels(), $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::CATEGORIES ] = function ( Container $container ) {
			return new Processors\Category_Import( $container[ Api::FACTORY ]->catalog(), $container[ self::BATCH_SIZE ] );
		};

		$container[ self::BRANDS ] = function ( Container $container ) {
			return new Processors\Brand_Import( $container[ Api::FACTORY ]->catalog(), $container[ self::BATCH_SIZE ] );
		};

		$container[ self::FETCH ] = function ( Container $container ) {
			return new Processors\Product_ID_Fetcher( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::MARK ] = function ( Container $container ) {
			return new Processors\Deleted_Product_Marker();
		};

		$container[ self::QUEUE ] = function ( Container $container ) {
			return new Processors\Queue_Runner( $container[ Api::FACTORY ]->catalog(), $container[ Api::FACTORY ]->channels(), $container[ self::BATCH_SIZE ], 10 );
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

		// Get a list of all products already listed in the channel
		$listings = $this->create_callback( 'process_listings', function () use ( $container ) {
			$container[ self::LISTING ]->run();
		} );

		// Make sure that the channel is fully initialized with products. May take multiple batches
		$channel = $this->create_callback( 'process_channel', function () use ( $container ) {
			$container[ self::CHANNEL ]->run();
		} );

		$categories = $this->create_callback( 'sync_categories', function () use ( $container ) {
			$container[ self::CATEGORIES ]->run();
		} );

		$brands = $this->create_callback( 'sync_brands', function () use ( $container ) {
			$container[ self::BRANDS ]->run();
		} );

		// Import product IDs. May take multiple batches
		$fetch = $this->create_callback( 'process_fetch', function () use ( $container ) {
			$container[ self::FETCH ]->run();
		} );

		// Any products no longer coming from the api should be deleted
		$mark = $this->create_callback( 'process_mark', function () use ( $container ) {
			$container[ self::MARK ]->run();
		} );

		// Process the queue we've established.
		$queue = $this->create_callback( 'process_queue', function () use ( $container ) {
			$container[ self::QUEUE ]->run();
		} );

		// Get the store info (currency, units).
		$store = $this->create_callback( 'fetch_store', function () use ( $container ) {
			$container[ self::STORE ]->run();
		} );

		// Cleanup
		$cleanup = $this->create_callback( 'process_cleanup', function () use ( $container ) {
			$container[ self::CLEANUP ]->run();
		} );

		$container[ self::TASK_LIST ] = [
			new Task_Definition( $start, 10, Runner\Status::STARTED ),
			new Task_Definition( $store, 20, Runner\Status::FETCHED_STORE, [ Runner\Status::FETCHING_STORE ], __( 'Fetching currency settings', 'bigcommerce' ) ),
			new Task_Definition( $listings, 30, Runner\Status::FETCHED_LISTING_IDS, [ Runner\Status::FETCHING_LISTING_IDS ], __( 'Fetching existing listings from the BigCommerce API', 'bigcommerce' ) ),
			new Task_Definition( $channel, 40, Runner\Status::INITIALIZED_CHANNEL, [ Runner\Status::INITIALIZING_CHANNEL ], __( 'Adding listings to the channel', 'bigcommerce' ) ),
			new Task_Definition( $categories, 44, Runner\Status::UPDATED_CATEGORIES, [ Runner\Status::UPDATING_CATEGORIES ], __( 'Updating Categories', 'bigcommerce' ) ),
			new Task_Definition( $brands, 46, Runner\Status::UPDATED_BRANDS, [ Runner\Status::UPDATING_BRANDS ], __( 'Updating Brands', 'bigcommerce' ) ),
			new Task_Definition( $fetch, 50, Runner\Status::FETCHED_PRODUCT_IDS, [ Runner\Status::FETCHING_PRODUCT_IDS ], __( 'Identifying products to import from the BigCommerce API', 'bigcommerce' ) ),
			new Task_Definition( $mark, 60, Runner\Status::MARKED_DELETED_PRODUCTS, [ Runner\Status::MARKING_DELETED_PRODUCTS ], __( 'Identifying products to remove from WordPress', 'bigcommerce' ) ),
			new Task_Definition( $queue, 70, Runner\Status::PROCESSED_QUEUE, [ Runner\Status::PROCESSING_QUEUE ], __( 'Importing products', 'bigcommerce' ) ),
			new Task_Definition( $cleanup, 100, Runner\Status::COMPLETED, [ Runner\Status::CLEANING ], __( 'Wrapping up', 'bigcommerce' ) ),
		];


		$container[ self::TASK_MANAGER ] = function ( Container $container ) {
			$manager = new Task_Manager();

			foreach ( $container[ self::TASK_LIST ] as $task ) {
				$manager->register( $task );
			}

			return $manager;
		};

		add_action( 'bigcommerce/import/run', function ( $status ) use ( $container ) {
			$container[ self::TASK_MANAGER ]->run_next( $status );
		} );

		$error = $this->create_callback( 'process_error', function () use ( $container ) {
			$container[ self::ERROR ]->run();
		} );
		add_action( 'bigcommerce/import/error', $error, 10, 0 );

	}
}