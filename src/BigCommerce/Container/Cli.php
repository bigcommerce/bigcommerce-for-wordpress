<?php


namespace BigCommerce\Container;


use BigCommerce\CLI\Documentation\Build_Docs;
use BigCommerce\CLI\Documentation\Import_Docs;
use BigCommerce\CLI\Import_Products;
use BigCommerce\CLI\Update_Country_Cache;
use Pimple\Container;

class Cli extends Provider {
	const IMPORT_PRODUCTS  = 'cli.import_products';
	const UPDATE_COUNTRIES = 'cli.update_countries';
	const DOCS_BUILD       = 'cli.documentation.build';
	const DOCS_IMPORT      = 'cli.documentation.import';

	public function register( Container $container ) {
		$container[ self::IMPORT_PRODUCTS ] = function ( Container $container ) {
			return new Import_Products();
		};

		$container[ self::UPDATE_COUNTRIES ] = function ( Container $container ) {
			return new Update_Country_Cache( $container[ Accounts::COUNTRIES_PATH ] );
		};

		$container[ self::DOCS_BUILD ] = function ( Container $container ) {
			return new Build_Docs( dirname( $container[ 'plugin_file' ] ) );
		};

		$container[ self::DOCS_IMPORT ] = function ( Container $container ) {
			return new Import_Docs( dirname( $container[ 'plugin_file' ] ) );
		};

		add_action( 'init', $this->create_callback( 'init', function () use ( $container ) {
			if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
				return;
			}

			$container[ self::IMPORT_PRODUCTS ]->register();
			$container[ self::UPDATE_COUNTRIES ]->register();
			$container[ self::DOCS_BUILD ]->register();
			$container[ self::DOCS_IMPORT ]->register();
		} ), 0, 0 );
	}
}