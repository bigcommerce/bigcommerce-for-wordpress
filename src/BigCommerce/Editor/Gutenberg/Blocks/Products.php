<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Shortcodes;

/**
 * Class Products
 *
 * A block to add one or more products into the post content
 */
class Products extends Shortcode_Block {
	const NAME = 'bigcommerce/products';

	private $shortcode_rest_controller;

	protected $shortcode = Shortcodes\Products::NAME;

	protected $category = 'common';

	public function __construct( $assets_url, Shortcode_Controller $shortcode_controller ) {
		parent::__construct( $assets_url );
		$this->shortcode_rest_controller = $shortcode_controller;
	}

	protected function title() {
		return __( 'BigCommerce Products', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'BigCommerce Products', 'bigcommerce' );
	}

	protected function html_image() {
		return '';
	}

	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'store', 'bigcommerce' );
		$keywords[] = __( 'catalog', 'bigcommerce' );
		return $keywords;
	}

	public function js_config() {
		$config = parent::js_config();

		$config[ 'preview_url' ] = $this->get_preview_url();

		$config[ 'inspector' ] = [
			'title' => __( 'Add BigCommerce Products', 'bigcommerce' ),
			'button_title' => __( 'Edit Products', 'bigcommerce' ),
		];
		return $config;
	}

	protected function attributes() {
		return [
			'shortcode' => [
				'type' => 'string',
			],
			'queryParams' => [
				'type' => 'object',
			],
		];
	}

	protected function get_preview_url() {
		return $this->shortcode_rest_controller->get_base_url() . '/html';
	}
}