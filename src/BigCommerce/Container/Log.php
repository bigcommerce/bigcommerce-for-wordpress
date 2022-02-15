<?php

namespace BigCommerce\Container;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use Pimple\Container;
use BigCommerce\Logging\Error_Log as Logger;

class Log extends Provider {

	const LOGGER   = 'logger.log';
	const LOG_PATH = 'logger.log_path';

	/**
	 * @param Container $container
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

		$container[ self::LOGGER ] = function ( Container $container ) {
			return new Logger( $container[ self::LOG_PATH ] );
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

			$log = $this->create_callback( 'log', function ( $level = Error_Log::INFO, $message = '', $context = [] ) use ( $container ) {
				$container[ self::LOGGER ]->log( $level, $message, $context );
			} );
			add_action( 'bigcommerce/log', $log, 10, 3 );
			add_action( 'bigcommerce/import/log', $log, 10, 3 );

			add_action( 'bigcommerce/import/error', $this->create_callback( 'log_import_error', function ( $message, $context = [] ) use ( $container ) {
				$container[ self::LOGGER ]->log( Error_Log::ERROR, $message, $context );
			} ), 10, 2 );
		}
	}
}