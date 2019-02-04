<?php


namespace BigCommerce\Meta_Boxes;


abstract class Post_Meta_Box extends Meta_Box {

	/**
	 * @param string   $post_type
	 * @param \WP_Post $post
	 *
	 * @return void
	 * @action add_meta_boxes
	 */
	public function register( $post_type = '', $post = null ) {
		if ( in_array( $post_type, $this->get_post_types() ) ) {
			parent::register();
		}
	}

	/**
	 * @param int      $post_id
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	abstract public function save_post( $post_id, $post );

	/**
	 * @return string[] The post types to which this metabox applies
	 */
	abstract protected function get_post_types();
}
