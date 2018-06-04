<?php


namespace BigCommerce\Container;


use Pimple\Container;

class Shortcodes extends Provider {
	const PRODUCTS = 'shortcode.products';
	const CART     = 'shortcode.cart';
	const LOGIN    = 'shortcode.login';
	const REGISTER = 'shortcode.register';
	const ACCOUNT  = 'shortcode.account';
	const ADDRESS  = 'shortcode.address';
	const ORDERS   = 'shortcode.orders';

	public function register( Container $container ) {
		$container[ self::PRODUCTS ] = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Products( $container[ Rest::SHORTCODE ] );
		};
		$container[ self::CART ]     = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Cart( $container[ Api::FACTORY ]->cart() );
		};
		$container[ self::LOGIN ]    = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Login_Form();
		};
		$container[ self::REGISTER ] = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Registration_Form();
		};
		$container[ self::ACCOUNT ] = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Account_Profile();
		};
		$container[ self::ADDRESS ] = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Address_List();
		};
		$container[ self::ORDERS ] = function ( Container $container ) {
			return new \BigCommerce\Shortcodes\Order_History( $container[ Rest::ORDERS_SHORTCODE ]);
		};

		add_action( 'after_setup_theme', $this->create_callback( 'register', function () use ( $container ) {
			add_shortcode( \BigCommerce\Shortcodes\Products::NAME, [ $container[ self::PRODUCTS ], 'render' ] );
			add_shortcode( \BigCommerce\Shortcodes\Cart::NAME, [ $container[ self::CART ], 'render' ] );
			add_shortcode( \BigCommerce\Shortcodes\Login_Form::NAME, [ $container[ self::LOGIN ], 'render' ] );
			add_shortcode( \BigCommerce\Shortcodes\Registration_Form::NAME, [ $container[ self::REGISTER ], 'render' ] );
			add_shortcode( \BigCommerce\Shortcodes\Account_Profile::NAME, [ $container[ self::ACCOUNT ], 'render' ] );
			add_shortcode( \BigCommerce\Shortcodes\Address_List::NAME, [ $container[ self::ADDRESS ], 'render' ] );
			add_shortcode( \BigCommerce\Shortcodes\Order_History::NAME, [ $container[ self::ORDERS ], 'render' ] );
		} ), 10, 0 );
	}
}