<?php


namespace BigCommerce\Container;


use BigCommerce\Rest\Product_Component_Shortcode_Controller;
use BigCommerce\Rest\Cart_Controller;
use BigCommerce\Rest\Orders_Shortcode_Controller;
use BigCommerce\Rest\Pricing_Controller;
use BigCommerce\Rest\Products_Controller;
use BigCommerce\Rest\Reviews_Listing_Controller;
use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Rest\Shipping_Controller;
use BigCommerce\Rest\Coupon_Code_Controller;
use BigCommerce\Reviews\Review_Fetcher;
use Pimple\Container;

class Rest extends Provider {
	const NAMESPACE_BASE = 'rest.namespace';
	const VERSION        = 'rest.version';

	const CART_BASE = 'rest.cart_base';
	const CART      = 'rest.cart';

	const PRODUCTS_BASE = 'rest.products_base';
	const PRODUCTS      = 'rest.products';

	const SHORTCODE_BASE = 'rest.shortcode_base';
	const SHORTCODE      = 'rest.shortcode';

	const ORDERS_SHORTCODE_BASE = 'rest.orders_shortcode_base';
	const ORDERS_SHORTCODE      = 'rest.orders_shortcode';

	const COMPONENT_SHORTCODE_BASE = 'rest.product_component_shortcode_base';
	const COMPONENT_SHORTCODE      = 'rest.product_component_shortcode';

	const REVIEW_LIST_BASE = 'rest.review_list_base';
	const REVIEW_LIST      = 'rest.review_list';

	const PRICING_BASE = 'rest.pricing_base';
	const PRICING      = 'rest.pricing';

	const SHIPPING_BASE = 'rest.shipping_base';
	const SHIPPING      = 'rest.shipping';

	const COUPON_CODE_BASE = 'rest.coupon_code_base';
	const COUPON_CODE      = 'rest.coupon_code';

	private $version = 1;

	public function register( Container $container ) {
		$container[ self::NAMESPACE_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST namespace base.
			 *
			 * @param string $namespace Namespace.
			 */
			return apply_filters( 'bigcommerce/rest/namespace_base', 'bigcommerce' );
		};

		$container[ self::VERSION ] = function ( Container $container ) {
			/**
			 * Filters REST version.
			 *
			 * @param int $version Version.
			 */
			return apply_filters( 'bigcommerce/rest/version', $this->version );
		};

		$container[ self::CART_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST cart base.
			 *
			 * @param string $cart Cart base.
			 */
			return apply_filters( 'bigcommerce/rest/cart_base', 'cart' );
		};

		$container[ self::CART ] = function ( Container $container ) {
			return new Cart_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::CART_BASE ], $container[ Api::FACTORY ]->cart() );
		};

		$container[ self::PRODUCTS_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST products base.
			 *
			 * @param string $products Products base.
			 */
			return apply_filters( 'bigcommerce/rest/products_base', 'products' );
		};

		$container[ self::PRODUCTS ] = function ( Container $container ) {
			return new Products_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::PRODUCTS_BASE ] );
		};

		$container[ self::SHORTCODE_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST shortcode base.
			 *
			 * @param string $shortcode Shortcode base.
			 */
			return apply_filters( 'bigcommerce/rest/shortcode_base', 'shortcode' );
		};

		$container[ self::SHORTCODE ] = function ( Container $container ) {
			return new Shortcode_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::SHORTCODE_BASE ] );
		};

		$container[ self::ORDERS_SHORTCODE_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST orders shortcode base.
			 *
			 * @param string $orders_shortcode Orders shortcode base.
			 */
			return apply_filters( 'bigcommerce/rest/orders_shortcode_base', 'orders-shortcode' );
		};

		$container[ self::ORDERS_SHORTCODE ] = function ( Container $container ) {
			return new Orders_Shortcode_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::ORDERS_SHORTCODE_BASE ] );
		};

		$container[ self::COMPONENT_SHORTCODE_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST product component shortcode base.
			 *
			 * @param string $component_shortcode Component shortcode base.
			 */
			return apply_filters( 'bigcommerce/rest/product_component_shortcode_base', 'component-shortcode' );
		};

		$container[ self::COMPONENT_SHORTCODE ] = function ( Container $container ) {
			return new Product_Component_Shortcode_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::COMPONENT_SHORTCODE_BASE ] );
		};

		$container[ self::REVIEW_LIST_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST review list base.
			 *
			 * @param string $product_reviews Product reviews base.
			 */
			return apply_filters( 'bigcommerce/rest/review_list_base', 'product-reviews' );
		};

		$container[ self::REVIEW_LIST ] = function ( Container $container ) {
			return new Reviews_Listing_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::REVIEW_LIST_BASE ], $container[ Reviews::FETCHER ] );
		};

		$container[ self::PRICING_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST pricing base.
			 *
			 * @param string $pricing Pricing base.
			 */
			return apply_filters( 'bigcommerce/rest/pricing_base', 'pricing' );
		};

		$container[ self::PRICING ] = function ( Container $container ) {
			return new Pricing_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::PRICING_BASE ], $container[ Api::FACTORY ]->pricing() );
		};

		$container[ self::SHIPPING_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST shipping base.
			 *
			 * @param string $shipping Shipping base.
			 */
			return apply_filters( 'bigcommerce/rest/shipping_base', 'shipping' );
		};

		$container[ self::SHIPPING ] = function ( Container $container ) {
			return new Shipping_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::SHIPPING_BASE ], $container[ Api::FACTORY ]->shipping(), $container[ Api::FACTORY ]->cart() );
		};

		$container[ self::COUPON_CODE_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST coupon code base.
			 *
			 * @param string $coupon_code Coupon code base.
			 */
			return apply_filters( 'bigcommerce/rest/coupon_code', 'coupon-code' );
		};

		$container[ self::COUPON_CODE ] = function ( Container $container ) {
			return new Coupon_Code_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::COUPON_CODE_BASE ], $container[ Api::FACTORY ]->checkout(), $container[ Api::FACTORY ]->cart() );
		};

		add_action( 'rest_api_init', $this->create_callback( 'rest_init', function () use ( $container ) {
			$container[ self::PRODUCTS ]->register_routes();
			$container[ self::SHORTCODE ]->register_routes();
			$container[ self::ORDERS_SHORTCODE ]->register_routes();
			$container[ self::COMPONENT_SHORTCODE ]->register_routes();
			$container[ self::CART ]->register_routes();
			$container[ self::REVIEW_LIST ]->register_routes();
			$container[ self::PRICING ]->register_routes();
			$container[ self::SHIPPING ]->register_routes();
			$container[ self::COUPON_CODE ]->register_routes();
		} ), 10, 0 );

		add_filter( 'bigcommerce/product/reviews/rest_url', $this->create_callback( 'review_list_rest_url', function ( $url, $post_id ) use ( $container ) {
			return $container[ self::REVIEW_LIST ]->product_reviews_url( $post_id );
		} ), 10, 2 );

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'cart_js_config', function( $config ) use ( $container ) {
			return $container[ self::CART ]->js_config( $config );
		}), 10, 1 );

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'pricing_js_config', function( $config ) use ( $container ) {
			return $container[ self::PRICING ]->js_config( $config );
		}), 10, 1 );

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'shipping_js_config', function( $config ) use ( $container ) {
			return $container[ self::SHIPPING ]->js_config( $config );
		}), 10, 1 );

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'coupon_code_js_config', function( $config ) use ( $container ) {
			return $container[ self::COUPON_CODE ]->js_config( $config );
		}), 10, 1 );
	}
}
