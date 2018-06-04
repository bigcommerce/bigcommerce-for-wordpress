<?php


namespace BigCommerce\CLI\Documentation;


use BigCommerce\CLI\Command;
use WP_CLI;
use WP_Parser\WP_CLI_Logger;

class Import_Docs extends Command {
	private $plugin_dir;

	public function __construct( $plugin_dir ) {
		$this->plugin_dir = $plugin_dir;
	}

	protected function command() {
		return 'docs import';
	}

	protected function description() {
		return __( 'Imports plugin documentation', 'bigcommerce' );
	}

	protected function arguments() {
		return [
			[
				'type'        => 'positional',
				'name'        => 'file',
				'optional'    => false,
				'description' => __( 'Path to the JSON file to import', 'bigcommerce' ),
			],
		];
	}

	public function run( $args, $assoc_args ) {
		if ( ! class_exists( '\WP_Parser\Importer' ) ) {
			WP_CLI::error( __( 'Please install and activate WP Parser from https://github.com/WordPress/phpdoc-parser before importing documentation.', 'bigcommerce' ) );
			exit;
		}

		$file = reset( $args );

		// Get the data from the <file>, and check it's valid.
		$phpdoc = false;

		if ( is_readable( $file ) ) {
			$phpdoc = file_get_contents( $file );
		}

		if ( ! $phpdoc ) {
			WP_CLI::error( sprintf( "Can't read %1\$s. Does the file exist?", $file ) );
			exit;
		}

		$phpdoc = json_decode( $phpdoc, true );
		if ( is_null( $phpdoc ) ) {
			WP_CLI::error( sprintf( "JSON in %1\$s can't be decoded", $file ) );
			exit;
		}

		// Import data
		$this->run_import( $phpdoc );
	}

	private function run_import( $data ) {
		if ( ! wp_get_current_user()->exists() ) {
			WP_CLI::error( 'Please specify a valid user: --user=<id|login>' );
			exit;
		}

		// Run the importer
		$importer = new Data_Importer();
		$importer->setLogger( new WP_CLI_Logger() );
		$importer->import( $data, true, false );

		WP_CLI::line();
	}
}