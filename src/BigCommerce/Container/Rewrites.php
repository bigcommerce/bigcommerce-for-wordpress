<?php


namespace BigCommerce\Container;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Rewrites\Action_Endpoint;
use BigCommerce\Rewrites\Flusher;
use Pimple\Container;

class Rewrites extends Provider {
	const ACTION_ENDPOINT = 'rewrites.action_endpoint';
	const FLUSH           = 'rewrites.flush';

	public function register( Container $container ) {
		$container[ self::ACTION_ENDPOINT ] = function ( Container $container ) {
			return new Action_Endpoint();
		};

		add_action( 'init', $this->create_callback( 'register_action_route', function () use ( $container ) {
			$container[ self::ACTION_ENDPOINT ]->register_route();
		} ), 10, 0 );

		add_action( 'parse_request', $this->create_callback( 'parse_action_request', function ( \WP $wp ) use ( $container ) {
			$container[ self::ACTION_ENDPOINT ]->handle_request( $wp );
		} ), 10, 1 );

		$container[ self::FLUSH ] = function ( Container $container ) {
			return new Flusher();
		};

		add_action( 'wp_loaded', $this->create_callback( 'flush', function () use ( $container ) {
			$container[ self::FLUSH ]->do_flush();
		} ), 10, 0 );

		$schedule_flush = $this->create_callback( 'schedule_flush', function () use ( $container ) {
			$container[ self::FLUSH ]->schedule_flush();
		} );
		add_action( 'update_option_' . Product_Archive::ARCHIVE_SLUG, $schedule_flush, 10, 0 );
		add_action( 'update_option_' . Product_Archive::CATEGORY_SLUG, $schedule_flush, 10, 0 );
		add_action( 'update_option_' . Product_Archive::BRAND_SLUG, $schedule_flush, 10, 0 );
		add_action( 'add_option_' . Product_Archive::ARCHIVE_SLUG, $schedule_flush, 10, 0 );
		add_action( 'add_option_' . Product_Archive::CATEGORY_SLUG, $schedule_flush, 10, 0 );
		add_action( 'add_option_' . Product_Archive::BRAND_SLUG, $schedule_flush, 10, 0 );
	}
}
