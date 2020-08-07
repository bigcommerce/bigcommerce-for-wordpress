<?php

namespace BigCommerce\Container;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Reviews\Product_Update_Listener;
use BigCommerce\Reviews\Review_Cache;
use BigCommerce\Reviews\Review_Fetcher;
use Pimple\Container;

class Reviews extends Provider {
	const PRODUCT_LISTENER = 'reviews.product_listener';
	const FETCHER          = 'reviews.fetcher';
	const CACHER           = 'reviews.cacher';

	public function register( Container $container ) {
		$container[ self::PRODUCT_LISTENER ] = function ( Container $container ) {
			return new Product_Update_Listener();
		};
		$meta_update                         = $this->create_callback( 'product_meta_updated', function ( $meta_id, $post_id, $meta_key, $meta_value ) use ( $container ) {
			$container[ self::PRODUCT_LISTENER ]->meta_updated( $meta_id, $post_id, $meta_key, $meta_value );
		} );
		add_action( 'added_post_meta', $meta_update, 10, 4 );
		add_action( 'updated_post_meta', $meta_update, 10, 4 );

		$container[ self::FETCHER ] = function ( Container $container ) {
			return new Review_Fetcher( $container[ Api::FACTORY ]->catalog() );
		};

		$container[ self::CACHER ] = function ( Container $container ) {
			return new Review_Cache( $container[ self::FETCHER ] );
		};
		add_action( Product_Update_Listener::TRIGGER_UPDATE, $this->create_callback( 'update_cache', function ( $product_id ) use ( $container ) {
			$container[ self::CACHER ]->update_cache( $product_id );
		} ), 10, 1 );
	}

}
