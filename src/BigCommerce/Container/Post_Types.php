<?php


namespace BigCommerce\Container;

use BigCommerce\Post_Types\Product;
use BigCommerce\Post_Types\Queue_Task;
use BigCommerce\Taxonomies\Channel\Channel;
use Pimple\Container;

class Post_Types extends Provider {
	const PRODUCT             = 'post_type.product';
	const PRODUCT_CONFIG      = 'post_type.product.config';
	const PRODUCT_QUERY       = 'post_type.product.query';
	const PRODUCT_ADMIN       = 'post_type.product.admin';
	const PRODUCT_UNSUPPORTED = 'post_type.product.unsupported';
	const PRODUCT_DELETION    = 'post_type.product.deletion';
	const STORE_LINKS         = 'post_type.product.store_links';
	const CHANNEL_INDICATOR   = 'post_type.product.channel_indicator';
	const CHANNEL_SYNC        = 'post_type.product.channel_sync';
	const PRODUCT_ADMIN_LIST  = 'post_type.product.admin_list';
	const PRODUCT_UNIQUE_SLUG = 'post_type.product.unique_slug';
	const LISTING_RESET       = 'post_type.product.listing_reset';
	const PRODUCT_RESYNC      = 'post_type.product.resync_single';
	const PRODUCT_SEO         = 'post_type.product.seo';

	const CART_INDICATOR = 'post_type.page.cart_indicator';
	const CART_CREATOR   = 'post_type.page.cart_creator';

	const QUEUE        = 'post_type.queue_task';
	const QUEUE_CONFIG = 'post_type.queue_task.config';

	public function register( Container $container ) {
		$this->product( $container );
		$this->queue( $container );

		add_action( 'init', $this->create_callback( 'register', function () use ( $container ) {
			$container[ self::PRODUCT_CONFIG ]->register();
			$container[ self::QUEUE_CONFIG ]->register();
		} ), 1, 0 );
	}

	private function product( Container $container ) {

		$container[ self::PRODUCT_ADMIN_LIST ] = function ( Container $container ) {
			return new Product\Admin_List();
		};

		$container[ self::PRODUCT_CONFIG ] = function ( Container $container ) {
			return new Product\Config( Product\Product::NAME );
		};

		$container[ self::PRODUCT_QUERY ] = function ( Container $container ) {
			return new Product\Query();
		};

		$container[ self::PRODUCT_ADMIN ] = function ( Container $container ) {
			return new Product\Admin_UI();
		};

		$container[ self::PRODUCT_UNSUPPORTED ] = function ( Container $container ) {
			return new Product\Unsupported_Products();
		};

		$container[ self::PRODUCT_DELETION ] = function ( Container $container ) {
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

		add_filter( 'views_edit-' . Product\Product::NAME, $this->create_callback( 'list_table_import_status', function ( $views ) use ( $container ) {
			return $container[ self::PRODUCT_ADMIN ]->list_table_import_status( $views );
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

		// do not push updates back upstream when running an import
		add_action( 'bigcommerce/import/before', function () {
			add_filter( 'bigcommerce/channel/listing/should_update', '__return_false', 10, 0 );
			add_filter( 'bigcommerce/channel/listing/should_delete', '__return_false', 10, 0 );
		}, 10, 0 );

		add_action( 'bigcommerce/import/after', function () {
			remove_filter( 'bigcommerce/channel/listing/should_update', '__return_false', 10 );
			remove_filter( 'bigcommerce/channel/listing/should_delete', '__return_false', 10 );
		}, 10, 0 );

		// Admin extra columns list
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

	private function queue( Container $container ) {
		$container[ self::QUEUE_CONFIG ] = function ( Container $container ) {
			return new Queue_Task\Config( Queue_Task\Queue_Task::NAME );
		};
	}

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
	 * @param Container $container
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
			return $container[ self::PRODUCT_SEO ]->print_meta_description();
		} ), 0, 0 );
	}
}
