<?php


namespace BigCommerce\Container;


use BigCommerce\Templates\Body_Classes;
use BigCommerce\Templates\Template_Override;
use Pimple\Container;

class Templates extends Provider {
	const OVERRIDE   = 'template.override';
	const BODY_CLASS = 'template.body_class';

	public function register( Container $container ) {
		$container[ self::OVERRIDE ] = function ( Container $container ) {
			return new Template_Override();
		};

		$container[ self::BODY_CLASS ] = function ( Container $container ) {
			return new Body_Classes();
		};


		/**
		 * Look for plugin templates in [plugin]/templates/public
		 */
		add_filter( 'bigcommerce/template/directory/plugin', $this->create_callback( 'plugin_directory', function ( $directory ) use ( $container ) {
			return $directory ?: plugin_dir_path( $container['plugin_file'] ) . 'templates/public';
		} ), 20, 1 );

		/**
		 * Look for template overrides in [theme]/bigcommerce
		 */
		add_filter( 'bigcommerce/template/directory/theme', $this->create_callback( 'theme_directory', function ( $directory ) {
			return $directory ?: 'bigcommerce';
		} ), 20, 1 );

		/**
		 * Render the product single
		 */
		add_filter( 'bigcommerce/template/product/single', $this->create_callback( 'product_single', function ( $output, $post_id ) use ( $container ) {
			if ( ! empty( $output ) ) {
				return $output;
			}

			return $container[ self::OVERRIDE ]->render_product_single( $post_id );
		} ), 10, 2 );

		/**
		 * Render the product archive
		 */
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
			return $container[ self::OVERRIDE ]->fallback_to_plugin_template( $path );
		} ), 10, 1 );

		add_filter( 'body_class', $this->create_callback( 'set_body_classes', function ( $classes ) use ( $container ) {
			return $container[ self::BODY_CLASS ]->set_body_classes( $classes );
		} ), 10, 1 );

	}
}
