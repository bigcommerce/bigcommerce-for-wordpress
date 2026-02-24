<?php


namespace BigCommerce\Post_Types\Product;

use BigCommerce\Api\v3\ApiException;

/**
 * Class Reset_Listing
 *
 * Resets listing data to defaults, reconnecting its title
 * and description to the base product.
 *
 * @package BigCommerce\Post_Types\Product
 */
class Reset_Listing {
	/**
	 * The action identifier for resetting the listing.
	 * @var string
	 */
	const ACTION = 'reset-listing';

	/**
	 * Adds a custom action link to the post row actions for products.
	 *
	 * @param array    $actions The current post row actions.
	 * @param \WP_Post $post    The post object.
	 *
	 * @return array Modified post row actions.
	 */
	public function add_row_action( $actions, $post ) {
		if ( get_post_type( $post ) !== Product::NAME ) {
			return $actions;
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		if ( ! $this->has_overrides( $post ) ) {
			return $actions; // nothing to reset
		}
		$actions[ self::ACTION ] = sprintf( '<a href="%s" class="%s" title="%s">%s</a>', esc_url( $this->get_reset_url( $post ) ), sanitize_html_class( self::ACTION ), esc_attr( __( 'Reset title and description overrides in the product listing, allowing this product to re-sync with the base product in BigCommerce.', 'bigcommerce' ) ), __( 'Reset Listing', 'bigcommerce' ) );

		return $actions;
	}

	/**
	 * Checks if the product listing overrides base product fields.
	 *
	 * @param \WP_Post $post The product post.
	 *
	 * @return bool True if overrides exist, false otherwise.
	 */
	public function has_overrides( \WP_Post $post ) {
		$product = new Product( $post->ID );
		$listing = $product->get_listing_data();
		if ( empty( $listing ) ) {
			return false;
		}
		if ( empty( $listing->name ) && empty( $listing->description ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Generates the URL for resetting a product listing.
	 *
	 * @param \WP_Post $post The product post.
	 *
	 * @return string The reset URL.
	 */
	public function get_reset_url( \WP_Post $post ) {
		$url = add_query_arg( [
			'action'      => self::ACTION,
			'post_id'     => $post->ID,
			'redirect_to' => urlencode( add_query_arg( [ 'post_type' => Product::NAME ], admin_url( 'edit.php' ) ) ),
		], admin_url( 'admin-post.php' ) );

		$url = wp_nonce_url( $url, self::ACTION . $post->ID );

		return $url;
	}

	/**
	 * Handles the reset listing request.
	 *
	 * Validates the request, resets the listing, and redirects the user.
	 *
	 * @return void
	 */
	public function handle_request() {
		$post_id = filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$nonce   = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
		if ( empty( $post_id ) || ! wp_verify_nonce( $nonce, self::ACTION . $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html( __( 'Invalid request', 'bigcommerce' ) ), esc_html( __( 'Invalid request', 'bigcommerce' ) ), 401 );
			exit;
		}

		$error = $this->reset_listing( $post_id );
		if ( $error->has_errors() ) {
			add_settings_error( self::ACTION, $error->get_error_code(), $error->get_error_message() );
		} else {
			add_settings_error( self::ACTION, 'success', sprintf( __( 'Reset listing for %s', 'bigcommerce' ), esc_html( get_the_title( $post_id ) ) ), 'updated' );
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
	private function reset_listing( $post_id ) {
		$product = new Product( $post_id );
		$source  = $product->get_source_data();

		$title       = $source->name;
		$description = $source->description;

		/*
		 * Create our own callback instead of __return_empty_string() so that
		 * we don't inadvertently unhook someone else's filter later
		 */
		$empty = function () {
			return '';
		};

		add_filter( 'bigcommerce/channel/listing/title', $empty, 10, 0 );
		add_filter( 'bigcommerce/channel/listing/description', $empty, 10, 0 );

		$error = new \WP_Error();

		/**
		 * Error triggered when updating a listing fails
		 *
		 * @param int          $channel_id
		 * @param int          $listing_id
		 * @param ApiException $e
		 */
		$error_handler = function ( $channel_id, $listing_id, $e ) use ( $error ) {
			$error->add( 'update_error', sprintf(
				__( 'Error sending listing updates to the BigCommerce API. Message: %s', 'bigcommerce' ),
				$e->getMessage()
			) );
		};

		add_action( 'bigcommerce/channel/error/could_not_fetch_listing', $error_handler, 10, 3 );
		add_action( 'bigcommerce/channel/error/could_not_update_listing', $error_handler, 10, 3 );

		/**
		 * Primary effect: updates the product title and description to match the base product
		 * Side effect: will cause the listing to be updated with empty values
		 *
		 * @see \BigCommerce\Post_Types\Product\Channel_Sync::post_updated()
		 */
		wp_update_post( [
			'ID'           => $post_id,
			'post_title'   => $title,
			'post_content' => $description,
		] );

		remove_filter( 'bigcommerce/channel/listing/title', $empty, 10 );
		remove_filter( 'bigcommerce/channel/listing/description', $empty, 10 );

		return $error;
	}
}