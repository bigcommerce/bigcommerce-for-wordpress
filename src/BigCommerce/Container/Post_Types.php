<?php

namespace BigCommerce\Container;

use BigCommerce\Customizer\Sections\Product_Category as Customizer;
use BigCommerce\Customizer\Sections\Product_Single;
use BigCommerce\Post_Types\Product;
use BigCommerce\Post_Types\Queue_Task;
use BigCommerce\Post_Types\Sync_Log;
use BigCommerce\Taxonomies\Channel\Channel;
use Pimple\Container;

/**
 * Handles the management of custom post types, particularly for products in the WordPress admin interface.
 * This class integrates with various hooks and filters to customize post behavior, including slug management, permalink modification,
 * featured image handling, and custom product status for unsupported products.
 *
 * It also interfaces with the BigCommerce API to manage import processes, prevent publication of certain posts, and manage product deletion.
 *
 * @package YourNamespace
 * @subpackage Post_Types
 */
class Post_Types extends Provider {
    /**
     * @var string Post type for product.
     */
    const PRODUCT = 'post_type.product';
    /**
     * @var string Configuration for product post type.
     */
    const PRODUCT_CONFIG = 'post_type.product.config';
    /**
     * @var string Query handling for product post type.
     */
    const PRODUCT_QUERY = 'post_type.product.query';
    /**
     * @var string Admin UI for product post type.
     */
    const PRODUCT_ADMIN = 'post_type.product.admin';
    /**
     * @var string Unsupported product handler.
     */
    const PRODUCT_UNSUPPORTED = 'post_type.product.unsupported';
    /**
     * @var string Product deletion handler.
     */
    const PRODUCT_DELETION = 'post_type.product.deletion';
    /**
     * @var string Store links handler for products.
     */
    const STORE_LINKS = 'post_type.product.store_links';
    /**
     * @var string Channel indicator handler for products.
     */
    const CHANNEL_INDICATOR = 'post_type.product.channel_indicator';
    /**
     * @var string Channel synchronization handler for products.
     */
    const CHANNEL_SYNC = 'post_type.product.channel_sync';
    /**
     * @var string Admin list handler for products.
     */
    const PRODUCT_ADMIN_LIST = 'post_type.product.admin_list';
    /**
     * @var string Unique slug generator for products.
     */
    const PRODUCT_UNIQUE_SLUG = 'post_type.product.unique_slug';
    /**
     * @var string Listing reset handler for products.
     */
    const LISTING_RESET = 'post_type.product.listing_reset';
    /**
     * @var string Single product resynchronization handler.
     */
    const PRODUCT_RESYNC = 'post_type.product.resync_single';
    /**
     * @var string SEO handler for products.
     */
    const PRODUCT_SEO = 'post_type.product.seo';

    /**
     * @var string Cart indicator handler.
     */
    const CART_INDICATOR = 'post_type.page.cart_indicator';
    /**
     * @var string Cart creation handler.
     */
    const CART_CREATOR = 'post_type.page.cart_creator';

    /**
     * @var string Queue task handler.
     */
    const QUEUE = 'post_type.queue_task';
    /**
     * @var string Queue task configuration.
     */
    const QUEUE_CONFIG = 'post_type.queue_task.config';

    /**
     * @var string Synchronization log handler.
     */
    const SYNC_LOG = 'post_type.sync_log';
    /**
     * @var string Configuration for synchronization logs.
     */
    const SYNC_LOG_CONFIG = 'post_type.sync_log.config';

    /**
     * @var string GraphQL products configuration.
     */
    const WPGRAPHQL_PRODUCTS = 'bigcommerce.wpgrapql_products';
    /**
     * @var string GraphQL configuration.
     */
    const WPGRAPHQL_CONFIG = 'bigcommerce.wpgrapql_config';

    /**
     * Registers all post types and hooks them into the container.
     *
     * @param Container $container The Pimple container.
     * @return void
     */
    public function register(Container $container) {
        $this->product($container);
        $this->queue($container);
        $this->sync_log($container);

        add_action('init', $this->create_callback('register', function () use ($container) {
            $container[self::PRODUCT_CONFIG]->register();
            $container[self::QUEUE_CONFIG]->register();
            $container[self::SYNC_LOG_CONFIG]->register();
            $container[self::WPGRAPHQL_PRODUCTS]->register();
        }), 1, 0);
    }

    /**
     * Configures product-related dependencies in the container.
     *
     * @param Container $container The Pimple container.
     * @return void
     */
    private function product(Container $container) {
        $container[self::PRODUCT_ADMIN_LIST] = function (Container $container) {
            return new Product\Admin_List();
        };

        $container[self::PRODUCT_CONFIG] = function (Container $container) {
            return new Product\Config(Product\Product::NAME);
        };

        $container[self::PRODUCT_QUERY] = function (Container $container) {
            return new Product\Query($container[Api::FACTORY]->catalog(), $container[Taxonomies::PRODUCT_CATEGORY_QUERY_FILTER]);
        };

        $container[self::PRODUCT_ADMIN] = function (Container $container) {
            return new Product\Admin_UI();
        };

        $container[self::WPGRAPHQL_CONFIG] = function (Container $container) {
            return new Product\WPGraph_Config();
        };

        $container[self::WPGRAPHQL_PRODUCTS] = function (Container $container) {
            return new Product\WPGraph_Product($container[self::WPGRAPHQL_CONFIG]);
        };

        $container[self::PRODUCT_UNSUPPORTED] = function (Container $container) {
            return new Product\Unsupported_Products();
        };

        $container[self::PRODUCT_DELETION] = function (Container $container) {
            return new Product\Deletion();
        };

		add_action( 'pre_get_posts', $this->create_callback( 'product_pre_get_posts', function ( \WP_Query $query ) use ( $container ) {
			$container[ self::PRODUCT_QUERY ]->filter_queries( $query );
		} ), 10, 1 );

		add_filter( 'request', $this->create_callback( 'empty_request_vars', function ( $vars ) use ( $container ) {
			return $container[ self::PRODUCT_QUERY ]->filter_empty_query_vars( $vars );
		} ), 10, 1 );

		add_filter( 'query_vars', $this->create_callback( 'product_query_vars', function ( $vars ) use ( $container ) {
			return $container[ self::PRODUCT_QUERY ]->add_query_vars( $vars );
		} ), 10, 1 );

		add_action( 'pre_handle_404', $this->create_callback( 'handle_non_visible_category', function ( $preempt ) use ( $container ) {
			if ( ! ( is_category() || is_archive() ) || is_admin() ) {
				return $preempt;
			}

			if ( get_option( Customizer::CATEGORIES_IS_VISIBLE, 'no' ) !== 'yes' ) {
				return $preempt;
			}

			$result = $container[ Taxonomies::PRODUCT_CATEGORY_QUERY_FILTER ]->get_non_visible_terms();

			if ( empty( $result) || is_wp_error($result) ) {
				return $preempt;
			}

			if ( ! in_array( get_queried_object_id(), $result ) ) {
				return $preempt;
			}

			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();

			return '';
		} ), 10, 1 );

		/**
		 * Only load the post admin hooks when on the post admin page to avoid interfering where we're not welcome
		 */
		$load_post_admin_hooks = $this->create_callback( 'load_post_php', function () use ( $container ) {
			static $loaded = false; // gutenberg calls rest_api_init even when not on rest API requests, causing this to load twice
			if ( ! $loaded ) {
				add_filter( 'wp_insert_post_data', $this->create_callback( 'prevent_slug_changes', function ( $data, $submitted ) use ( $container ) {
					return $container[ self::PRODUCT_ADMIN ]->prevent_slug_changes( $data, $submitted );
				} ), 10, 2 );

				add_filter( 'get_sample_permalink_html', $this->create_callback( 'override_sample_permalink_html', function ( $html, $post_id, $title, $slug, $post ) use ( $container ) {
					return $container[ self::PRODUCT_ADMIN ]->override_sample_permalink_html( $html, $post_id, $title, $slug, $post );
				} ), 10, 5 );

				add_action( 'add_meta_boxes_' . Product\Product::NAME, $this->create_callback( 'remove_featured_image_meta_box', function ( \WP_Post $post ) use ( $container ) {
					$container[ self::PRODUCT_ADMIN ]->remove_featured_image_meta_box( $post );
				} ), 10, 1 );

				$loaded = true;
			}
		} );

		add_action( 'load-post.php', $load_post_admin_hooks, 10, 0 );
		add_action( 'wp_ajax_inline-save', $load_post_admin_hooks, 0, 0 );
		add_action( 'load-edit.php', $load_post_admin_hooks, 10, 0 );
		add_action( 'rest_api_init', $load_post_admin_hooks, 10, 0 );

		add_filter( 'pre_handle_404', $this->create_callback( 'handle_product_out_of_stock_behaviour', function ( $preempt ) use ( $container ) {
			global $wp_query;
			if ( empty($wp_query->query['post_type']) || $wp_query->query['post_type'] !== Product\Product::NAME || is_admin() ) {
				return $preempt;
			}

			$product  = new Product\Product( get_queried_object_id() );
			$redirect = $product->get_redirect_product_link();

			if ( ! empty( $redirect ) ) {
				wp_safe_redirect( $redirect );
				return;
			}

			return $preempt;
		} ), 10, 1 );

		add_filter( 'views_edit-' . Product\Product::NAME, $this->create_callback( 'list_table_import_status', function ( $views ) use ( $container ) {
			return $container[ self::PRODUCT_ADMIN ]->list_table_import_status( $views );
		} ), 10, 1 );

		add_filter( 'views_edit-' . Product\Product::NAME, $this->create_callback( 'list_import_tooltip', function ( $views ) use ( $container ) {
			return $container[ self::PRODUCT_ADMIN ]->list_import_tooltip( $views );
		} ), 10, 1 );

		add_filter( 'views_edit-' . Product\Product::NAME, $this->create_callback( 'list_table_manage_link', function ( $views ) use ( $container ) {
			return $container[ self::PRODUCT_ADMIN ]->list_table_manage_link( $views );
		} ), 2, 1 );

		add_action( 'admin_notices', $this->create_callback( 'list_table_admin_notices', function () use ( $container ) {
			$container[ self::PRODUCT_ADMIN ]->list_table_admin_notices();
		} ), 10, 0 );

		add_filter( 'map_meta_cap', $this->create_callback( 'unsupported_meta_caps', function ( $caps, $cap, $user_id, $args ) use ( $container ) {
			return $container[ self::PRODUCT_UNSUPPORTED ]->disallow_publication( $caps, $cap, $user_id, $args );
		} ), 10, 4 );

		add_filter( 'display_post_states', $this->create_callback( 'unsupported_post_state', function ( $post_states, $post ) use ( $container ) {
			return $container[ self::PRODUCT_UNSUPPORTED ]->show_unsupported_status( $post_states, $post );
		} ), 10, 4 );

		add_filter( 'wp_insert_post_data', $this->create_callback( 'prevent_publication', function ( $data, $postarr ) use ( $container ) {
			return $container[ self::PRODUCT_UNSUPPORTED ]->prevent_publication( $data, $postarr );
		} ), 10, 2 );

		add_action( 'before_delete_post', $this->create_callback( 'delete_product', function ( $post_id ) use ( $container ) {
			$container[ self::PRODUCT_DELETION ]->delete_product_data( $post_id );
		} ), 10, 1 );

		add_action( 'bigcommerce/import/before', function () {
			add_filter( 'bigcommerce/channel/listing/should_update', '__return_false', 10, 0 );
			add_filter( 'bigcommerce/channel/listing/should_delete', '__return_false', 10, 0 );
		}, 10, 0 );

		add_action( 'bigcommerce/import/after', function () {
			remove_filter( 'bigcommerce/channel/listing/should_update', '__return_false', 10 );
			remove_filter( 'bigcommerce/channel/listing/should_delete', '__return_false', 10 );
		}, 10, 0 );

		add_filter( 'manage_bigcommerce_product_posts_columns', $this->create_callback( 'add_bigcommerce_product_id_column', function ( $columns ) use ( $container ) {
			return $container[ self::PRODUCT_ADMIN_LIST ]->add_product_list_columns( $columns );
		} ), 5, 1 );

		add_action( 'manage_bigcommerce_product_posts_custom_column', $this->create_callback( 'add_bigcommerce_product_id_values', function ( $columns, $post_id ) use ( $container ) {
			$container[ self::PRODUCT_ADMIN_LIST ]->get_bigcommerce_product_id_value( $columns, $post_id );
		} ), 5, 2 );

		add_action( 'manage_bigcommerce_product_posts_custom_column', $this->create_callback( 'add_bigcommerce_product_thumbnail_image', function ( $columns, $post_id ) use ( $container ) {
			$container[ self::PRODUCT_ADMIN_LIST ]->get_bigcommerce_product_thumbnail_value( $columns, $post_id );
		} ), 5, 2 );


		$this->product_store_links( $container );
		$this->product_listing_reset( $container );
		$this->product_resync( $container );
		$this->product_channel_indicator( $container );
		$this->channel_sync( $container );
		$this->product_slugs( $container );
		$this->product_seo( $container );
	}

		/**
		 * Queues the configuration for the Queue_Task.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function queue( Container $container ) {
			$container[ self::QUEUE_CONFIG ] = function ( Container $container ) {
				return new Queue_Task\Config( Queue_Task\Queue_Task::NAME );
			};
		}

		/**
		 * Sets up the synchronization logging.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function sync_log( Container $container ) {
			$container[ self::SYNC_LOG_CONFIG ] = function ( Container $container ) {
				return new Sync_Log\Config( Sync_Log\Sync_Log::NAME );
			};

			$container[ self::SYNC_LOG ] = function ( Container $container ) {
				return new Sync_Log\Sync_Log;
			};

			add_action( 'bigcommerce/import/start', $this->create_callback( 'sync_log_create_sync', function ( $error ) use ( $container ) {
				$container[ self::SYNC_LOG ]->create_sync();
			} ) );

			add_action( 'bigcommerce/import/error', $this->create_callback( 'sync_log_log_error', function ( $error ) use ( $container ) {
				$container[ self::SYNC_LOG ]->log_error( $error );
			} ) );

			add_action( 'bigcommerce/import/logs/rotate', $this->create_callback( 'sync_log_complete_sync', function ( $log ) use ( $container ) {
				$container[ self::SYNC_LOG ]->complete_sync( $log );
			} ) );

			add_filter( 'bigcommerce/diagnostics', $this->create_callback( 'sync_log_diagnostics', function ( $data ) use ( $container ) {
				return $container[ self::SYNC_LOG ]->diagnostic_data( $data );
			} ), 10, 1 );
		}

		/**
		 * Adds product store links to various admin actions.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function product_store_links( Container $container ) {
			$container[ self::STORE_LINKS ] = function ( Container $container ) {
				return new Product\Store_Links( $container[ Api::FACTORY ] );
			};

			add_filter( 'post_row_actions', $this->create_callback( 'post_row_link', function ( $actions, $post ) use ( $container ) {
				return $container[ self::STORE_LINKS ]->add_row_action( $actions, $post );
			} ), 10, 2 );

			add_filter( 'post_submitbox_misc_actions', $this->create_callback( 'submitbox_store_link', function ( $post ) use ( $container ) {
				$container[ self::STORE_LINKS ]->add_submitbox_link( $post );
			} ), 10, 1 );

			add_filter( 'bigcommerce/gutenberg/js_config', $this->create_callback( 'gutenberg_store_link', function ( $data ) use ( $container ) {
				return $container[ self::STORE_LINKS ]->add_link_to_gutenberg_config( $data );
			} ), 10, 1 );

			add_action( 'admin_bar_menu', $this->create_callback( 'admin_bar_edit_link', function ( $wp_admin_bar ) use ( $container ) {
				$container[ self::STORE_LINKS ]->modify_edit_product_links_admin_bar( $wp_admin_bar );
			} ), 81, 1 );
		}

		/**
		 * Resets product listings based on specific actions.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function product_listing_reset( Container $container ) {
			$container[ self::LISTING_RESET ] = function ( Container $container ) {
				return new Product\Reset_Listing();
			};

			add_filter( 'post_row_actions', $this->create_callback( 'post_row_reset', function ( $actions, $post ) use ( $container ) {
				return $container[ self::LISTING_RESET ]->add_row_action( $actions, $post );
			} ), 10, 2 );

			add_action( 'admin_post_' . Product\Reset_Listing::ACTION, $this->create_callback( 'handle_reset_listing', function () use ( $container ) {
				$container[ self::LISTING_RESET ]->handle_request();
			} ), 10, 0 );
		}

		/**
		 * Resyncs a single product based on user action.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function product_resync( Container $container ) {
			$container[ self::PRODUCT_RESYNC ] = function ( Container $container ) {
				return new Product\Single_Product_Sync();
			};

			add_filter( 'post_row_actions', $this->create_callback( 'post_row_resync', function ( $actions, $post ) use ( $container ) {
				return $container[ self::PRODUCT_RESYNC ]->add_row_action( $actions, $post );
			} ), 10, 2 );

			add_action( 'admin_post_' . Product\Single_Product_Sync::ACTION, $this->create_callback( 'handle_resync_product', function () use ( $container ) {
				$container[ self::PRODUCT_RESYNC ]->handle_request();
			} ), 10, 0 );
		}

		/**
		 * Adds a channel indicator for products when enabled.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function product_channel_indicator( Container $container ) {
			$container[ self::CHANNEL_INDICATOR ] = function ( Container $container ) {
				return new Product\Channel_Indicator();
			};

			add_action( 'post_submitbox_misc_actions', $this->create_callback( 'submitbox_channel_indicator', function ( $post ) use ( $container ) {
				if ( Channel::multichannel_enabled() ) {
					$container[ self::CHANNEL_INDICATOR ]->add_submitbox_message( $post );
				}
			} ), 10, 1 );

			add_filter( 'bigcommerce/gutenberg/js_config', $this->create_callback( 'gutenberg_channel_indicator', function ( $data ) use ( $container ) {
				if ( Channel::multichannel_enabled() ) {
					return $container[ self::CHANNEL_INDICATOR ]->add_message_to_gutenberg_config( $data );
				}

				return $data;
			} ), 10, 1 );
		}

		/**
		 * Synchronizes product data with external channels.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function channel_sync( Container $container ) {
			$container[ self::CHANNEL_SYNC ] = function ( Container $container ) {
				return new Product\Channel_Sync( $container[ Api::FACTORY ]->channels() );
			};

			add_action( 'save_post', $this->create_callback( 'sync_to_channel', function ( $post_id, $post ) use ( $container ) {
				$container[ self::CHANNEL_SYNC ]->post_updated( $post_id, $post );
			} ), 10, 2 );

			add_action( 'before_delete_post', $this->create_callback( 'delete_from_channel', function ( $post_id ) use ( $container ) {
				$container[ self::CHANNEL_SYNC ]->post_deleted( $post_id );
			} ), 5, 1 );
		}

		/**
		 * Adds unique slugs for products based on the channel context.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function product_slugs( Container $container ) {
			$container[ self::PRODUCT_UNIQUE_SLUG ] = function ( Container $container ) {
				return new Product\Unique_Slug_Filter();
			};

			add_filter( 'wp_unique_post_slug', $this->create_callback( 'unique_slug_per_channel', function ( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug ) use ( $container ) {
				if ( Channel::multichannel_enabled() ) {
					return $container[ self::PRODUCT_UNIQUE_SLUG ]->get_unique_slug( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug );
				}

				return $slug;
			} ), 10, 6 );
		}

		/**
		 * Handles product SEO settings.
		 *
		 * @param Container $container The service container.
		 *
		 * @return void
		 */
		private function product_seo( Container $container ) {
			$container[ self::PRODUCT_SEO ] = function ( Container $container ) {
				return new Product\Seo();
			};

			add_filter( 'wp_title_parts', $this->create_callback( 'product_wp_title', function ( $title_parts ) use ( $container ) {
				return $container[ self::PRODUCT_SEO ]->filter_wp_title( $title_parts );
			} ), 10, 1 );

			add_filter( 'document_title_parts', $this->create_callback( 'product_document_title', function ( $title_parts ) use ( $container ) {
				return $container[ self::PRODUCT_SEO ]->filter_document_title( $title_parts );
			} ), 10, 1 );

			add_filter( 'wp_head', $this->create_callback( 'product_page_meta_description', function () use ( $container ) {
				if ( get_option( Product_Single::META_DESC_DISABLE, 'yes' ) !== 'yes' ) {
					return null;
				}
	
				return $container[ self::PRODUCT_SEO ]->print_meta_description();
			} ), 0, 0 );
		}
}
