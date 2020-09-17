<?php


namespace BigCommerce\Container;


use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Check_Balance_Page;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Pages\Gift_Certificate_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Pages\Required_Page;
use BigCommerce\Pages\Shipping_Returns_Page;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;
use BigCommerce\Settings\Sections\Gift_Certificates as Gift_Certificate_Settings;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;
use Pimple\Container;

class Pages extends Provider {
	const REQUIRED_PAGES = 'pages.required_pages';

	const CART_PAGE         = 'pages.cart';
	const CHECKOUT_PAGE     = 'pages.checkout';
	const LOGIN_PAGE        = 'pages.login';
	const REGISTRATION_PAGE = 'pages.register';
	const ACCOUNT_PAGE      = 'pages.account';
	const ADDRESS_PAGE      = 'pages.address';
	const ORDERS_PAGE       = 'pages.orders';
	const GIFT_PURCHACE     = 'pages.gift_certificate.purchase';
	const GIFT_BALANCE      = 'pages.gift_certificate.balance';
	const SHIPPING_PAGE     = 'pages.shipping_returns';
	const WISHLIST_USER     = 'pages.wishlist.user';
	const WISHLIST_PUBLIC   = 'pages.wishlist.public';

	public function register( Container $container ) {
		$container[ self::REQUIRED_PAGES ] = function ( Container $container ) {
			$pages = [
				$container[ self::LOGIN_PAGE ],
				$container[ self::ACCOUNT_PAGE ],
				$container[ self::ADDRESS_PAGE ],
				$container[ self::ORDERS_PAGE ],
				$container[ self::SHIPPING_PAGE ],
			];
			if ( ( (bool) get_option( Cart_Settings::OPTION_ENABLE_CART, true ) ) === true ) {
				$pages[] = $container[ self::CART_PAGE ];
			}
			if ( ( (bool) get_option( Cart_Settings::OPTION_EMBEDDED_CHECKOUT, true ) ) === true ) {
				$pages[] = $container[ self::CHECKOUT_PAGE ];
			}
			if ( ( (bool) get_option( Gift_Certificate_Settings::OPTION_ENABLE, true ) ) === true ) {
				$pages[] = $container[ self::GIFT_PURCHACE ];
				$pages[] = $container[ self::GIFT_BALANCE ];
			}
			if ( get_option( 'users_can_register' ) ) {
				$pages[] = $container[ self::REGISTRATION_PAGE ];
			}
			if ( get_option( Wishlist_Settings::ENABLED ) ) {
				$pages[] = $container[ self::WISHLIST_USER ];
			}

			return $pages;
		};

		$container[ self::CART_PAGE ] = function ( Container $container ) {
			return new Cart_Page();
		};

		$container[ self::CHECKOUT_PAGE ] = function ( Container $container ) {
			return new Checkout_Page();
		};

		$container[ self::LOGIN_PAGE ] = function ( Container $container ) {
			return new Login_Page();
		};

		$container[ self::REGISTRATION_PAGE ] = function ( Container $container ) {
			return new Registration_Page();
		};

		$container[ self::ACCOUNT_PAGE ] = function ( Container $container ) {
			return new Account_Page();
		};

		$container[ self::ADDRESS_PAGE ] = function ( Container $container ) {
			return new Address_Page();
		};

		$container[ self::ORDERS_PAGE ] = function ( Container $container ) {
			return new Orders_Page();
		};

		$container[ self::GIFT_PURCHACE ] = function ( Container $container ) {
			return new Gift_Certificate_Page();
		};

		$container[ self::GIFT_BALANCE ] = function ( Container $container ) {
			return new Check_Balance_Page();
		};

		$container[ self::SHIPPING_PAGE ] = function ( Container $container ) {
			return new Shipping_Returns_Page();
		};

		$container[ self::WISHLIST_USER ] = function( Container $container ) {
			return new Wishlist_Page();
		};

		add_action( 'admin_init', $this->create_callback( 'create_pages', function () use ( $container ) {
			foreach ( $container[ self::REQUIRED_PAGES ] as $page ) {
				/** @var Required_Page $page */
				$page->ensure_page_exists();
			}
		} ), 10, 0 );

		$clear_options = $this->create_callback( 'clear_options', function ( $post_id ) use ( $container ) {
			foreach ( $container[ self::REQUIRED_PAGES ] as $page ) {
				/** @var Required_Page $page */
				$page->clear_option_on_delete( $post_id );
			}
		} );
		add_action( 'trashed_post', $clear_options, 10, 1 );
		add_action( 'deleted_post', $clear_options, 10, 1 );

		add_action( 'display_post_states', $this->create_callback( 'post_states', function ( $post_states, $post ) use ( $container ) {

			foreach ( $container[ self::REQUIRED_PAGES ] as $page ) {
				/** @var Required_Page $page */
				$post_states = $page->add_post_state( $post_states, $post );
			}

			return $post_states;
		} ), 10, 2 );

		add_action( 'bigcommerce/settings/accounts/after_page_field/page=' . Registration_Page::NAME, $this->create_callback( 'enable_registration_notice', function () use ( $container ) {
			$container[ self::REGISTRATION_PAGE ]->enable_registration_notice();
		} ), 10, 0 );

		add_action( 'the_content', $this->create_callback( 'page_content', function ( $content ) use ( $container ) {
			if ( is_page() && in_the_loop() && is_main_query() ) {
				foreach ( $container[ self::REQUIRED_PAGES ] as $page ) {
					/** @var Required_Page $page */
					$content = $page->filter_content( get_the_ID(), $content );
				}
			}
			return $content;
		} ) );
	}
}