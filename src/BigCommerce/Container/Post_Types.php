<?php


namespace BigCommerce\Container;

use BigCommerce\Post_Types\Product;
use Pimple\Container;

class Post_Types extends Provider {
	const PRODUCT             = 'post_type.product';
	const PRODUCT_CONFIG      = 'post_type.product.config';
	const PRODUCT_QUERY       = 'post_type.product.query';
	const PRODUCT_ADMIN       = 'post_type.product.admin';
	const PRODUCT_UNSUPPORTED = 'post_type.product.unsupported';
	const PRODUCT_DELETION    = 'post_type.product.deletion';
	const STORE_LINKS         = 'post_type.product.store_links';

	const CART_INDICATOR = 'post_type.page.cart_indicator';
	const CART_CREATOR   = 'post_type.page.cart_creator';

	public function register( Container $container ) {
		$this->product( $container );

		add_action( 'init', $this->create_callback( 'register', function () use ( $container ) {
			$container[ self::PRODUCT_CONFIG ]->register();
		} ), 1, 0 );
	}

	private function product( Container $container ) {
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
				add_filter( 'wp_insert_post_data', $this->create_callback( 'prevent_title_changes', function ( $data, $submitted ) use ( $container ) {
					return $container[ self::PRODUCT_ADMIN ]->prevent_title_changes( $data, $submitted );
				} ), 10, 2 );

				add_action( 'edit_form_before_permalink', $this->create_callback( 'insert_static_title', function ( \WP_Post $post ) use ( $container ) {
					$container[ self::PRODUCT_ADMIN ]->insert_static_title( $post );
				} ), 10, 1 );

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

		add_filter( 'map_meta_cap', $this->create_callback( 'unsupported_meta_caps', function ( $caps, $cap, $user_id, $args ) use ( $container ) {
			return $container[ self::PRODUCT_UNSUPPORTED ]->disallow_publication( $caps, $cap, $user_id, $args );
		} ), 10, 4 );
		add_filter( 'display_post_states', $this->create_callback( 'unsupported_post_state', function ( $post_states, $post ) use ( $container ) {
			return $container[ self::PRODUCT_UNSUPPORTED ]->show_unsupported_status( $post_states, $post );
		} ), 10, 4 );
		add_filter( 'wp_insert_post_data', $this->create_callback( 'prevent_publication', function ( $data, $postarr ) use ( $container ) {
			return $container[ self::PRODUCT_UNSUPPORTED ]->prevent_publication( $data, $postarr );
		} ), 10, 2 );

		$container[ self::STORE_LINKS ] = function ( Container $container ) {
			return new Product\Store_Links( $container[ Api::FACTORY ] );
		};
		add_filter( 'post_row_actions', $this->create_callback( 'post_row_link', function ( $actions, $post ) use ( $container ) {
			return $container[ self::STORE_LINKS ]->add_row_action( $actions, $post );
		} ), 10, 2 );
		add_filter( 'post_submitbox_misc_actions', $this->create_callback( 'submitbox_link', function ( $post ) use ( $container ) {
			$container[ self::STORE_LINKS ]->add_submitbox_link( $post );
		} ), 10, 1 );
		add_filter( 'bigcommerce/gutenberg/js_config', $this->create_callback( 'gutenberg_link', function ( $data ) use ( $container ) {
			return $container[ self::STORE_LINKS ]->add_link_to_gutenberg_config( $data );
		} ), 10, 1 );

		add_action( 'before_delete_post', $this->create_callback( 'delete_product', function ( $post_id ) use ( $container ) {
			$container[ self::PRODUCT_DELETION ]->delete_product_data( $post_id );
		} ), 10, 1 );
	}
}