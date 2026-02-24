<?php

namespace BigCommerce\Container;

use BigCommerce\Widgets\Mini_Cart_Widget;
use BigCommerce\Widgets\Product_Category_Widget;
use BigCommerce\Widgets\Currency_Switcher_Widget;
use Pimple\Container;

/**
 * Registers and manages widgets for the application.
 * Includes widgets for product categories, mini cart, and currency switcher.
 *
 * @package BigCommerce\Container
 */
class Widgets extends Provider {
    /**
     * The key used to store the widget list in the container.
     *
     * @var string
     */
    const WIDGET_LIST = 'widgets.list';

    /**
     * Registers the widgets and initializes the widget registration action.
     *
     * Registers a list of widgets and hooks them into WordPress using `widgets_init`.
     *
     * @param Container $container The service container to register services into.
     */
    public function register( Container $container ) {
        // Register the list of widgets.
        $container[ self::WIDGET_LIST ] = function ( Container $container ) {
            return [
                Product_Category_Widget::class,
                Mini_Cart_Widget::class,
                Currency_Switcher_Widget::class,
            ];
        };

        add_action( 'widgets_init', $this->create_callback( 'widgets_init', function () use ( $container ) {
            foreach ( $container[ self::WIDGET_LIST ] as $class ) {
                register_widget( $class );
            }
        } ), 10, 0 );
    }
}
