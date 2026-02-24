<?php

namespace BigCommerce\Container;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use Pimple\Container;
use BigCommerce\Logging\Error_Log as Logger;

/**
 * This class is responsible for setting up logging functionality in the BigCommerce container.
 * It registers services for logging, defines constants for log paths, and handles log-related actions 
 * during the BigCommerce import process, such as logging errors and diagnostics.
 *
 * @package BigCommerce\Container
 */
class Log extends Provider {

    /**
     * Constant for the logger service.
     *
     * This constant defines the key used to retrieve the logger instance from the container.
     *
     * @var string
     */
	const LOGGER          = 'logger.log';

    /**
     * Constant for the log file path.
     *
     * This constant defines the key used to retrieve the log file path from the container.
     *
     * @var string
     */
	const LOG_PATH        = 'logger.log_path';

    /**
     * Constant for the log folder path.
     *
     * This constant defines the key used to retrieve the log folder path from the container.
     *
     * @var string
     */
	const LOG_FOLDER_PATH = 'logger.log_folder_path';

    /**
     * Registers logging-related services and actions in the container.
     *
     * This method sets up the necessary services for logging, such as the log file path, 
     * log folder path, and logger instance. It also registers various actions and filters 
     * for logging during the BigCommerce import process.
     *
     * @param Container $container The container instance used to register the services.
     */
	public function register( Container $container ) {
		$container[ self::LOG_PATH ] = function ( Container $container ) {
			$log_path = bigcommerce_get_env( 'BIGCOMMERCE_DEBUG_LOG' );
			if ( empty( $log_path ) ) {
				$log_path = trailingslashit( wp_upload_dir()[ 'basedir' ] ) . 'logs/bigcommerce/debug.log';
			}

			/**
			 * Filter the path to the debug logging file
			 *
			 * @param string $log_path The full file system path to the log file
			 */
			return apply_filters( 'bigcommerce/logger/path', $log_path );
		};

		$container[ self::LOG_FOLDER_PATH ] = function ( Container $container ) {
			$log_path = trailingslashit( wp_upload_dir()['basedir'] ) . 'logs/bigcommerce/';

			/**
			 * Filter the path to the debug logging file
			 *
			 * @param string $log_path The full file system path to the log file
			 */
			return apply_filters( 'bigcommerce/logger/custom_path', $log_path );
		};

		$container[ self::LOGGER ] = function ( Container $container ) {
			return new Logger( $container[ self::LOG_PATH ], $container[ self::LOG_FOLDER_PATH ] );
		};


		// Check if the import errors option is active or not, if true, loads the action
		if ( get_option( Troubleshooting_Diagnostics::LOG_ERRORS, true ) ) {
			add_action( 'bigcommerce/import/start', $this->create_callback( 'truncate_log', function () use ( $container ) {
				$container[ self::LOGGER ]->truncate_log();
			} ), 9, 0 );
			
			add_action( 'bigcommerce/import/product/error', $this->create_callback( 'log_product_import_error', function ( $product_id, CatalogApi $catalog_api, \Exception $exception ) use ( $container ) {
				$container[ self::LOGGER ]->log_product_import_error( $product_id, $catalog_api, $exception );
			} ), 10, 3 );

			add_filter( 'bigcommerce/diagnostics', $this->create_callback( 'output_log_to_diagnostics', function ( $diagnostics ) use ( $container ) {
				return $container[ self::LOGGER ]->add_log_to_diagnostics( $diagnostics );
			} ), 10, 1 );

			$log = $this->create_callback( 'log', function ( $level = Error_Log::INFO, $message = '', $context = [], $path = '' ) use ( $container ) {
				$container[ self::LOGGER ]->log( $level, $message, $context, $path );
			} );

			add_action( 'bigcommerce/log', $log, 10, 4 );

			add_action( 'bigcommerce/import/log', $log, 10, 3 );

			add_action( 'bigcommerce/import/error', $this->create_callback( 'log_import_error', function ( $message, $context = [] ) use ( $container ) {
				$container[ self::LOGGER ]->log( Error_Log::ERROR, $message, $context );
			} ), 10, 2 );
		}
	}
}
