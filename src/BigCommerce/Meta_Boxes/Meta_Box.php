<?php


namespace BigCommerce\Meta_Boxes;


abstract class Meta_Box {

	/**
	 * @param string   $post_type
	 * @param \WP_Post $post
	 *
	 * @return void
	 * @action add_meta_boxes
	 */
	public function register( $post_type, $post ) {
		if ( in_array( $post_type, $this->get_post_types() ) ) {
			add_meta_box(
				$this->get_name(),
				$this->get_title(),
				[ $this, 'render' ],
				null,
				$this->get_context(),
				$this->get_priority(),
				$this->get_callback_args()
			);
		}
	}

	/**
	 * @return string The unique identifier for the metabox
	 */
	abstract protected function get_name();

	/**
	 * @return string The title of the metabox
	 */
	abstract protected function get_title();

	/**
	 * Render the metabox contents
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	abstract public function render( $post );

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

	/**
	 * @return string One of 'normal', 'side', or 'advanced'
	 */
	protected function get_context() {
		return 'advanced';
	}

	/**
	 * @return string One of 'high', 'core', 'default', 'low'
	 */
	protected function get_priority() {
		return 'default';
	}

	/**
	 * @return null|array Additional args to pass to the render callback
	 */
	protected function get_callback_args() {
		return null;
	}

}
