<?php


namespace BigCommerce\Container;


use BigCommerce\Import\Cache_Cleanup;
use BigCommerce\Import\Processors;
use BigCommerce\Import\Runner;
use BigCommerce\Import\Task_Definition;
use BigCommerce\Import\Task_Manager;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Sections\Import as Import_Settings;
use BigCommerce\Taxonomies\Channel\Connections;
use Pimple\Container;

class Import extends Provider {
	const CRON_MONITOR = 'import.cron.monitor';
	const CRON_RUNNER  = 'import.cron.runner';
	const LOCK_MONITOR = 'import.lock.monitor';
	const TIMEOUT      = 'timeout';

	const TASK_MANAGER  = 'import.task_manager';
	const TASK_LIST     = 'import.task_list';
	const CACHE_CLEANUP = 'import.cache_cleanup';
	const CHANNEL_LIST  = 'import.channel_list';

	const BATCH_SIZE       = 'import.batch_size';
	const LARGE_BATCH_SIZE = 'import.large_batch_size';

	const START            = 'import.start';
	const LISTINGS         = 'import.listings';
	const CHANNEL          = 'import.channel';
	const PURGE_CATEGORIES = 'import.purge.categories';
	const PURGE_BRANDS     = 'import.purge.brands';
	const CATEGORIES       = 'import.categories';
	const BRANDS           = 'import.brands';
	const RESIZE           = 'import.resize';
	const PRODUCTS         = 'import.products';
	const MARK             = 'import.mark_deleted';
	const QUEUE            = 'import.queue';
	const STORE            = 'import.store';
	const CURRENCIES       = 'import.currencies';
	const CLEANUP          = 'import.cleanup';
	const ERROR            = 'import.error';

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
			return new Runner\Cron_Monitor();
		};

		$container[ self::CRON_RUNNER ] = function ( Container $container ) {
			return new Runner\Cron_Runner();
		};

		add_action( 'init', $this->create_callback( 'cron_init', function () use ( $container ) {
			if ( $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::CRON_MONITOR ]->check_for_scheduled_crons();
			}
		} ), 10, 0 );

		$container[ self::LOCK_MONITOR ] = function ( Container $container ) {
			return new Runner\Lock_Monitor( $container[ self::TIMEOUT ] );
		};

		add_action( 'init', $this->create_callback( 'lock_expiration', function () use ( $container ) {
			$container[ self::LOCK_MONITOR ]->check_for_expired_lock();
		} ), 0, 0 );

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

			return min( $batch, 25 );
		};

		$container[ self::LARGE_BATCH_SIZE ] = function ( Container $container ) {
			return min( $container[ self::BATCH_SIZE ] * 10, 200 );
		};

		$container[ self::START ] = function ( Container $container ) {
			return new Processors\Start_Import();
		};

		$container[ self::PURGE_CATEGORIES ] = function ( Container $container ) {
			return new Processors\Category_Purge( $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::PURGE_BRANDS ] = function ( Container $container ) {
			return new Processors\Brand_Purge( $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::CATEGORIES ] = function ( Container $container ) {
			return new Processors\Category_Import( $container[ Api::FACTORY ]->catalog(), $container[ self::BATCH_SIZE ] );
		};

		$container[ self::BRANDS ] = function ( Container $container ) {
			return new Processors\Brand_Import( $container[ Api::FACTORY ]->catalog(), $container[ self::BATCH_SIZE ] );
		};

		$container[ self::RESIZE ] = function ( Container $container ) {
			return new Processors\Image_Resizer( $container[ self::BATCH_SIZE ] );
		};

		$container[ self::LISTINGS ] = function ( Container $container ) {
			return function ( $channel_term ) use ( $container ) {
				return new Processors\Listing_Fetcher( $container[ Api::FACTORY ]->channels(), $channel_term, $container[ self::LARGE_BATCH_SIZE ] );
			};
		};

		$container[ self::CHANNEL ] = function ( Container $container ) {
			return function ( $channel_term ) use ( $container ) {
				return new Processors\Channel_Initializer( $container[ Api::FACTORY ]->channels(), $container[ Api::FACTORY ]->catalog(), $channel_term, $container[ self::LARGE_BATCH_SIZE ] );
			};
		};

		$container[ self::PRODUCTS ] = function ( Container $container ) {
			return new Processors\Product_Data_Fetcher( $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::MARK ] = function ( Container $container ) {
			return new Processors\Deleted_Product_Marker();
		};

		$container[ self::QUEUE ] = function ( Container $container ) {
			return new Processors\Queue_Runner( $container[ Api::FACTORY ]->catalog(), $container[ self::BATCH_SIZE ], 10 );
		};

		$container[ self::STORE ] = function ( Container $container ) {
			return new Processors\Store_Settings( $container[ Api::FACTORY ]->store() );
		};

		$container[ self::CURRENCIES ] = function ( Container $container ) {
			return new Processors\Currencies( $container[ Api::FACTORY ]->currencies() );
		};

		$container[ self::CLEANUP ] = function ( Container $container ) {
			return new Processors\Cleanup( $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::ERROR ] = function ( Container $container ) {
			return new Processors\Error_Handler();
		};

		$container[ self::CHANNEL_LIST ] = function ( Container $container ) {
			$connections = new Connections();
			return $connections->active();
		};

		$start = $this->create_callback( 'process_start', function () use ( $container ) {
			$container[ self::START ]->run();
		} );
		add_action( 'bigcommerce/import/start', $start, 10, 0 );

		$container[ self::TASK_LIST ] = function ( Container $container ) {
			$list = [];

			$list[] = new Task_Definition( $this->process_start, 10, Runner\Status::STARTED );

			$list[] = new Task_Definition( $this->create_callback( 'fetch_store', function () use ( $container ) {
				$container[ self::STORE ]->run();
			} ), 20, Runner\Status::FETCHED_STORE, [ Runner\Status::FETCHING_STORE ], __( 'Fetching store information', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'fetch_currencies', function () use ( $container ) {
				$container[ self::CURRENCIES ]->run();
			} ), 21, Runner\Status::FETCHED_CURRENCIES, [ Runner\Status::FETCHING_CURRENCIES ], __( 'Retrieving currency settings', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'purge_categories', function () use ( $container ) {
				$container[ self::PURGE_CATEGORIES ]->run();
			} ), 22, Runner\Status::PURGED_CATEGORIES, [ Runner\Status::PURGING_CATEGORIES ], __( 'Removing Categories', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'purge_brands', function () use ( $container ) {
				$container[ self::PURGE_BRANDS ]->run();
			} ), 23, Runner\Status::PURGED_BRANDS, [ Runner\Status::PURGING_BRANDS ], __( 'Removing Brands', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'sync_categories', function () use ( $container ) {
				$container[ self::CATEGORIES ]->run();
			} ), 24, Runner\Status::UPDATED_CATEGORIES, [ Runner\Status::UPDATING_CATEGORIES ], __( 'Updating Categories', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'sync_brands', function () use ( $container ) {
				$container[ self::BRANDS ]->run();
			} ), 26, Runner\Status::UPDATED_BRANDS, [ Runner\Status::UPDATING_BRANDS ], __( 'Updating Brands', 'bigcommerce' ) );


			$list[] = new Task_Definition( $this->create_callback( 'resize_images', function () use ( $container ) {
				$container[ self::RESIZE ]->run();
			} ), 27, Runner\Status::RESIZED_IMAGES, [ Runner\Status::RESIZING_IMAGES ], __( 'Regenerating Product Images', 'bigcommerce' ) );

			foreach ( $container[ self::CHANNEL_LIST ] as $channel_term ) {
				$suffix = sprintf( '-%d', $channel_term->term_id );
				$list[] = new Task_Definition( $this->create_callback( 'process_listings' . $suffix, function () use ( $container, $channel_term ) {
					$container[ self::LISTINGS ]( $channel_term )->run();
				} ), 30, Runner\Status::FETCHED_LISTINGS . $suffix, [ Runner\Status::FETCHING_LISTINGS . $suffix ], sprintf( __( 'Fetching existing listings from the BigCommerce API for channel %s', 'bigcommerce' ), esc_html( $channel_term->name ) ) );

				$list[] = new Task_Definition( $this->create_callback( 'process_channel' . $suffix, function () use ( $container, $channel_term ) {
					$container[ self::CHANNEL ]( $channel_term )->run();
				} ), 40, Runner\Status::INITIALIZED_CHANNEL . $suffix, [ Runner\Status::INITIALIZING_CHANNEL . $suffix ], sprintf( __( 'Adding listings to channel %s', 'bigcommerce' ), esc_html( $channel_term->name ) ) );
			}

			$list[] = new Task_Definition( $this->create_callback( 'process_fetch', function () use ( $container, $channel_term ) {
				$container[ self::PRODUCTS ]->run();
			} ), 50, Runner\Status::FETCHED_PRODUCTS, [ Runner\Status::FETCHING_PRODUCTS ], __( 'Fetching product data from the BigCommerce API', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'process_mark', function () use ( $container, $channel_term ) {
				$container[ self::MARK ]->run();
			} ), 60, Runner\Status::MARKED_DELETED_PRODUCTS, [ Runner\Status::MARKING_DELETED_PRODUCTS ], __( 'Identifying posts to remove from WordPress', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'process_queue', function () use ( $container ) {
				$container[ self::QUEUE ]->run();
			} ), 70, Runner\Status::PROCESSED_QUEUE, [ Runner\Status::PROCESSING_QUEUE ], __( 'Importing products', 'bigcommerce' ) );

			$list[] = new Task_Definition( $this->create_callback( 'process_cleanup', function () use ( $container ) {
				$container[ self::CLEANUP ]->run();
			} ), 100, Runner\Status::COMPLETED, [ Runner\Status::CLEANING ], __( 'Wrapping up', 'bigcommerce' ) );

			/**
			 * Filter the tasks that will be registered for the product import
			 *
			 * @param Task_Definition[] $list The list of tasks to register
			 */
			return apply_filters( 'bigcommerce/import/task_list', $list );
		};

		$container[ self::TASK_MANAGER ] = function ( Container $container ) {
			$manager = new Task_Manager();

			foreach ( $container[ self::TASK_LIST ] as $task ) {
				$manager->register( $task );
			}

			/**
			 * Triggered when the task manager for the import has finished initializing
			 *
			 * @param Task_Manager $manager The task manager object
			 */
			do_action( 'bigcommerce/import/task_manager/init', $manager );

			return $manager;
		};

		add_action( 'bigcommerce/import/run', function ( $status ) use ( $container ) {
			try {
				$container[ self::TASK_MANAGER ]->run_next( $status );
			} catch ( \Exception $e ) {
				do_action( 'bigcommerce/import/error', $e->getMessage(), [] );
				do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );
			}
		} );

		$error = $this->create_callback( 'process_error', function () use ( $container ) {
			$container[ self::ERROR ]->run();
		} );
		add_action( 'bigcommerce/import/error', $error, 10, 0 );


		$container[ self::CACHE_CLEANUP ] = function ( Container $container ) {
			return new Cache_Cleanup();
		};

		$flush_option_caches = $this->create_callback( 'flush_option_caches', function () use ( $container ) {
			$container[ self::CACHE_CLEANUP ]->flush_caches();
		} );

		add_action( 'bigcommerce/import/before', $flush_option_caches, 0, 0 );
		add_action( 'bigcommerce/import/after', $flush_option_caches, 0, 0 );

	}
}
