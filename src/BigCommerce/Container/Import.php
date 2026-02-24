<?php


namespace BigCommerce\Container;


use BigCommerce\Import\Cache_Cleanup;
use BigCommerce\Import\Processors;
use BigCommerce\Import\Runner;
use BigCommerce\Import\Task_Definition;
use BigCommerce\Import\Task_Manager;
use BigCommerce\Import\Import_Type;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Manager\Manager;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Sections\Import as Import_Settings;
use BigCommerce\Taxonomies\Channel\Connections;
use Pimple\Container;

/** 
* This class handles the import process for BigCommerce data. It includes functionality
* to manage cron jobs, process various import tasks, handle batch sizes, and trigger 
* different import-related actions.
*/
class Import extends Provider {
   
   /**
	* The service identifier for the cron monitor used to check scheduled crons.
	* @var string
	*/
   const CRON_MONITOR = 'import.cron.monitor';

   /**
	* The service identifier for the cron runner responsible for processing cron jobs.
	* @var string
	*/
   const CRON_RUNNER = 'import.cron.runner';

   /**
	* The service identifier for the parallel runner responsible for asynchronous job execution.
	* @var string
	*/
   const PARALLEL_RUNNER = 'import.async.runner';

   /**
	* The service identifier for monitoring locks during the import process.
	* @var string
	*/
   const LOCK_MONITOR = 'import.lock.monitor';

   /**
	* The service identifier for the timeout setting used to control the import process timeout.
	* @var string
	*/
   const TIMEOUT = 'timeout';

   /**
	* The default customer group for the import process.
	* @var string
	*/
   const CUSTOMER_DEFAULT_GROUP = 'import.customer_default_group';

   /**
	* The service identifier for the storefront processor used in the import process.
	* @var string
	*/
   const MSF_STOREFRONT_PROCESSOR = 'import.msf_storefront_processor';

   /**
	* The service identifier for the task manager handling postponed tasks.
	* @var string
	*/
   const TASK_MANAGER = 'import.task_manager';

   /**
	* The service identifier for the list of tasks to be processed during the import.
	* @var string
	*/
   const TASK_LIST = 'import.task_list';

   /**
	* The service identifier for the cache cleanup process during import.
	* @var string
	*/
   const CACHE_CLEANUP = 'import.cache_cleanup';

   /**
	* The service identifier for managing channels during the import process.
	* @var string
	*/
   const CHANNEL_LIST = 'import.channel_list';

   /**
	* The service identifier for the batch size setting for the import process.
	* @var string
	*/
   const BATCH_SIZE = 'import.batch_size';

   /**
	* The service identifier for a larger batch size setting for the import process.
	* @var string
	*/
   const LARGE_BATCH_SIZE = 'import.large_batch_size';

   /**
	* The service identifier for managing postponed tasks after the import process.
	* @var string
	*/
   const POST_TASK_MANAGER = 'import.postponed_task_manager';

   /**
	* The service identifier for starting the import process.
	* @var string
	*/
   const START = 'import.start';

   /**
	* The service identifier for importing product listings during the import process.
	* @var string
	*/
   const LISTINGS = 'import.listings';

   /**
	* The service identifier for the channel configuration used in the import process.
	* @var string
	*/
   const CHANNEL = 'import.channel';

   /**
	* The service identifier for purging categories during the import process.
	* @var string
	*/
   const PURGE_CATEGORIES = 'import.purge.categories';

   /**
	* The service identifier for purging brands during the import process.
	* @var string
	*/
   const PURGE_BRANDS = 'import.purge.brands';

   /**
	* The service identifier for importing categories during the import process.
	* @var string
	*/
   const CATEGORIES = 'import.categories';

   /**
	* The service identifier for importing brands during the import process.
	* @var string
	*/
   const BRANDS = 'import.brands';

   /**
	* The service identifier for resizing images during the import process.
	* @var string
	*/
   const RESIZE = 'import.resize';

   /**
	* The service identifier for importing products during the import process.
	* @var string
	*/
   const PRODUCTS = 'import.products';

   /**
	* The service identifier for marking deleted products during the import process.
	* @var string
	*/
   const MARK = 'import.mark_deleted';

   /**
	* The service identifier for managing the import queue.
	* @var string
	*/
   const QUEUE = 'import.queue';

   /**
	* The service identifier for managing the store configuration during the import process.
	* @var string
	*/
   const STORE = 'import.store';

   /**
	* The service identifier for handling currency settings during the import process.
	* @var string
	*/
   const CURRENCIES = 'import.currencies';

   /**
	* The service identifier for the cleanup process during the import.
	* @var string
	*/
   const CLEANUP = 'import.cleanup';

   /**
	* The service identifier for cleaning up product-related data during the import process.
	* @var string
	*/
   const PRODUCT_CLEANUP = 'import.product_cleanup';

   /**
	* The service identifier for tracking the import status.
	* @var string
	*/
   const IMPORT_STATUS = 'import.import_status';

   /**
	* The service identifier for handling import errors.
	* @var string
	*/
   const ERROR = 'import.error';

   /**
	* The service identifier for specifying the type of import process.
	* @var string
	*/
   const IMPORT_TYPE = 'import.type';

   /**
	* The service identifier for the headless processor used during import.
	* @var string
	*/
   const HEADLESS_PROCESSOR = 'headless.processor';

   /**
	* Registers the import-related services and actions within the container.
	* 
	* @param Container $container The container instance for dependency injection.
	* 
	* @return void
	*/
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

		$container[ self::POST_TASK_MANAGER ] = function ( Container $container ) {
			return new Manager();
		};

		$container[ self::CRON_MONITOR ] = function ( Container $container ) {
			return new Runner\Cron_Monitor();
		};

		$container[ self::CRON_RUNNER ] = function ( Container $container ) {
			return new Runner\Cron_Runner();
		};

		$container[ self::PARALLEL_RUNNER ] = function ( Container $container ) {
			return new Runner\AsyncProcessing_Runner();
		};

		add_action( 'init', $this->create_callback( 'cron_init', function () use ( $container ) {
			if ( $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::CRON_MONITOR ]->check_for_scheduled_crons();
			}
		} ), 10, 0 );

		$container[ self::LOCK_MONITOR ] = function ( Container $container ) {
			return new Runner\Lock_Monitor( $container[ self::TIMEOUT ] );
		};

		add_filter( 'cron_schedules', function ( $schedules ) use ( $container ) {
			return $container[ self::POST_TASK_MANAGER ]->add_interval( $schedules );
		}, 10, 1 );

		add_action( 'init', function ( $schedules ) use ( $container ) {
			return $container[ self::POST_TASK_MANAGER ]->maybe_schedule_queue_processor( $schedules );
		}, 10, 1 );

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

		add_action( Runner\AsyncProcessing_Runner::CONTINUE_IMPORT, $this->create_callback( 'async_import_run', function () use ( $container ) {
			if ( ! Import_Status::is_parallel_run_enabled() ) {
				return;
			}
			$container[ self::PARALLEL_RUNNER ]->run();
		} ), 10, 0 );
	
		add_action( Processors\Cleanup::CLEAN_USERS_TRANSIENT, $this->create_callback( 'clean_users_group_transient', function () use ( $container ) {
			$container[ self::CLEANUP ]->clean_customer_group_transients();
		} ), 10, 0 );

		add_action( Processors\Cleanup::PURGE_PRODUCTS, $this->create_callback( 'purge_bc_deleted_products', function () use ( $container ) {
			$container[ self::PRODUCT_CLEANUP ]->run();
		} ), 10, 0 );

		add_action( Processors\Cleanup::CLEAN_PRODUCTS_TRANSIENT, $this->create_callback( 'clean_products_data_transient', function ( $offset = 0, $partially = false ) use ( $container ) {
			$container[ self::CLEANUP ]->refresh_products_transient( $offset, $partially );
		} ), 10, 1 );

		add_action( Manager::CRON_PROCESSOR, $this->create_callback( 'postponed_task_processing', function () use ( $container ) {
			$container[ self::POST_TASK_MANAGER ]->run_tasks();
		} ), 10, 1 );
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

		$container[ self::CUSTOMER_DEFAULT_GROUP ] = function ( Container $container ) {
			return new Processors\Default_Customer_Group( $container[ Api::FACTORY ]->price_lists() );
		};

		$container[ self::MSF_STOREFRONT_PROCESSOR ] = function ( Container $container ) {
			return new Processors\Storefront_Processor( $container[ Api::FACTORY ]->storefront_settings(), new Connections() );
		};

		$container[ self::PURGE_CATEGORIES ] = function ( Container $container ) {
			return new Processors\Category_Purge( $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::PURGE_BRANDS ] = function ( Container $container ) {
			return new Processors\Brand_Purge( $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::CATEGORIES ] = function ( Container $container ) {
			return new Processors\Category_Import( $container[ Api::FACTORY ]->catalog(), $container[ GraphQL::GRAPHQL_REQUESTOR ], $container[ self::BATCH_SIZE ] );
		};

		$container[ self::HEADLESS_PROCESSOR ] = function ( Container $container ) {
			return function ( $channel_term ) use ( $container ) {
				return new Processors\Headless_Product_Processor( $container[ Api::FACTORY ]->catalog(),  $container[ self::IMPORT_STATUS ], $container[ GraphQL::GRAPHQL_REQUESTOR ], $channel_term, 50 );
			};
		};

		$container[ self::BRANDS ] = function ( Container $container ) {
			return new Processors\Brand_Import( $container[ Api::FACTORY ]->catalog(), $container[ GraphQL::GRAPHQL_REQUESTOR ], $container[ self::BATCH_SIZE ] );
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
				return new Processors\Channel_Initializer( $container[ Api::FACTORY ]->channels(), $container[ Api::FACTORY ]->catalog(), $channel_term, $container[ self::BATCH_SIZE ] );
			};
		};

		$container[ self::PRODUCTS ] = function ( Container $container ) {
			return new Processors\Product_Data_Fetcher( $container[ Api::FACTORY ]->catalog(), $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::MARK ] = function ( Container $container ) {
			return new Processors\Deleted_Product_Marker();
		};

		$container[ self::QUEUE ] = function ( Container $container ) {
			return new Processors\Queue_Runner( $container[ Api::FACTORY ]->catalog(), $container[ self::BATCH_SIZE ], 5 );
		};

		$container[ self::STORE ] = function ( Container $container ) {
			return new Processors\Store_Settings( $container[ Api::FACTORY ]->store(), $container[ self::CUSTOMER_DEFAULT_GROUP ], $container[ self::MSF_STOREFRONT_PROCESSOR ], $container[ Api::FACTORY ]->storefront_settings() );
		};

		$container[ self::CURRENCIES ] = function ( Container $container ) {
			return new Processors\Currencies( $container[ Api::FACTORY ]->currencies(), $container[ Api::FACTORY ]->currenciesV3(), new Connections() );
		};

		$container[ self::PRODUCT_CLEANUP ] = function ( Container $container ) {
			return new Processors\ProductCleanup( $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::CLEANUP ] = function ( Container $container ) {
			return new Processors\Cleanup( $container[ Api::CACHE_HANDLER ], $container[ self::LARGE_BATCH_SIZE ] );
		};

		$container[ self::ERROR ] = function ( Container $container ) {
			return new Processors\Error_Handler();
		};

		$container[ self::CHANNEL_LIST ] = function ( Container $container ) {
			$connections = new Connections();
			return $connections->active();
		};

		$container[ self::IMPORT_STATUS ] = function ( Container $container ) {
			return new Runner\Status();
		};

		$start = $this->create_callback( 'process_start', function () use ( $container ) {
			$container[ self::START ]->run();
			// Run pre import cleanup process. Set abort = false, pre_import = true
			$container[ self::CLEANUP ]->run( false, true );
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


			if ( Import_Type::is_traditional_import() ) {
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

				$list[] = new Task_Definition( $this->create_callback( 'process_fetch', function () use ( $container ) {
					$container[ self::PRODUCTS ]->run();
				} ), 50, Runner\Status::FETCHED_PRODUCTS, [ Runner\Status::FETCHING_PRODUCTS ], __( 'Fetching product data from the BigCommerce API', 'bigcommerce' ) );

				$list[] = new Task_Definition( $this->create_callback( 'process_mark', function () use ( $container ) {
					$container[ self::MARK ]->run();
				} ), 60, Runner\Status::MARKED_DELETED_PRODUCTS, [ Runner\Status::MARKING_DELETED_PRODUCTS ], __( 'Identifying posts to remove from WordPress', 'bigcommerce' ) );

				$list[] = new Task_Definition( $this->create_callback( 'process_queue', function () use ( $container ) {
					$container[ self::QUEUE ]->run();
				} ), 70, Runner\Status::PROCESSED_QUEUE, [ Runner\Status::PROCESSING_QUEUE ], __( 'Importing products', 'bigcommerce' ) );
			} else {
				foreach ( $container[ self::CHANNEL_LIST ] as $channel_term ) {
					$suffix = sprintf( '-%d', $channel_term->term_id );
					$list[] = new Task_Definition( $this->create_callback( 'process_headless_products' . $suffix, function () use ( $container, $channel_term ) {
						$container[ self::HEADLESS_PROCESSOR ]( $channel_term )->run();
					} ), 30, Runner\Status::FETCHED_PRODUCTS . $suffix, [ Runner\Status::FETCHING_PRODUCTS . $suffix ], sprintf( __( 'Fetching products data from the BigCommerce API for channel %s', 'bigcommerce' ), esc_html( $channel_term->name ) ) );
				}
			}

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

		$container[ self::IMPORT_TYPE ] = function ( Container $container ) {
			return new Import_Type( $container[ Api::FACTORY ]->catalog() );
		};

		add_filter( 'bigcommerce_modified_product_ids', $this->create_callback( 'modified_product_ids', function ( $modified_product_ids ) use ( $container ) {
			return $container[ self::IMPORT_TYPE ]->fetch_modified_product_ids();
		} ) );

		add_filter( 'bigcommerce/import/task_list', $this->create_callback( 'filter_import_type_task_list', function ( $task_list ) use ( $container ) {
			return $container[ self::IMPORT_TYPE ]->filter_task_list( $task_list );
		} ) );

		add_filter( 'bigcommerce/import/term/data', $this->create_callback( 'filter_import_parent_category_data', function ( $data, $bc_category_id ) use ( $container ) {
			$category_data = $container[ self::CATEGORIES ]->get_category_data( $bc_category_id );
			return $category_data->getData();
		} ), 10, 2 );

	}
}
