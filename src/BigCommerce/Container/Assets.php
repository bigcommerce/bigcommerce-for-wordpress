<?php


namespace BigCommerce\Container;


use BigCommerce\Assets\Admin;
use BigCommerce\Assets\Theme;
use BigCommerce\Plugin;
use BigCommerce\Taxonomies\Channel\Connections;
use Pimple\Container;

class Assets extends Provider {
	const PATH                  = 'assets.path';
	const VERSION               = 'assets.version';
	const ADMIN_SCRIPTS         = 'assets.admin.scripts';
	const ADMIN_STYLES          = 'assets.admin.styles';
	const ADMIN_CONFIG          = 'assets.admin.config';
	const ADMIN_LOCALIZATIONN   = 'assets.admin.l10n';
	const FRONTEND_SCRIPTS      = 'assets.frontend.scripts';
	const FRONTEND_STYLES       = 'assets.frontend.styles';
	const FRONTEND_CONFIG       = 'assets.frontend.config';
	const FRONTEND_LOCALIZATION = 'assets.frontend.l10n';
	const IMAGE_SIZES           = 'assets.frontend.imagesizes';

	public function register( Container $container ) {
		$container[ self::PATH ]    = function ( Container $container ) {
			return plugins_url( 'assets', $container[ 'plugin_file' ] );
		};
		$container[ self::VERSION ] = function ( Container $container ) {
			$version = Plugin::VERSION;
			if ( file_exists( dirname( $container[ 'plugin_file' ] ) . '/build-timestamp.php' ) ) {
				include_once( dirname( $container[ 'plugin_file' ] ) . '/build-timestamp.php' );
			}
			if ( defined( 'BIGCOMMERCE_ASSETS_BUILD_TIMESTAMP' ) ) {
				$version .= '-' . BIGCOMMERCE_ASSETS_BUILD_TIMESTAMP;
			}

			return $version;
		};

		$this->admin( $container );
		$this->frontend( $container );
	}

	public function admin( Container $container ) {
		$container[ self::ADMIN_SCRIPTS ] = function ( Container $container ) {
			return new Admin\Scripts( $container[ self::PATH ], $container[ self::VERSION ], $container[ self::ADMIN_CONFIG ], $container[ self::ADMIN_LOCALIZATIONN ] );
		};

		$container[ self::ADMIN_STYLES ] = function ( Container $container ) {
			return new Admin\Styles( $container[ self::PATH ], $container[ self::VERSION ] );
		};

		$container[ self::ADMIN_CONFIG ] = function( Container $container ) {
			return new Admin\JS_Config( $container[ self::PATH ] );
		};

		$container[ self::ADMIN_LOCALIZATIONN ] = function( Container $container ) {
			return new Admin\JS_Localization();
		};

		add_action( 'admin_enqueue_scripts', $this->create_callback( 'admin_admin_enqueue_scripts', function () use ( $container ) {
			$container[ self::ADMIN_SCRIPTS ]->enqueue_scripts();
			$container[ self::ADMIN_STYLES ]->enqueue_styles();
		} ), 9, 0 );
		
		add_action( 'admin_enqueue_scripts', $this->create_callback( 'admin_remove_google_sitekit_script_on_bc_admin_pages', function ( $hook ) {
			if ( strpos( get_current_screen()->id, 'bigcommerce') !== false ) {
				wp_dequeue_script( 'googlesitekit-base' );
			}
		} ), 999 );
	}

	public function frontend( Container $container ) {
		$container[ self::FRONTEND_SCRIPTS ] = function ( Container $container ) {
			return new Theme\Scripts( $container[ self::PATH ], $container[ self::VERSION ], $container[ self::FRONTEND_CONFIG ], $container[ self::FRONTEND_LOCALIZATION ] );
		};

		$container[ self::FRONTEND_STYLES ] = function ( Container $container ) {
			return new Theme\Styles( $container[ self::PATH ], $container[ self::VERSION ] );
		};

		$container[ self::FRONTEND_CONFIG ] = function ( Container $container ) {
			return new Theme\JS_Config( $container[ self::PATH ], new Connections() );
		};

		$container[ self::FRONTEND_LOCALIZATION ] = function( Container $container ) {
			return new Theme\JS_Localization();
		};

		$container[ self::IMAGE_SIZES ] = function ( Container $container ) {
			return new Theme\Image_Sizes();
		};

		add_action( 'after_setup_theme', $this->create_callback( 'frontend_after_setup_theme', function () use ( $container ) {
			$container[ self::IMAGE_SIZES ]->register_sizes();
		} ), 10, 0 );

		add_action( 'wp_enqueue_scripts', $this->create_callback( 'frontend_wp_enqueue_scripts', function () use ( $container ) {
			$container[ self::FRONTEND_SCRIPTS ]->enqueue_scripts();
			$container[ self::FRONTEND_STYLES ]->enqueue_styles();
		} ), 10, 0 );
	}
}
