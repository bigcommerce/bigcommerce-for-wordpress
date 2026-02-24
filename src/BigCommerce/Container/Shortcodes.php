<?php

namespace BigCommerce\Container;

use BigCommerce\Shortcodes as Codes;
use BigCommerce\Taxonomies\Channel\BC_Status;
use Pimple\Container;

/**
 * Registers shortcodes for various storefront features.
 *
 * This class handles:
 * - Shortcodes for products, cart, checkout, login, registration, account, addresses, orders, etc.
 * - Dependency injection for shortcode classes.
 * - WordPress shortcode registration.
 */
class Shortcodes extends Provider {
    /**
     * Shortcode for rendering a product list.
     *
     * @var string
     */
    const PRODUCTS = 'shortcode.products';

    /**
     * Shortcode for rendering the cart.
     *
     * @var string
     */
    const CART = 'shortcode.cart';

    /**
     * Shortcode for rendering the checkout page.
     *
     * @var string
     */
    const CHECKOUT = 'shortcode.checkout';

    /**
     * Shortcode for rendering the login form.
     *
     * @var string
     */
    const LOGIN = 'shortcode.login';

    /**
     * Shortcode for rendering the registration form.
     *
     * @var string
     */
    const REGISTER = 'shortcode.register';

    /**
     * Shortcode for rendering the account profile.
     *
     * @var string
     */
    const ACCOUNT = 'shortcode.account';

    /**
     * Shortcode for rendering the address list.
     *
     * @var string
     */
    const ADDRESS = 'shortcode.address';

    /**
     * Shortcode for rendering the order history.
     *
     * @var string
     */
    const ORDERS = 'shortcode.orders';

    /**
     * Shortcode for rendering the gift certificate form.
     *
     * @var string
     */
    const GIFT_FORM = 'shortcode.gift_certificate.form';

    /**
     * Shortcode for checking the balance of a gift certificate.
     *
     * @var string
     */
    const GIFT_BALANCE = 'shortcode.gift_certificate.balance';

    /**
     * Shortcode for rendering product reviews.
     *
     * @var string
     */
    const PRODUCT_REVIEWS = 'shortcode.products_reviews';

    /**
     * Shortcode for rendering product components.
     *
     * @var string
     */
    const PRODUCT_COMPONENTS = 'shortcode.products_components';

    /**
     * Shortcode for rendering a wishlist.
     *
     * @var string
     */
    const WISHLIST = 'shortcode.wishlist';

    /**
     * Registers all shortcodes into the DI container and attaches WordPress hooks.
     *
     * Services registered:
     * - Shortcode classes like `Products`, `Cart`, `Checkout`, etc.
     *
     * Hook registered:
     * - `after_setup_theme`: Registers WordPress shortcodes.
     *
     * @param Container $container The DI container for registering services.
     */
    public function register(Container $container) {
        // Register each shortcode class as a service.
        $container[self::PRODUCTS] = function (Container $container) {
            return new Codes\Products($container[Rest::SHORTCODE]);
        };

        $container[self::CART] = function (Container $container) {
            return new Codes\Cart($container[Api::FACTORY]->cart());
        };

        $container[self::CHECKOUT] = function (Container $container) {
            return new Codes\Checkout($container[Api::FACTORY]->cart());
        };

        $container[self::LOGIN] = function (Container $container) {
            return new Codes\Login_Form();
        };

        $container[self::REGISTER] = function (Container $container) {
            return new Codes\Registration_Form();
        };

        $container[self::ACCOUNT] = function (Container $container) {
            return new Codes\Account_Profile();
        };

        $container[self::ADDRESS] = function (Container $container) {
            return new Codes\Address_List();
        };

        $container[self::ORDERS] = function (Container $container) {
            return new Codes\Order_History($container[Rest::ORDERS_SHORTCODE]);
        };

        $container[self::GIFT_FORM] = function (Container $container) {
            return new Codes\Gift_Certificate_Form($container[Api::FACTORY]->marketing());
        };

        $container[self::GIFT_BALANCE] = function (Container $container) {
            return new Codes\Gift_Certificate_Balance($container[Api::FACTORY]->marketing());
        };

        $container[self::PRODUCT_REVIEWS] = function (Container $container) {
            return new Codes\Product_Reviews();
        };

        $container[self::PRODUCT_COMPONENTS] = function () {
            return new Codes\Product_Components();
        };

        $container[self::WISHLIST] = function (Container $container) {
            return new Codes\Wishlist($container[Api::FACTORY]->wishlists());
        };

        add_action('after_setup_theme', $this->create_callback('register', function () use ($container) {
            add_shortcode(Codes\Products::NAME, [$container[self::PRODUCTS], 'render']);
            add_shortcode(Codes\Cart::NAME, [$container[self::CART], 'render']);
            add_shortcode(Codes\Checkout::NAME, [$container[self::CHECKOUT], 'render']);
            add_shortcode(Codes\Login_Form::NAME, [$container[self::LOGIN], 'render']);
            add_shortcode(Codes\Registration_Form::NAME, [$container[self::REGISTER], 'render']);
            add_shortcode(Codes\Account_Profile::NAME, [$container[self::ACCOUNT], 'render']);
            add_shortcode(Codes\Address_List::NAME, [$container[self::ADDRESS], 'render']);
            add_shortcode(Codes\Order_History::NAME, [$container[self::ORDERS], 'render']);
            add_shortcode(Codes\Gift_Certificate_Form::NAME, [$container[self::GIFT_FORM], 'render']);
            add_shortcode(Codes\Gift_Certificate_Balance::NAME, [$container[self::GIFT_BALANCE], 'render']);
            add_shortcode(Codes\Product_Reviews::NAME, [$container[self::PRODUCT_REVIEWS], 'render']);
            add_shortcode(Codes\Product_Components::NAME, [$container[self::PRODUCT_COMPONENTS], 'render']);
            add_shortcode(Codes\Wishlist::NAME, [$container[self::WISHLIST], 'render']);
        }), 10, 0);
    }
}