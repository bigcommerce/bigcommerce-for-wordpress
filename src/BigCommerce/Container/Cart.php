<?php


namespace BigCommerce\Container;


use BigCommerce\Cart\Add_To_Cart;
use BigCommerce\Cart\Buy_Now;
use BigCommerce\Cart\Cache_Control;
use BigCommerce\Cart\Cart_Menu_Item;
use BigCommerce\Cart\Cart_Recovery;
use BigCommerce\Cart\Checkout;
use BigCommerce\Cart\Mini_Cart;
use Pimple\Container;

/**
 * Cart service provider for managing cart-related functionality.
 *
 * The `Cart` class registers various cart-related services in the container, such as:
 * - Menu item for the cart
 * - Mini cart functionality
 * - Cache control for cart and checkout pages
 * - Buy Now functionality
 * - Add to Cart and Cart Recovery services
 * - Checkout service
 *
 * The class also sets up necessary action and filter hooks to integrate these services
 * into the WordPress environment, ensuring that cart operations are handled effectively.
 *
 * @package BigCommerce\Container
 */
class Cart extends Provider {

	/**
	 * The identifier for the cart page indicator service.
	 *
	 * This constant is used to reference the service responsible for managing the cart
	 * page indicator functionality, helping to indicate the current state of the cart
	 * on the frontend.
	 * 
	 * @var string
	 */
	const CART_INDICATOR = 'cart.page_indicator';

	/**
	 * The identifier for the cart page creator service.
	 *
	 * This constant is used to reference the service that handles the creation and
	 * setup of the cart page, ensuring that the cart page content is properly generated
	 * and displayed to the user.
	 * 
	 * @var string
	 */
	const CART_CREATOR = 'cart.page_creator';

	/**
	 * The identifier for the cart menu item service.
	 *
	 * This constant is used to reference the service that manages the cart menu item,
	 * ensuring that the cart is properly represented in the navigation menu and allowing
	 * the user to interact with it.
	 * 
	 * @var string
	 */
	const MENU_ITEM = 'cart.menu_item';

	/**
	 * The identifier for the mini cart service.
	 *
	 * This constant is used to reference the service responsible for handling the
	 * mini cart, which typically displays a summary of the user's cart in a compact
	 * view, often in a header or sidebar.
	 * 
	 * @var string
	 */
	const MINI_CART = 'cart.mini_cart';

	/**
	 * The identifier for the cache control service for cart and checkout pages.
	 *
	 * This constant is used to reference the service that manages caching for cart
	 * and checkout pages, ensuring proper cache invalidation and preventing issues
	 * with stale data on these pages.
	 * 
	 * @var string
	 */
	const CACHE_CONTROL = 'cart.cache_control';

	/**
	 * The identifier for the Buy Now service.
	 *
	 * This constant is used to reference the service responsible for handling the
	 * Buy Now functionality, which allows users to quickly add products to their
	 * cart and proceed to checkout without navigating through multiple pages.
	 * 
	 * @var string
	 */
	const BUY_NOW = 'cart.buy_now';

	/**
	 * The identifier for the Add to Cart service.
	 *
	 * This constant is used to reference the service that handles the action of
	 * adding items to the cart, ensuring that products are properly added and the
	 * cart is updated accordingly.
	 * 
	 * @var string
	 */
	const ADD_TO_CART = 'cart.add_to_cart';

	/**
	 * The identifier for the Cart Recovery service.
	 *
	 * This constant is used to reference the service responsible for handling
	 * cart recovery, allowing users to retrieve abandoned carts and continue their
	 * shopping experience without losing previously added items.
	 * 
	 * @var string
	 */
	const RECOVER_FROM_CART = 'cart.recover_from_cart';

	/**
	 * The identifier for the Checkout service.
	 *
	 * This constant is used to reference the service that manages the checkout
	 * process, including handling the cart's transition to the checkout page and
	 * managing the user's order details.
	 * 
	 * @var string
	 */
	const CHECKOUT = 'cart.checkout';


	/**
	 * Registers the cart-related services and hooks within the container.
	 *
	 * This method registers various cart-related services such as the menu item, mini cart, cache control,
	 * buy now functionality, add-to-cart functionality, and checkout services within the container. It also sets up
	 * action and filter hooks to manage cart behavior throughout the application.
	 *
	 * @param Container $container The dependency injection container used to manage services.
	 * @return void
	 */
	public function register( Container $container ) {
		$this->menu_item( $container );
		$this->mini_cart( $container );
		$this->cache_control( $container );

		$this->buy_now( $container );
		$this->cart( $container );
		$this->checkout( $container );
	}

	private function menu_item( Container $container ) {
		$container[ self::MENU_ITEM ] = function ( Container $container ) {
			return new Cart_Menu_Item();
		};

		add_filter( 'wp_setup_nav_menu_item', $this->create_callback( 'menu_item', function ( $menu_item ) use ( $container ) {
			return $container[ self::MENU_ITEM ]->add_classes_to_cart_page( $menu_item );
		} ), 10, 1 );
	}

	private function cache_control( Container $container ) {
		$container[ self::CACHE_CONTROL ] = function ( Container $container ) {
			return new Cache_Control();
		};

		add_action( 'template_redirect', $this->create_callback( 'shortcode_check', function () use ( $container ) {
			$container[ self::CACHE_CONTROL ]->check_for_shortcodes( [
				\BigCommerce\Shortcodes\Cart::NAME,
				\BigCommerce\Shortcodes\Checkout::NAME,
			] );
		} ), 10, 0 );
	
		add_action( 'bigcommerce/do_not_cache', $this->create_callback( 'do_not_cache', function () use ( $container ) {
			$container[ self::CACHE_CONTROL ]->do_not_cache();
		} ), 10, 0 );
	}

	private function buy_now( Container $container ) {
		$container[ self::BUY_NOW ] = function ( Container $container ) {
			return new Buy_Now();
		};

		add_action( 'bigcommerce/action_endpoint/' . Buy_Now::ACTION, $this->create_callback( 'buy_now_handle_request', function ( $args ) use ( $container ) {
			$container[ self::BUY_NOW ]->handle_request( reset( $args ), $container[ Api::FACTORY ]->cart() );
		} ), 10, 1 );
	}

	private function cart( Container $container ) {
		$container[ self::ADD_TO_CART ] = function ( Container $container ) {
			return new Add_To_Cart();
		};

		$container[ self::RECOVER_FROM_CART ] = function ( Container $container ) {
			return new Cart_Recovery( $container[ Api::FACTORY ]->abandonedCart(), $container[ Api::FACTORY ]->cart() );
		};

		add_action( 'bigcommerce/action_endpoint/' . Add_To_Cart::ACTION, $this->create_callback( 'add_to_cart_handle_request', function ( $args ) use ( $container ) {
			$container[ self::ADD_TO_CART ]->handle_request( reset( $args ), $container[ Api::FACTORY ]->cart() );
		} ), 10, 1 );

		add_action( 'bigcommerce/action_endpoint/' . Cart_Recovery::ACTION, $this->create_callback( 'recover_cart_handle_request', function ( $args ) use ( $container ) {
			$container[ self::RECOVER_FROM_CART ]->handle_request();
		} ), 10, 1 );
	}

	private function mini_cart( Container $container ) {
		$container[ self::MINI_CART ] = function ( Container $container ) {
			return new Mini_Cart();
		};

		add_filter( 'bigcommerce/js_config', $this->create_callback( 'mini_cart_js_config', function ( $config ) use ( $container ) {
			return $container[ self::MINI_CART ]->add_mini_cart_config( $config );
		} ), 10, 1 );
	}

	private function checkout( Container $container ) {
		$container[ self::CHECKOUT ] = function ( Container $container ) {
			return new Checkout( $container[ Api::FACTORY ] );
		};

		add_action( 'bigcommerce/action_endpoint/' . Checkout::ACTION, $this->create_callback( 'checkout_handle_request', function ( $args ) use ( $container ) {
			$container[ self::CHECKOUT ]->handle_request( reset( $args ), $container[ Api::FACTORY ]->cart() );
		} ), 10, 1 );
	}
}
