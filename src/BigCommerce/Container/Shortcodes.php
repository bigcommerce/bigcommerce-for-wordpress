<?php


namespace BigCommerce\Container;

use BigCommerce\Shortcodes as Codes;
use Pimple\Container;

class Shortcodes extends Provider {
	const PRODUCTS           = 'shortcode.products';
	const CART               = 'shortcode.cart';
	const CHECKOUT           = 'shortcode.checkout';
	const LOGIN              = 'shortcode.login';
	const REGISTER           = 'shortcode.register';
	const ACCOUNT            = 'shortcode.account';
	const ADDRESS            = 'shortcode.address';
	const ORDERS             = 'shortcode.orders';
	const GIFT_FORM          = 'shortcode.gift_certificate.form';
	const GIFT_BALANCE       = 'shortcode.gift_certificate.balance';
	const PRODUCT_REVIEWS    = 'shortcode.products_reviews';
	const PRODUCT_COMPONENTS = 'shortcode.products_components';
	const WISHLIST           = 'shortcode.wishlist';

	public function register( Container $container ) {
		$container[ self::PRODUCTS ]           = function ( Container $container ) {
			return new Codes\Products( $container[ Rest::SHORTCODE ] );
		};
		$container[ self::CART ]               = function ( Container $container ) {
			return new Codes\Cart( $container[ Api::FACTORY ]->cart() );
		};
		$container[ self::CHECKOUT ]           = function ( Container $container ) {
			return new Codes\Checkout( $container[ Api::FACTORY ]->cart() );
		};
		$container[ self::LOGIN ]        = function ( Container $container ) {
			return new Codes\Login_Form();
		};
		$container[ self::REGISTER ]     = function ( Container $container ) {
			return new Codes\Registration_Form();
		};
		$container[ self::ACCOUNT ]      = function ( Container $container ) {
			return new Codes\Account_Profile();
		};
		$container[ self::ADDRESS ]      = function ( Container $container ) {
			return new Codes\Address_List();
		};
		$container[ self::ORDERS ]          = function ( Container $container ) {
			return new Codes\Order_History( $container[ Rest::ORDERS_SHORTCODE ] );
		};
		$container[ self::GIFT_FORM ]          = function ( Container $container ) {
			return new Codes\Gift_Certificate_Form( $container[ Api::FACTORY ]->marketing() );
		};
		$container[ self::GIFT_BALANCE ]       = function ( Container $container ) {
			return new Codes\Gift_Certificate_Balance( $container[ Api::FACTORY ]->marketing() );
		};
		$container[ self::PRODUCT_REVIEWS ]    = function ( Container $container ) {
			return new Codes\Product_Reviews();
		};
		$container[ self::PRODUCT_COMPONENTS ] = function () {
			return new Codes\Product_Components();
		};
		$container[ self::WISHLIST ]           = function ( Container $container ) {
			return new Codes\Wishlist( $container[ Api::FACTORY ]->wishlists() );
		};

		add_action( 'after_setup_theme', $this->create_callback( 'register', function () use ( $container ) {
			add_shortcode( Codes\Products::NAME, [ $container[ self::PRODUCTS ], 'render' ] );
			add_shortcode( Codes\Cart::NAME, [ $container[ self::CART ], 'render' ] );
			add_shortcode( Codes\Checkout::NAME, [ $container[ self::CHECKOUT ], 'render' ] );
			add_shortcode( Codes\Login_Form::NAME, [ $container[ self::LOGIN ], 'render' ] );
			add_shortcode( Codes\Registration_Form::NAME, [ $container[ self::REGISTER ], 'render' ] );
			add_shortcode( Codes\Account_Profile::NAME, [ $container[ self::ACCOUNT ], 'render' ] );
			add_shortcode( Codes\Address_List::NAME, [ $container[ self::ADDRESS ], 'render' ] );
			add_shortcode( Codes\Order_History::NAME, [ $container[ self::ORDERS ], 'render' ] );
			add_shortcode( Codes\Gift_Certificate_Form::NAME, [ $container[ self::GIFT_FORM ], 'render' ] );
			add_shortcode( Codes\Gift_Certificate_Balance::NAME, [ $container[ self::GIFT_BALANCE ], 'render' ] );
			add_shortcode( Codes\Product_Reviews::NAME, [ $container[ self::PRODUCT_REVIEWS ], 'render' ] );
			add_shortcode( Codes\Product_Components::NAME, [ $container[ self::PRODUCT_COMPONENTS ], 'render' ] );
			add_shortcode( Codes\Wishlist::NAME, [ $container[ self::WISHLIST ], 'render' ] );
		} ), 10, 0 );
	}
}