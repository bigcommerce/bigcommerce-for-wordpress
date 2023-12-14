<?php

namespace BigCommerce\Logging;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class Log
 *
 * @package BigCommerce\Logging
 */
class Error_Log {
	const EMERGENCY    = 'emergency';
	const ALERT        = 'alert';
	const CRITICAL     = 'critical';
	const ERROR        = 'error';
	const WARNING      = 'warning';
	const NOTICE       = 'notice';
	const INFO         = 'info';
	const DEBUG        = 'debug';
	const MAX_SIZE     = 25;
	const ALLOWED_LOGS = [
		'debug',
		'webhooks',
		'manager'
	];
	/**
	 * @var Logger
	 */
	public $log;

	public $webhook_log;

	/**
	 * @var string This log file path
	 */
	public $log_path;

	/**
	 * @var string This log webhooks file path
	 */
	public $log_folder_path;
	/**
	 * Log constructor.
	 *
	 * @param string $log_path File system path to the log file
	 */
	public function __construct( $log_path, $log_folder_path ) {
		$this->log_path        = $log_path;
		$this->log_folder_path = $log_folder_path;
	}

	/**
	 * Set up the import errors log
	 */
	public function init_log( $path = '' ) {
		if ( empty( $path ) && isset( $this->log ) ) {
			return;
		}

		if ( ! empty( $path ) && isset( $this->{$path} ) ) {
			return;
		}

		$this->init_log_dir();

		// Format lines as json objects
		$formatter = apply_filters( 'bigcommerce/logger/formatter', new \Monolog\Formatter\LineFormatter() );

		// Logger message
		$logger_name = apply_filters( 'bigcommerce/logger/channel', 'BigCommerce' );
		if ( empty( $path ) ) {
			$this->log = new Logger( $logger_name );
		} else {
			$this->{$path} = new Logger( $logger_name . '-' . $path );
		}

		$logger_level = $this->log_level();

		try {
			$path_to_log = empty( $path ) ? $this->log_path : $this->log_folder_path . $path . '.log';
			$handler     = apply_filters( 'bigcommerce/logger/handler', new StreamHandler( $path_to_log, $logger_level ) );

			// Logger Handler
			$handler->setFormatter( $formatter );

			if ( empty( $path ) ) {
				$this->log->pushHandler( $handler );

				return;
			}

			$this->{$path}->pushHandler( $handler );
		} catch ( \Exception $e ) {
			// log is not writeable
			error_log( __( 'Unable to initialize import error log', 'bigcommerce' ) );
		}
	}

	private function log_level() {
		/**
		 * Filter the logging level. Defaults to 'debug'.
		 *
		 * @param string|int The logging level, as either a PSR-3 LogLevel string or a Monolog integer.
		 */
		$level = apply_filters( 'bigcommerce/logger/level', self::DEBUG );
		switch ( $level ) {
			case self::DEBUG:
				return Logger::DEBUG;
			case self::INFO:
				return Logger::INFO;
			case self::NOTICE:
				return Logger::NOTICE;
			case self::WARNING:
				return Logger::WARNING;
			case self::ERROR:
				return Logger::ERROR;
			case self::CRITICAL:
				return Logger::CRITICAL;
			case self::ALERT:
				return Logger::ALERT;
			case self::EMERGENCY:
				return Logger::EMERGENCY;
			default:
				if ( is_numeric( $level ) ) {
					return (int) $level;
				}
				return Logger::DEBUG;
		}
	}

	/**
	 * Set up the import error log directory
	 */
	private function init_log_dir() {
		$file_log_dir = dirname( $this->log_path );

		// Check if Dir already exists, if not create using wp_mkdir_p
		if ( ! is_dir( $file_log_dir ) ) {
			wp_mkdir_p( $file_log_dir );

			// Builds a .htaccess file to prevent direct download
			$this->write_htaccess( $file_log_dir );
		}
	}

	/**
	 * Generates a .htaccess file to prevent direct downloads
	 *
	 * @param $directory_path
	 */
	private function write_htaccess( $directory_path ) {
		$htaccess_file = fopen( $directory_path . "/.htaccess", "a+" );

		$rulles = <<<HTACCESS
# BigCommerce Plugin Rule
<FilesMatch ".*">
    Order Allow,Deny
    Deny from All
</FilesMatch>
HTACCESS;

		fwrite( $htaccess_file, $rulles );
		fclose( $htaccess_file );
	}

	/**
	 * Get data from log
	 *
	 * @return array
	 */
	public function get_log_data() {
		$result = [
			'entries' => [],
		];

		if ( ! is_dir( $this->log_folder_path ) ) {
			return $result;
		}

		$files = scandir( $this->log_folder_path );

		if ( empty( $files ) ) {
			return $result;
		}

		foreach ( $files as $file ) {
			$file_path = $this->log_folder_path . $file;

			if ( ! is_file( $file_path ) ) {
				continue;
			}
			$name = pathinfo( $file_path, PATHINFO_FILENAME );

			if ( ! in_array( $name, self::ALLOWED_LOGS ) ) {
				continue;
			}

			if ( filesize( $file_path ) == 0 ) {
				$result['entries'][ $name ] = [
						'message'       => __( 'The log file is empty', 'bigcommerce' ),
						'log_content'   => '',
						'log_date_time' => '',
				];
				continue;
			}

			$log_content                = file_get_contents( $file_path );
			$log_creation_date_time     = date( "F-d-Y H:i:s.", filemtime( $file_path ) );
			$result['entries'][ $name ] = [
					'message'       => __( 'ok', 'bigcommerce' ),
					'log_content'   => $log_content,
					'log_date_time' => $log_creation_date_time,
			];
		}

		return $result;
	}

	/**
	 * Clean log file if it has size more than set in Troubleshooting_Diagnostics::LOG_FILE_SIZE
	 */
	public function truncate_log() {
		if ( ! is_dir( $this->log_folder_path ) ) {
			return;
		}

		$max_allowed_size = (int) get_option( Troubleshooting_Diagnostics::LOG_FILE_SIZE, self::MAX_SIZE );

		$files = scandir( $this->log_folder_path );

		if ( empty( $files ) ) {
			return;
		}

		// Truncate logs files by path
		foreach ( $files as $file ) {
			$file_path = $this->log_folder_path . $file;
			if ( ! is_file( $file_path ) ) {
				continue;
			}
			$extension = pathinfo( $file_path, PATHINFO_EXTENSION );

			if ( empty( $extension ) || $extension !== 'log' ) {
				continue;
			}

			$log_size = $this->get_log_size_mb( $file_path );

			if ( $log_size < $max_allowed_size ) {
				continue;
			}

			/**
			 * Clean log file contents
			 */
			$file = fopen( $file_path, "w" );

			fclose( $file );
		}
	}

	/**
	 * Get log file size in MB
	 * @return float|int
	 */
	public function get_log_size_mb( $path ) {
		if ( ! file_exists( $path ) ) {
			return 0;
		}

		/**
		 * Get log size in bytes
		 */
		$file_size = filesize( $path );

		/**
		 * Return the size of log file in MB
		 */
		return round( $file_size / 1024 / 1024, 1 );
	}


	/**
	 * Writes a log line into the log file
	 *
	 * @param int        $product_id
	 * @param CatalogApi $catalog_api
	 * @param \Exception $exception
	 *
	 * @action bigcommerce/import/product/error
	 */
	public function log_product_import_error( $product_id, CatalogApi $catalog_api, \Exception $exception ) {
		$message = __( 'Product import error', 'bigcommerce' );
		$context = [ $product_id, $catalog_api, $exception ];
		$this->log( self::WARNING, $message, $context );
	}

	public function log( $level, $message, $context, $path = '' ) {
		$this->init_log( $path );

		if ( ! is_array( $context ) ) {
			$context = [];
		}

		$handler = empty( $path ) ? $this->log : $this->{$path};

		switch ( $level ) {
			case self::EMERGENCY:
				$handler->emergency( $message, $context );
				break;
			case self::ALERT:
				$handler->alert( $message, $context );
				break;
			case self::CRITICAL:
				$handler->critical( $message, $context );
				break;
			case self::ERROR:
				$handler->error( $message, $context );
				break;
			case self::WARNING:
				$handler->warning( $message, $context );
				break;
			case self::NOTICE:
				$handler->notice( $message, $context );
				break;
			case self::INFO:
				$handler->info( $message, $context );
				break;
			case self::DEBUG:
			default:
				$handler->debug( $message, $context );
				break;
		}
	}

	public function add_log_to_diagnostics( $diagnostics ) {
		$logs = $this->get_log_data();

		if ( empty( $logs['entries'] ) ) {
			$diagnostics[] = [
				'label' => __( 'Error Logs', 'bigcommerce' ),
				'key'   => 'errorlogs',
				'value' => [
					'label' => __( 'Import Logs', 'bigcommerce' ),
					'key'   => 'importlogs',
					'value' => '',
				],
			];

			return $diagnostics;
		}

		$result = [
			'label' => __( 'Error Logs', 'bigcommerce' ),
			'key'   => 'errorlogs',
			'value' => [],
		];

		foreach ( $logs['entries'] as $name => $entry ) {
			$label             = $name === 'debug' ? __( 'Import Logs', 'bigcommerce' ) : __( sprintf( '%s Logs', ucfirst( $name ) ), 'bigcommerce' );
			$result['value'][] = [
				'label' => $label,
				'key'   => 'importlogs',
				'value' => $entry['log_content'],
			];
		}

		$diagnostics[] = $result;

		return $diagnostics;
	}
}
