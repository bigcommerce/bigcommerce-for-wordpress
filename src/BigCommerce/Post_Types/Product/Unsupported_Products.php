<?php


namespace BigCommerce\Post_Types\Product;


class Unsupported_Products {
	/**
	 * Users should not be permitted to publish unsupported posts.
	 *
	 * Unfortunately, WP does not provide granular caps for publishing
	 * posts, so we are left with a blanket cap for publishing all
	 * posts. We just apply this filter on the product single admin,
	 * but can do nothing about quick edit in the products list table.
	 *
	 * @param array  $caps    Returns the user's actual capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. Typically the object ID.
	 *
	 * @return array
	 * @filter map_meta_cap
	 */
	public function disallow_publication( $caps, $cap, $user_id, $args ) {
		if ( $cap == 'publish_posts' && is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->base == 'post' && isset( $_GET[ 'post' ] ) && $this->is_unsupported_product( (int) $_GET[ 'post' ] ) ) {
				$caps[] = 'do_not_allow';
			}
		}

		return $caps;
	}

	/**
	 * @param array    $post_states
	 * @param \WP_Post $post
	 *
	 * @return array
	 * @filter display_post_states
	 */
	public function show_unsupported_status( $post_states, $post ) {
		if ( $this->is_unsupported_product( $post->ID ) ) {
			unset( $post_states[ 'draft' ] );
			$post_states[ 'unsupported' ] = __( 'Unsupported', 'bigcommerce' );
		}

		return $post_states;
	}

	/**
	 * @param array $data    An array of sanitized attachment post data.
	 * @param array $postarr An array of unsanitized attachment post data.
	 *
	 * @return array
	 * @filter wp_insert_post_data
	 */
	public function prevent_publication( $data, $postarr ) {
		if ( ! empty( $postarr[ 'ID' ] ) && in_array( $data[ 'post_status' ], [
				'publish',
				'future',
			] ) && $this->is_unsupported_product( $postarr[ 'ID' ] ) ) {
			$data[ 'post_status' ] = 'draft';
		}

		return $data;
	}

	private function is_unsupported_product( $post_id ) {
		if ( get_post_type( $post_id ) !== Product::NAME ) {
			return false; // if it's not a product, it's not an unsupported product
		}

		// any product with modifiers is unsupported
		$encoded = get_post_meta( $post_id, Product::MODIFIER_DATA_META_KEY, true );
		if ( empty( $encoded ) ) {
			return false;
		}
		$decoded = json_decode( $encoded );
		if ( is_array( $decoded ) ) {
			$unsupported = array_filter( $decoded, function( $modifier ) {
				return $modifier->type == 'file';
			});
			return count( $unsupported ) > 0;
		}

		// all others are supported
		return false;
	}
}