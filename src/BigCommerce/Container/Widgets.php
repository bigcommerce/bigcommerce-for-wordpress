<?php


namespace BigCommerce\Container;


use BigCommerce\Widgets\Mini_Cart_Widget;
use BigCommerce\Widgets\Product_Category_Widget;
use Pimple\Container;

class Widgets extends Provider {
	const WIDGET_LIST = 'widgets.list';

	public function register( Container $container ) {
		$container[ self::WIDGET_LIST ] = function ( Container $container ) {
			return [
				Product_Category_Widget::class,
				Mini_Cart_Widget::class,
			];
		};

		add_action( 'widgets_init', $this->create_callback( 'widgets_init', function () use ( $container ) {
			foreach ( $container[ self::WIDGET_LIST ] as $class ) {
				register_widget( $class );
			}
		} ), 10, 0 );
	}

}
