<?php


namespace BigCommerce\Templates;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * Class Template_Override
 *
 * Tell WordPress about the path to the templates
 */
class Template_Override {

	/**
	 * Render the contents of the product single template
	 *
	 * @param int $post_id
	 *
	 * @return string
	 * @filter bigcommerce/template/product/single
	 */
	public function render_product_single( $post_id ) {
		$product = new Product( get_the_ID() );
		$single  = Product_Single::factory( [
			Product_Single::PRODUCT => $product,
		] );
		$wrapper = Page_Wrapper::factory( [
			Page_Wrapper::CONTENT => $single->render(),
		] );

		return $wrapper->render();
	}

	/**
	 * Render the contents of the product archive template
	 *
	 * @return string
	 * @filter bigcommerce/template/product/archive
	 */
	public function render_product_archive() {
		$archive = Product_Archive::factory( [
			Product_Archive::QUERY => $GLOBALS[ 'wp_query' ],
		] );
		$wrapper = Page_Wrapper::factory( [
			Page_Wrapper::CONTENT => $archive->render(),
		] );

		return $wrapper->render();
	}

	/**
	 * @param string[] $templates
	 *
	 * @return string[]
	 * @filter single_template_hierarchy
	 * @filter singular_template_hierarchy
	 * @filter index_template_hierarchy
	 */
	public function set_product_single_template_path( $templates ) {
		if ( ! is_singular( Product::NAME ) ) {
			return $templates;
		}

		// strip out single.php to force post-type-specific template
		$templates = array_filter( $templates, function ( $path ) {
			return ! in_array( $path, [ 'single.php', 'singular.php', 'index.php' ] );
		} );

		$prefixed = $this->prefix_theme_paths( $templates );

		return array_merge( $prefixed, $templates );
	}

	/**
	 * @param string[] $templates
	 *
	 * @return string[]
	 * @filter archive_template_hierarchy
	 * @filter index_template_hierarchy
	 */
	public function set_product_archive_template_path( $templates ) {
		if ( ! is_post_type_archive( Product::NAME ) ) {
			return $templates;
		}

		// strip out archive.php to force post-type-specific template
		$templates = array_filter( $templates, function ( $path ) {
			return ! in_array( $path, [ 'archive.php', 'index.php' ] );
		} );

		$prefixed = $this->prefix_theme_paths( $templates );

		return array_merge( $prefixed, $templates );
	}

	/**
	 * @param string[] $templates
	 *
	 * @return string[]
	 * @filter search_template_hierarchy
	 * @filter index_template_hierarchy
	 */
	public function set_search_template_path( $templates ) {
		if ( ! is_search() ) {
			return $templates;
		}

		$post_type = get_query_var( 'post_type' );
		if ( $post_type !== Product::NAME ) {
			return $templates;
		}

		// strip out search.php and index.php to force post-type-specific template
		$templates = array_filter( $templates, function ( $path ) {
			return ! in_array( $path, [ 'search.php', 'index.php' ] );
		} );

		$prefixed = $this->prefix_theme_paths( $templates );

		return array_merge( $prefixed, $templates );
	}

	/**
	 * @param string[] $templates
	 *
	 * @return string[]
	 * @filter taxonomy_template_hierarchy
	 * @filter archive_template_hierarchy
	 * @filter index_template_hierarchy
	 */
	public function set_taxonomy_archive_template_path( $templates ) {
		if ( ! is_tax( [ Product_Category::NAME, Brand::NAME ] ) ) {
			return $templates;
		}

		// strip out generic templates to force post-type-specific template
		$templates = array_filter( $templates, function ( $path ) {
			return ! in_array( $path, [ 'taxonomy.php', 'archive.php', 'index.php' ] );
		} );

		$templates[] = sprintf( 'archive-%s.php', Product::NAME );

		$prefixed = $this->prefix_theme_paths( $templates );

		return array_merge( $prefixed, $templates );
	}

	/**
	 * Prefix all paths with the theme's plugin override dir
	 *
	 * @param string[] $paths
	 *
	 * @return string[]
	 */
	private function prefix_theme_paths( $paths ) {
		/**
		 * This filter is documented in src/BigCommerce/Templates/Controller.php
		 */
		$prefix = trailingslashit( apply_filters( 'bigcommerce/template/directory/theme', '' ) );

		return array_map( function ( $path ) use ( $prefix ) {
			return $prefix . ltrim( $path, '/' );
		}, $paths );
	}

	/**
	 * @param string|bool $template
	 *
	 * @return string|bool
	 *
	 * @filter template_include
	 */
	public function fallback_to_plugin_template( $template ) {
		if ( ! empty( $template ) ) {
			return $template;
		}
		if ( is_singular( Product::NAME ) ) {
			return $this->get_product_single_path();
		}
		if ( is_post_type_archive( Product::NAME ) ) {
			return $this->get_product_archive_path();
		}
		if ( is_tax( [ Product_Category::NAME, Brand::NAME ] ) ) {
			return $this->get_product_archive_path();
		}

		return $template;
	}

	/**
	 * @return bool|string Path to the product single template in the plugin
	 */
	private function get_product_single_path() {
		return $this->get_plugin_template_path( 'single-' . Product::NAME . '.php' );
	}

	/**
	 * @return bool|string Path to the product archive template in the plugin
	 */
	private function get_product_archive_path() {
		return $this->get_plugin_template_path( 'archive-' . Product::NAME . '.php' );
	}

	/**
	 * Get the relative path to a template file in the plugin dir
	 *
	 * @param string $relative_path
	 *
	 * @return string|bool
	 */
	private function get_plugin_template_path( $relative_path ) {
		/**
		 * This filter is documented in src/BigCommerce/Templates/Controller.php
		 */
		$plugin_dir = apply_filters( 'bigcommerce/template/directory/plugin', '', $relative_path );
		$path       = trailingslashit( $plugin_dir ) . $relative_path;

		$path = apply_filters( 'bigcommerce/template/path', $path, $relative_path );
		if ( ! file_exists( $path ) ) {
			return false;
		}

		return $path;
	}
}