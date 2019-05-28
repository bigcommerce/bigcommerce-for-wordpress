<?php


namespace BigCommerce\Taxonomies;


use BigCommerce\Customizer\Sections\Product_Archive;

abstract class Taxonomy_Config {

	/** @var  string The ID of the taxonomy */
	protected $taxonomy = '';

	/** @var array Post types that support this taxonomy */
	protected $post_types = [];

	/**
	 * @param string $taxonomy   The ID of the taxonomy
	 * @param array  $post_types An array of post types that will use this taxonomy
	 */
	public function __construct( $taxonomy, array $post_types ) {
		$this->taxonomy   = $taxonomy;
		$this->post_types = $post_types;
	}

	/**
	 * Hook into WordPress to register the taxonomy
	 */
	public function register() {
		register_taxonomy( $this->taxonomy(), $this->post_types(), $this->get_args() );
	}

	/**
	 * @return string The ID of the taxonomy
	 */
	public function taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * @return array The IDs of the post types associated with this taxonomy
	 */
	public function post_types() {
		return $this->post_types;
	}

	/**
	 * Arguments to pass when registering the taxonomy.
	 *
	 * Filterable with register_taxonomy_args
	 *
	 * @see register_taxonomy() for accepted args.
	 * @return array
	 */
	abstract public function get_args();


	/**
	 * Get labels for the taxonomy.
	 * Filterable with taxonomy_labels_{$taxonomy_name}
	 *
	 * @return array
	 */
	abstract public function get_labels();

	/**
	 * Get the products archive slug. We use get_option here
	 * rather than get_post_type() because we're looking for it
	 * to add to taxonomy slugs before the post type has been
	 * registered.
	 *
	 * @return string The slug for the products archive
	 */
	protected function get_products_slug() {
		$slug = _x( 'products', 'post type rewrite slug', 'bigcommerce' );
		$setting = get_option( Product_Archive::ARCHIVE_SLUG, $slug );
		if ( ! empty( $setting ) ) {
			$slug = $setting;
		}
		return sanitize_title( trim( $slug, '/') );
	}

	protected function get_caps() {
		$caps = [
			'manage_terms' => 'manage_categories',
			'edit_terms'   => 'do_not_allow', // prevent editing, because it will be overwritten on next import
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'do_not_allow', // prevent assignment, because it will be overwritten on next import
		];
		/**
		 * Filter the default capabilities for taxonomy terms.
		 *
		 * The dynamic portion of the hook is the name of the taxonomy.
		 *
		 * @param array $caps The capabilities array for the taxonomy
		 */
		$caps = apply_filters( 'bigcommerce/taxonomy/' . $this->taxonomy . '/capabilities', $caps );
		return $caps;
	}
}