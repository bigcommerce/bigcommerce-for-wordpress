<?php


namespace BigCommerce\Container;


use BigCommerce\Templates\Body_Classes;
use BigCommerce\Templates\Template_Override;
use Pimple\Container;

/**
 * Handles template overrides and body class modifications
 * within the BigCommerce integration. It registers various filters to modify the template paths and
 * the body class dynamically based on the context, such as product pages, archives, taxonomies, and search results.
 */
class Templates extends Provider {
    /**
     * The container key for the template override service.
     *
     * This constant is used to define the key for the `Template_Override` service within the container.
     * It is utilized when accessing the template override functionality, such as rendering product templates.
	 * @var string
     */
    const OVERRIDE   = 'template.override';

    /**
     * The container key for the body class service.
     *
     * This constant is used to define the key for the `Body_Classes` service within the container.
     * It is used to modify the body class on the front end by adding relevant classes.
	 * @var string
     */
    const BODY_CLASS = 'template.body_class';

    /**
     * Register the template-related services and filters.
     *
     * This method registers all the necessary services and filters related to templates, including
     * template overrides, body classes, and template hierarchy modifications. It is called when the
     * container is initialized and hooks into various WordPress actions and filters for template management.
     *
     * @param Container $container The container instance used to register services.
     */
    public function register( Container $container ) {
		$container[ self::OVERRIDE ] = function ( Container $container ) {
			return new Template_Override();
		};

		$container[ self::BODY_CLASS ] = function ( Container $container ) {
			return new Body_Classes();
		};

		add_filter( 'bigcommerce/template/directory/plugin', $this->create_callback( 'plugin_directory', function ( $directory ) use ( $container ) {
			return $directory ?: plugin_dir_path( $container['plugin_file'] ) . 'templates/public';
		} ), 20, 1 );

		add_filter( 'bigcommerce/template/directory/theme', $this->create_callback( 'theme_directory', function ( $directory ) {
			return $directory ?: 'bigcommerce';
		} ), 20, 1 );

		add_filter( 'bigcommerce/template/product/single', $this->create_callback( 'product_single', function ( $output, $post_id ) use ( $container ) {
			if ( ! empty( $output ) ) {
				return $output;
			}

			return $container[ self::OVERRIDE ]->render_product_single( $post_id );
		} ), 10, 2 );

		add_filter( 'bigcommerce/template/product/archive', $this->create_callback( 'product_archive', function ( $output ) use ( $container ) {
			if ( ! empty( $output ) ) {
				return $output;
			}

			return $container[ self::OVERRIDE ]->render_product_archive();
		} ), 10, 2 );

		$single_template_hierarchy = $this->create_callback( 'single_template_hierarchy', function ( $templates ) use ( $container ) {
			return $container[ self::OVERRIDE ]->set_product_single_template_path( $templates );
		} );

		add_filter( 'single_template_hierarchy', $single_template_hierarchy, 10, 1 );
		add_filter( 'singular_template_hierarchy', $single_template_hierarchy, 10, 1 );
		add_filter( 'index_template_hierarchy', $single_template_hierarchy, 10, 1 );

		$archive_template_hierarchy = $this->create_callback( 'archive_template_hierarchy', function ( $templates ) use ( $container ) {
			return $container[ self::OVERRIDE ]->set_product_archive_template_path( $templates );
		} );

		add_filter( 'archive_template_hierarchy', $archive_template_hierarchy, 10, 1 );
		add_filter( 'index_template_hierarchy', $archive_template_hierarchy, 10, 1 );

		$tax_template_hierarchy = $this->create_callback( 'taxonomy_template_hierarchy', function ( $templates ) use ( $container ) {
			return $container[ self::OVERRIDE ]->set_taxonomy_archive_template_path( $templates );
		} );

		add_filter( 'taxonomy_template_hierarchy', $tax_template_hierarchy, 10, 1 );
		add_filter( 'archive_template_hierarchy', $tax_template_hierarchy, 10, 1 );
		add_filter( 'index_template_hierarchy', $tax_template_hierarchy, 10, 1 );

		$search_template_hierarchy = $this->create_callback( 'search_template_hierarchy', function ( $templates ) use ( $container ) {
			return $container[ self::OVERRIDE ]->set_search_template_path( $templates );
		} );

		add_filter( 'search_template_hierarchy', $search_template_hierarchy, 10, 1 );
		add_filter( 'index_template_hierarchy', $search_template_hierarchy, 10, 1 );

		add_filter( 'template_include', $this->create_callback( 'template_include', function ( $path ) use ( $container ) {
			return $container[ self::OVERRIDE ]->include_product_template( $path );
		} ), 10, 1 );

		add_filter( 'body_class', $this->create_callback( 'set_body_classes', function ( $classes ) use ( $container ) {
			return $container[ self::BODY_CLASS ]->set_body_classes( $classes );
		} ), 10, 1 );

	}
}
