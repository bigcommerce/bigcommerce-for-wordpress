<?php


namespace BigCommerce\Post_Types\Product;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Webhooks\Webhook_Cron_Tasks;

/**
 * Class Single_Product_Sync
 *
 * Adds a link to the post actions row to sync a single product
 */
class Single_Product_Sync {
	const ACTION = 'sync-product';

	/**
	 * @param array    $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 * @filter post_row_actions
	 */
	public function add_row_action( $actions, $post ) {
		if ( get_post_type( $post ) !== Product::NAME ) {
			return $actions;
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$actions[ self::ACTION ] = sprintf( '<a href="%s" class="%s" title="%s">%s</a>', esc_url( $this->get_sync_url( $post ) ), sanitize_html_class( self::ACTION ), esc_attr( __( 'Synchronize the product with the latest data from the BigCommerce API', 'bigcommerce' ) ), __( 'Re-sync', 'bigcommerce' ) );

		return $actions;
	}

	private function get_sync_url( \WP_Post $post ) {
		$url = add_query_arg( [
			'action'      => self::ACTION,
			'post_id'     => $post->ID,
			'redirect_to' => urlencode( add_query_arg( [ 'post_type' => Product::NAME ], admin_url( 'edit.php' ) ) ),
		], admin_url( 'admin-post.php' ) );

		$url = wp_nonce_url( $url, self::ACTION . $post->ID );

		return $url;
	}

	/**
	 * @return void
	 * @action admin_post_ . self::ACTIOn
	 */
	public function handle_request() {
		$post_id = filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$nonce   = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
		if ( empty( $post_id ) || ! wp_verify_nonce( $nonce, self::ACTION . $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html( __( 'Invalid request', 'bigcommerce' ) ), esc_html( __( 'Invalid request', 'bigcommerce' ) ), 401 );
			exit;
		}

		$error = $this->sync_product( $post_id );
		if ( $error->has_errors() ) {
			add_settings_error( self::ACTION, $error->get_error_code(), $error->get_error_message() );
		} else {
			add_settings_error( self::ACTION, 'success', sprintf( __( 'Imported product: %s', 'bigcommerce' ), esc_html( get_the_title( $post_id ) ) ), 'updated' );
		}
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		$redirect = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_URL ) ?: admin_url( 'edit.php?post_type=' . Product::NAME );
		$redirect = add_query_arg( [ 'settings-updated' => 1 ], $redirect );
		wp_safe_redirect( esc_url_raw( $redirect ), 303 );
		exit();
	}

	/**
	 * @param int $post_id
	 *
	 * @return \WP_Error
	 */
	private function sync_product( $post_id ) {
		$error = new \WP_Error();

		/**
		 * Error triggered when updating a product fails
		 *
		 * @param string $message
		 */
		$error_handler = function ( $message ) use ( $error ) {
			$error->add( 'import_error', sprintf(
				__( 'Error updating product. Message: %s', 'bigcommerce' ),
				$message
			) );
		};

		add_action( 'bigcommerce/import/error', $error_handler, 0, 1 );

		$product = new Product( $post_id );
		update_post_meta( $post_id, Product::REQUIRES_REFRESH_META_KEY, 1 );

		/*
		 * If the caching client is in use, clear the
		 * generation key to get a fresh response.
		 */
		wp_cache_delete( 'generation_key', 'bigcommerce_api' );

		try {
			/*
			 * Piggyback on the update handler already hooked in for handling
			 * webhook requests
			 */
			do_action( Webhook_Cron_Tasks::UPDATE_PRODUCT, $product->bc_id() );
		} catch ( \Exception $e ) {
			$error_handler( $e->getMessage() );
		}

		remove_filter( 'bigcommerce/import/error', $error_handler, 0 );

		return $error;
	}
}