<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Shortcodes;

/**
 * Class Products
 *
 * A block to add one or more products into the post content
 */
class Products extends Gutenberg_Block {
	const NAME = 'bigcommerce/products';

	private $shortcode_rest_controller;

	public function __construct( Shortcode_Controller $shortcode_controller ) {
		parent::__construct();
		$this->shortcode_rest_controller = $shortcode_controller;
	}

	public function render( $attributes ) {
		if ( empty( $attributes[ 'shortcode' ] ) ) {
			return sprintf( '[%s]', Shortcodes\Products::NAME );
		}
		return $attributes[ 'shortcode' ]; // content will be passed through do_shortcode
	}

	public function js_config() {
		return [
			'name'                   => $this->name(),
			'title'                  => __( 'BigCommerce Products', 'bigcommerce' ),
			'icon'                   => 'dashicons-bigcommerce', // TODO: path to SVG.
			'category'               => 'common',
			'keywords'               => [
				__( 'ecommerce', 'bigcommerce' ),
				__( 'commerce', 'bigcommerce' ),
				__( 'products', 'bigcommerce' ),
			],
			'preview_url'            => $this->get_preview_url(),
			'inspector_title'        => __( 'Add Big Commerce Products', 'bigcommerce' ),
			'inspector_button_title' => __( 'Edit Products', 'bigcommerce' ),
		];
	}

	protected function attributes() {
		return [
			'shortcode' => [
				'type' => 'string',
			],
			'attributes' => [
				'type' => 'object',
			],
		];
	}

	protected function get_preview_url() {
		return $this->shortcode_rest_controller->get_base_url() . '/html';
	}
}