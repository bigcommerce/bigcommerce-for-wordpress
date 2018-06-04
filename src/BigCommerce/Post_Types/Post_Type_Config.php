<?php


namespace BigCommerce\Post_Types;


abstract class Post_Type_Config {

	/** @var string */
	protected $post_type = '';

	/**
	 * @param string $post_type The post type name
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Hook into WordPress to register the post type
	 *
	 * @action init
	 */
	public function register() {
		register_post_type( $this->post_type(), $this->get_args() );
	}

	/**
	 * @return string The ID of the post type
	 */
	public function post_type() {
		return $this->post_type;
	}


	/**
	 * Arguments to pass when registering the post type.
	 *
	 * Filterable with register_post_type_args
	 *
	 * @see register_post_type() for accepted args.
	 * @return array
	 */
	abstract public function get_args();

	/**
	 * Get labels for the post type.
	 * Filterable with post_type_labels_{$post_type_name}
	 *
	 * @return array
	 */
	abstract public function get_labels();
}