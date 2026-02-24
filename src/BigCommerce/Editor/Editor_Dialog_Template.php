<?php

namespace BigCommerce\Editor;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Exceptions\Channel_Not_Found_Exception;
use BigCommerce\Import\Import_Type;
use BigCommerce\Rest\Products_Controller;
use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

/**
 * A class that handles rendering the editor dialog template with product filtering options.
 */
class Editor_Dialog_Template {

	/** @var string Path to the templates/public directory */
	private $template_dir;

	/** @var bool Flag to prevent rendering the dialog multiple times */
	private $rendered = false;

	/**
	 * Editor_Dialog_Template constructor.
	 *
	 * @param string $template_dir Path to the templates/public directory.
	 */
	public function __construct( $template_dir ) {
		$this->template_dir = trailingslashit( $template_dir );
	}

	/**
	 * Modify the configuration for the editor dialog.
	 *
	 * @param array                $config Configuration array.
	 * @param Products_Controller  $products_controller The products controller instance.
	 * @param Shortcode_Controller $shortcode_controller The shortcode controller instance.
	 *
	 * @return array Modified configuration.
	 * @filter bigcommerce/admin/js_config
	 */
	public function js_config( $config, Products_Controller $products_controller, Shortcode_Controller $shortcode_controller ) {
		$config[ 'editor_dialog' ] = [
			'product_api_url'   => $products_controller->get_base_url(),
			'shortcode_api_url' => $shortcode_controller->get_base_url(),
		];

		return $config;
	}

	/**
	 * Renders the editor dialog template only once to avoid duplication.
	 * 
	 * Gutenberg will load it earlier if enabled. We want to ensure it doesn't 
	 * get duplicated in the footer.
	 *
	 * @return string Rendered dialog HTML.
	 * @action admin_print_footer_scripts
	 * @action enqueue_block_editor_assets
	 */
	public function render_dialog_once() {
		if ( $this->rendered ) {
			return '';
		}
		$this->rendered = true;

		return $this->render_dialog();
	}

	/**
	 * Renders the full dialog content.
	 *
	 * @return string Rendered dialog HTML.
	 */
	public function render_dialog() {
		return $this->render_template( 'admin-dialog.php', [
			'query_builder_sidebar'  => $this->query_builder(),
			'query_settings_sidebar' => $this->query_settings(),
		] );
	}

	private function query_builder() {

		$featured = get_term_by( 'slug', Flag::FEATURED, Flag::NAME );
		$sale     = get_term_by( 'slug', Flag::SALE, Flag::NAME );
		$brands   = get_terms( [
			'taxonomy' => Brand::NAME,
			'orderby'  => 'title',
			'order'    => 'ASC',
		] );

		$brands_by_parent = [];

		foreach ( $brands as $brand ) {
			$brands_by_parent[ $brand->parent ][] = $brand;
		}

		//  Nest sub-category under the correct parent
		$brand_choices = $this->get_choices( 0, $brands_by_parent, 0 );

		$categories = get_terms( [
			'taxonomy'   => Product_Category::NAME,
			'orderby'    => 'title',
			'order'      => 'ASC',
			'hide_empty' => false,
		] );

		$categories_by_parent = [];

		foreach ( $categories as $category ) {
			$categories_by_parent[ $category->parent ][] = $category;
		}

		//  Nest sub-category under the correct parent
		$category_choices = $this->get_choices( 0, $categories_by_parent, 0 );

		$channels = $this->get_channels();

		return $this->render_template( 'query-builder.php', [
			'featured'   => $featured,
			'sale'       => Import_Type::is_traditional_import() && $sale,
			'brands'     => $brand_choices,
			'categories' => $category_choices,
			'channels'   => $channels,
		] );
	}

	/**
	 * Reorder the terms so sub-categories get nested bellow its parent
	 *
	 * @param $parent_id
	 * @param $terms_by_parent
	 * @param $depth
	 *
	 * @return array
	 */
	private function get_choices( $parent_id, $terms_by_parent, $depth ) {
		if ( empty( $terms_by_parent[ $parent_id ] ) ) {
			return [];
		}

		$choices = [];
		foreach ( $terms_by_parent[ $parent_id ] as $term ) {
			$choices[ $term->term_id ][ 'name' ]  = $term->name;
			$choices[ $term->term_id ][ 'slug' ]  = $term->slug;
			$choices[ $term->term_id ][ 'id' ]    = $term->term_id;
			$choices[ $term->term_id ][ 'depth' ] = $depth;
			$choices = $choices + $this->get_choices( $term->term_id, $terms_by_parent, $depth + 1 );
		}

		return $choices;
	}

	private function get_channels() {
		try {
			$connections = new Connections();
			$primary     = $connections->primary();
		} catch( Channel_Not_Found_Exception $e ) {
			return [];
		}

		return array_map( function ( \WP_Term $term ) use ( $primary ) {
			return [
				'name'    => $term->name,
				'slug'    => $term->slug,
				'id'      => $term->term_id,
				'primary' => $primary->term_id === $term->term_id,
			];
		}, $connections->active() );
	}

	private function query_settings() {
		return $this->render_template( 'query-settings.php', [
			'posts_per_page' => absint( get_option( Product_Archive::PER_PAGE, Product_Archive::PER_PAGE_DEFAULT ) ),
		] );
	}

	private function render_template( $filename, $vars ) {
		$path = $this->template_dir . $filename;
		extract( $vars );

		ob_start();
		require( $path );

		return ob_get_clean();
	}
}
