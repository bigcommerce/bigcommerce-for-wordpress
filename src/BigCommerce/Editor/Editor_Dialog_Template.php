<?php

namespace BigCommerce\Editor;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Rest\Products_Controller;
use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class Editor_Dialog_Template {

	/** @var string Path to the public-views directory */
	private $template_dir;

	private $rendered = false;

	public function __construct( $template_dir ) {
		$this->template_dir = trailingslashit( $template_dir );
	}

	/**
	 * @param array                $config
	 * @param Products_Controller  $products_controller
	 * @param Shortcode_Controller $shortcode_controller
	 *
	 * @return array
	 * @filter bigcommerce/admin/js_config
	 */
	public function js_config( $config, Products_Controller $products_controller, Shortcode_Controller $shortcode_controller ) {
		$config['editor_dialog'] = [
			'product_api_url'   => $products_controller->get_base_url(),
			'shortcode_api_url' => $shortcode_controller->get_base_url(),
		];

		return $config;
	}

	/**
	 * Wrap the dialog template rendering so that it only occurs once.
	 * Gutenberg will load it earlier if it's enabled. We don't want
	 * a duplicated in the footer.
	 *
	 * @return string
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

	public function render_dialog() {
		return $this->render_template( 'admin-dialog.php', [
			'query_builder_sidebar' => $this->query_builder(),
			'query_settings_sidebar'      => $this->query_settings(),
		] );
	}

	private function query_builder() {

		$featured   = get_term_by( 'slug', Flag::FEATURED, Flag::NAME );
		$sale       = get_term_by( 'slug', Flag::SALE, Flag::NAME );
		$brands     = get_terms( [
			'taxonomy' => Brand::NAME,
			'orderby'  => 'title',
			'order'    => 'ASC',

		] );
		$categories = get_terms( [
			'taxonomy' => Product_Category::NAME,
			'orderby'  => 'title',
			'order'    => 'ASC',
			'parent'   => 0,
		] );

		return $this->render_template( 'query-builder.php', [
			'featured'   => $featured,
			'sale'       => $sale,
			'brands'     => $brands,
			'categories' => $categories,
		] );
	}

	private function query_settings() {
		return $this->render_template( 'query-settings.php', [
			'posts_per_page' => absint( get_option( Product_Archive::PER_PAGE, Product_Archive::PER_PAGE_DEFAULT ) ),
		]);
	}

	private function render_template( $filename, $vars ) {
		$path = $this->template_dir . $filename;
		extract( $vars );

		ob_start();
		require( $path );

		return ob_get_clean();
	}
}
