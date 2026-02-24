<?php

namespace BigCommerce\Container;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Reviews\Product_Update_Listener;
use BigCommerce\Reviews\Review_Cache;
use BigCommerce\Reviews\Review_Fetcher;
use Pimple\Container;

/**
 * Provides functionality for managing reviews in the BigCommerce container.
 *
 * Registers services for handling review-related operations, including caching,
 * fetching, and product updates.
 */
class Reviews extends Provider {
    /**
     * Key for the product listener service.
     * 
     * @var string
     */
    const PRODUCT_LISTENER = 'reviews.product_listener';

    /**
     * Key for the review fetcher service.
     * 
     * @var string
     */
    const FETCHER = 'reviews.fetcher';

    /**
     * Key for the review cache service.
     * 
     * @var string
     */
    const CACHER = 'reviews.cacher';

    /**
     * Registers review-related services in the container.
     *
     * Services include:
     * - Product update listener for tracking metadata changes.
     * - Review fetcher for retrieving reviews from the API.
     * - Review cache for caching reviews and updating the cache on product updates.
     *
     * @param Container $container The DI container for registering services.
     */
    public function register(Container $container) {
        /**
         * Registers the Product_Update_Listener service.
         *
         * Listens for updates to product metadata and processes them accordingly.
         *
         * @return Product_Update_Listener The product update listener instance.
         */
        $container[self::PRODUCT_LISTENER] = function (Container $container) {
            return new Product_Update_Listener();
        };

        /**
         * Callback for handling product meta updates.
         *
         * Triggered when product metadata is added or updated.
         *
         * @param int    $meta_id   The ID of the metadata entry.
         * @param int    $post_id   The ID of the product post.
         * @param string $meta_key  The metadata key.
         * @param mixed  $meta_value The metadata value.
         */
        $meta_update = $this->create_callback('product_meta_updated', function ($meta_id, $post_id, $meta_key, $meta_value) use ($container) {
            $container[self::PRODUCT_LISTENER]->meta_updated($meta_id, $post_id, $meta_key, $meta_value);
        });
        add_action('added_post_meta', $meta_update, 10, 4);
        add_action('updated_post_meta', $meta_update, 10, 4);

        /**
         * Registers the Review_Fetcher service.
         *
         * Fetches reviews from the API using the catalog client.
         *
         * @return Review_Fetcher The review fetcher instance.
         */
        $container[self::FETCHER] = function (Container $container) {
            return new Review_Fetcher($container[Api::FACTORY]->catalog());
        };

        /**
         * Registers the Review_Cache service.
         *
         * Handles caching of reviews and updating the cache when necessary.
         *
         * @return Review_Cache The review cache instance.
         */
        $container[self::CACHER] = function (Container $container) {
            return new Review_Cache($container[self::FETCHER]);
        };

        add_action(Product_Update_Listener::TRIGGER_UPDATE, $this->create_callback('update_cache', function ($product_id) use ($container) {
            $container[self::CACHER]->update_cache($product_id);
        }), 10, 1);
    }
}
