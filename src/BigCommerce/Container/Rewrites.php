<?php

namespace BigCommerce\Container;

use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Rewrites\Action_Endpoint;
use BigCommerce\Rewrites\Flusher;
use Pimple\Container;

/**
 * Handles rewrite rules and flushing mechanisms for the BigCommerce container.
 *
 * Registers services and hooks to manage custom rewrite rules, flushing
 * permalinks, and handling requests via custom endpoints.
 */
class Rewrites extends Provider {
    /**
     * Key for the Action_Endpoint service.
     *
     * @var string
     */
    const ACTION_ENDPOINT = 'rewrites.action_endpoint';

    /**
     * Key for the Flusher service.
     *
     * @var string
     */
    const FLUSH = 'rewrites.flush';

    /**
     * Registers rewrite-related services and hooks in the container.
     *
     * Services include:
     * - Action endpoint for managing custom route registration and requests.
     * - Flusher for handling permalink flush operations.
     *
     * @param Container $container The DI container for registering services.
     */
    public function register(Container $container) {
        /**
         * Registers the Action_Endpoint service.
         *
         * Manages the registration of a custom action route and handles incoming
         * requests for that route.
         *
         * @return Action_Endpoint The action endpoint instance.
         */
        $container[self::ACTION_ENDPOINT] = function (Container $container) {
            return new Action_Endpoint();
        };

        add_action('init', $this->create_callback('register_action_route', function () use ($container) {
            $container[self::ACTION_ENDPOINT]->register_route();
        }), 10, 0);

        add_action('parse_request', $this->create_callback('parse_action_request', function (\WP $wp) use ($container) {
            $container[self::ACTION_ENDPOINT]->handle_request($wp);
        }), 10, 1);

        /**
         * Registers the Flusher service.
         *
         * Handles flushing permalink rules and scheduling flush operations.
         *
         * @return Flusher The flusher instance.
         */
        $container[self::FLUSH] = function (Container $container) {
            return new Flusher();
        };

        add_action('wp_loaded', $this->create_callback('flush', function () use ($container) {
            $container[self::FLUSH]->do_flush();
        }), 10, 0);

        /**
         * Callback schedules a flush operation when certain product archive options are updated or added.
         */
        $schedule_flush = $this->create_callback('schedule_flush', function () use ($container) {
            $container[self::FLUSH]->schedule_flush();
        });

        add_action('update_option_' . Product_Archive::ARCHIVE_SLUG, $schedule_flush, 10, 0);
        add_action('update_option_' . Product_Archive::CATEGORY_SLUG, $schedule_flush, 10, 0);
        add_action('update_option_' . Product_Archive::BRAND_SLUG, $schedule_flush, 10, 0);
        add_action('add_option_' . Product_Archive::ARCHIVE_SLUG, $schedule_flush, 10, 0);
        add_action('add_option_' . Product_Archive::CATEGORY_SLUG, $schedule_flush, 10, 0);
        add_action('add_option_' . Product_Archive::BRAND_SLUG, $schedule_flush, 10, 0);
    }
}
