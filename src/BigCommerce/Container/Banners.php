<?php

namespace BigCommerce\Container;


use BigCommerce\Banners\Banners as Banners_Manager;
use Pimple\Container;


class Banners extends Provider {
	const BANNERS = 'banners';

	public function register( Container $container ) {
		$container[ self::BANNERS ] = function ( Container $container ) {
			return new Banners_Manager( $container[ Api::FACTORY ]->banners() );
		};

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'banners_js_config', function ( $config ) use ( $container ) {
			return $container[ self::BANNERS ]->js_config( $config );
		} ), 10, 1 );
	}

}
