<?php


namespace BigCommerce\Post_Types\Product;

use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Class Unique_Slug_Filter
 *
 * Responsible for filtering the unique slug proposed
 * for a product, enforcing uniqueness per channel
 * but not globally
 */
class Unique_Slug_Filter {

	/**
	 * @param string $slug          The post slug, as deduplicated by WP.
	 * @param int    $post_id       Post ID.
	 * @param string $post_status   The post status.
	 * @param string $post_type     Post type.
	 * @param int    $post_parent   Post parent ID
	 * @param string $original_slug The original post slug.
	 *
	 * @return string
	 * @filter wp_unique_post_slug
	 */
	public function get_unique_slug( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug ) {
		if ( $post_type !== Product::NAME ) {
			return $slug; // we're only concerned with products
		}

		if ( $slug === $original_slug ) {
			return $slug; // nothing changed, so WP didn't consider it a duplicate
		}

		try {
			$channel = $this->get_assigned_channel( $post_id );
		} catch ( Channel_Not_Found_Exception $e ) {
			return $slug; // no channel assigned to the post and no primary channel set
		}

		$slug      = $original_slug;
		$blacklist = $this->get_slug_blacklist();
		$suffix = 1;

		while ( in_array( $slug, $blacklist ) || ! $this->is_unique_in_channel( $slug, $post_id, $channel ) ) {
			$suffix++;
			$slug = _truncate_post_slug( $original_slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
		}

		return $slug;
	}

	/**
	 * Get the channel term assigned to the post
	 *
	 * @param int $post_id
	 *
	 * @return \WP_Term
	 */
	private function get_assigned_channel( $post_id ) {
		$terms = get_the_terms( $post_id, Channel::NAME );
		if ( empty( $terms ) ) {
			$connections = new Connections();
			return $connections->current();
		}

		return reset( $terms );
	}

	/**
	 * Identify if the given slug is unique in the context of the given channel
	 *
	 * @param string   $slug
	 * @param int      $post_id
	 * @param \WP_Term $channel
	 *
	 * @return bool
	 */
	private function is_unique_in_channel( $slug, $post_id, \WP_Term $channel ) {
		/** @var \wpdb $wpdb */
		global $wpdb;


		$check_sql = "SELECT p.post_name
		              FROM $wpdb->posts p
		              INNER JOIN {$wpdb->term_relationships} r
		                ON r.object_id=p.ID
		              WHERE p.post_name = %s
		                AND p.post_type = %s
		                AND p.ID != %d
		                AND r.term_taxonomy_id = %d
		              LIMIT 1";

		$matched_name = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, Product::NAME, $post_id, $channel->term_taxonomy_id ) );

		return empty( $matched_name );
	}

	private function get_slug_blacklist() {
		$blacklist = $GLOBALS['wp_rewrite']->feeds;
		if ( ! is_array( $blacklist ) ) {
			$blacklist = [];
		}
		$blacklist[] = 'embed';

		return $blacklist;
	}
}
