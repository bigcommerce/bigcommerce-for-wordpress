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
use BigCommerce\Rest\Storefront_Controller;
use BigCommerce\Rest\Terms_Controller;
use BigCommerce\Reviews\Review_Fetcher;
use Pimple\Container;

/**
 * Provides RESTful controllers and endpoints for BigCommerce integration.
 *
 * @package BigCommerce\Container
 */
class Rest extends Provider {
	/**
	 * The base namespace for REST API routes.
	 *
	 * @var string
	 */
	const NAMESPACE_BASE = 'rest.namespace';

	/**
	 * The version of the REST API.
	 *
	 * @var string
	 */
	const VERSION = 'rest.version';

	/**
	 * The base route for the cart API.
	 *
	 * @var string
	 */
	const CART_BASE = 'rest.cart_base';

	/**
	 * The cart API identifier.
	 *
	 * @var string
	 */
	const CART = 'rest.cart';

	/**
	 * The base route for the products API.
	 *
	 * @var string
	 */
	const PRODUCTS_BASE = 'rest.products_base';

	/**
	 * The products API identifier.
	 *
	 * @var string
	 */
	const PRODUCTS = 'rest.products';

	/**
	 * The base route for the storefront API.
	 *
	 * @var string
	 */
	const STOREFRONT_BASE = 'rest.storefront_base';

	/**
	 * The storefront API identifier.
	 *
	 * @var string
	 */
	const STOREFRONT = 'rest.storefront';

	/**
	 * The base route for the terms API.
	 *
	 * @var string
	 */
	const TERMS_BASE = 'rest.terms_base';

	/**
	 * The terms API identifier.
	 *
	 * @var string
	 */
	const TERMS = 'rest.terms';

	/**
	 * The base route for the shortcode API.
	 *
	 * @var string
	 */
	const SHORTCODE_BASE = 'rest.shortcode_base';

	/**
	 * The shortcode API identifier.
	 *
	 * @var string
	 */
	const SHORTCODE = 'rest.shortcode';

	/**
	 * The base route for the orders shortcode API.
	 *
	 * @var string
	 */
	const ORDERS_SHORTCODE_BASE = 'rest.orders_shortcode_base';

	/**
	 * The orders shortcode API identifier.
	 *
	 * @var string
	 */
	const ORDERS_SHORTCODE = 'rest.orders_shortcode';

	/**
	 * The base route for the product component shortcode API.
	 *
	 * @var string
	 */
	const COMPONENT_SHORTCODE_BASE = 'rest.product_component_shortcode_base';

	/**
	 * The product component shortcode API identifier.
	 *
	 * @var string
	 */
	const COMPONENT_SHORTCODE = 'rest.product_component_shortcode';

	/**
	 * The base route for the review list API.
	 *
	 * @var string
	 */
	const REVIEW_LIST_BASE = 'rest.review_list_base';

	/**
	 * The review list API identifier.
	 *
	 * @var string
	 */
	const REVIEW_LIST = 'rest.review_list';

	/**
	 * The base route for the pricing API.
	 *
	 * @var string
	 */
	const PRICING_BASE = 'rest.pricing_base';

	/**
	 * The pricing API identifier.
	 *
	 * @var string
	 */
	const PRICING = 'rest.pricing';

	/**
	 * The base route for the shipping API.
	 *
	 * @var string
	 */
	const SHIPPING_BASE = 'rest.shipping_base';

	/**
	 * The shipping API identifier.
	 *
	 * @var string
	 */
	const SHIPPING = 'rest.shipping';

	/**
	 * The base route for the coupon code API.
	 *
	 * @var string
	 */
	const COUPON_CODE_BASE = 'rest.coupon_code_base';

	/**
	 * The coupon code API identifier.
	 *
	 * @var string
	 */
	const COUPON_CODE = 'rest.coupon_code';	

	/**
	 * The version of the REST API used by the container. It is referenced when setting up the
	 * `VERSION` service in the container, ensuring that the API version can be applied to the various API endpoints
	 * and controllers.
	 *
	 * @var string
	 */
	private $version = 1;

	/**
	 * Registers services and controllers in the container and sets up REST API hooks.
	 *
	 * @param Container $container The dependency injection container instance.
	 *
	 * @return void
	 */
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

		$container[ self::STOREFRONT_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST products base.
			 *
			 * @param string $products Products base.
			 */
			return apply_filters( 'bigcommerce/rest/storefront_base', 'storefront' );
		};

		$container[ self::STOREFRONT ] = function ( Container $container ) {
			return new Storefront_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::STOREFRONT_BASE ] );
		};

		$container[ self::TERMS_BASE ] = function ( Container $container ) {
			/**
			 * Filters REST terms base.
			 */
			return apply_filters( 'bigcommerce/rest/products_base', 'terms' );
		};

		$container[ self::TERMS ] = function ( Container $container ) {
			return new Terms_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::TERMS_BASE ] );
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
			return new Reviews_Listing_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::REVIEW_LIST_BASE ], $container[ Reviews::FETCHER ], $container[ Api::CACHE_HANDLER ] );
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
			return new Shipping_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::SHIPPING_BASE ], $container[ Api::FACTORY ]->shipping(), $container[ Api::FACTORY ]->cart(), $container[ Api::FACTORY ]->checkout() );
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
			$container[ self::TERMS ]->register_routes();
			$container[ self::SHORTCODE ]->register_routes();
			$container[ self::ORDERS_SHORTCODE ]->register_routes();
			$container[ self::COMPONENT_SHORTCODE ]->register_routes();
			$container[ self::CART ]->register_routes();
			$container[ self::REVIEW_LIST ]->register_routes();
			$container[ self::PRICING ]->register_routes();
			$container[ self::SHIPPING ]->register_routes();
			$container[ self::COUPON_CODE ]->register_routes();
			$container[ self::STOREFRONT ]->register_routes();
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

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'products_js_config', function( $config ) use ( $container ) {
			return $container[ self::PRODUCTS ]->js_config( $config );
		}), 10, 1 );

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'shipping_js_config', function( $config ) use ( $container ) {
			return $container[ self::SHIPPING ]->js_config( $config );
		}), 10, 1 );

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'coupon_code_js_config', function( $config ) use ( $container ) {
			return $container[ self::COUPON_CODE ]->js_config( $config );
		}), 10, 1 );
	}
}
