<?php


namespace BigCommerce\Container;


use BigCommerce\Util\Kses;
use Pimple\Container;

class Util extends Provider {
	const KSES = 'util.kses';

	public function register( Container $container ) {
		$container[ self::KSES ] = function ( Container $container ) {
			return new Kses();
		};

		add_action( 'wp_kses_allowed_html', $this->create_callback( 'kses_allowed_html', function ( $allowed_tags, $context ) use ( $container ) {
			return $container[ self::KSES ]->product_description_allowed_html( $allowed_tags, $context );
		} ), 10, 2 );
	}

}
