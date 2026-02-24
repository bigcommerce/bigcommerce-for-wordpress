<?php

namespace BigCommerce\Container;

use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Check_Balance_Page;
use BigCommerce\Pages\Checkout_Page;
use BigCommerce\Pages\Checkout_Complete_Page;
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

/**
 * Provides page-related services and ensures required pages exist for BigCommerce integration.
 */
class Pages extends Provider {
    /**
     * Identifier for required pages collection.
     *
     * @var string
     */
    const REQUIRED_PAGES = 'pages.required_pages';

    /**
     * Identifier for the cart page.
     *
     * @var string
     */
    const CART_PAGE = 'pages.cart';

    /**
     * Identifier for the checkout page.
     *
     * @var string
     */
    const CHECKOUT_PAGE = 'pages.checkout';

    /**
     * Identifier for the checkout complete page.
     *
     * @var string
     */
    const CHECKOUT_COMPLETE_PAGE = 'pages.checkout.complete';

    /**
     * Identifier for the login page.
     *
     * @var string
     */
    const LOGIN_PAGE = 'pages.login';

    /**
     * Identifier for the registration page.
     *
     * @var string
     */
    const REGISTRATION_PAGE = 'pages.register';

    /**
     * Identifier for the account page.
     *
     * @var string
     */
    const ACCOUNT_PAGE = 'pages.account';

    /**
     * Identifier for the address page.
     *
     * @var string
     */
    const ADDRESS_PAGE = 'pages.address';

    /**
     * Identifier for the orders page.
     *
     * @var string
     */
    const ORDERS_PAGE = 'pages.orders';

    /**
     * Identifier for purchasing gift certificates.
     *
     * @var string
     */
    const GIFT_PURCHACE = 'pages.gift_certificate.purchase';

    /**
     * Identifier for checking gift certificate balance.
     *
     * @var string
     */
    const GIFT_BALANCE = 'pages.gift_certificate.balance';

    /**
     * Identifier for the shipping and returns page.
     *
     * @var string
     */
    const SHIPPING_PAGE = 'pages.shipping_returns';

    /**
     * Identifier for the user's wishlist page.
     *
     * @var string
     */
    const WISHLIST_USER = 'pages.wishlist.user';

    /**
     * Identifier for the public wishlist page.
     *
     * @var string
     */
    const WISHLIST_PUBLIC = 'pages.wishlist.public';

    /**
     * Registers page-related services into the Pimple container.
     *
     * @param Container $container The dependency injection container.
     * @return void
     */
    public function register( Container $container ) {
        $container[ self::REQUIRED_PAGES ] = function ( Container $container ) {
            $pages = [
                $container[ self::LOGIN_PAGE ],
                $container[ self::ACCOUNT_PAGE ],
                $container[ self::ADDRESS_PAGE ],
                $container[ self::ORDERS_PAGE ],
                $container[ self::SHIPPING_PAGE ],
                $container[ self::CHECKOUT_COMPLETE_PAGE ],
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

        // Define individual page services with corresponding classes
        $container[ self::CART_PAGE ] = function ( Container $container ) {
            return new Cart_Page();
        };

        $container[ self::CHECKOUT_PAGE ] = function ( Container $container ) {
            return new Checkout_Page();
        };

        $container[ self::CHECKOUT_COMPLETE_PAGE ] = function ( Container $container ) {
            return new Checkout_Complete_Page();
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

        $container[ self::WISHLIST_USER ] = function ( Container $container ) {
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
        } ), 5, 1 );
    }
}
