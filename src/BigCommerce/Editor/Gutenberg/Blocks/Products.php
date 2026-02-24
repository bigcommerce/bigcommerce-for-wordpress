<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Rest\Shortcode_Controller;
use BigCommerce\Shortcodes;

/**
 * A block to add one or more products into the post content.
 */
class Products extends Shortcode_Block {
	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce/products';

	/**
	 * The REST controller used for the shortcode.
	 *
	 * @var Shortcode_Controller
	 */
	private $shortcode_rest_controller;

	/**
	 * The shortcode used for this block.
	 *
	 * @var string
	 */
	protected $shortcode = Shortcodes\Products::NAME;

	/**
	 * The category for this block.
	 *
	 * @var string
	 */
	protected $category = 'common';

	/**
	 * Products constructor.
	 *
	 * @param string              $assets_url             The assets URL for the block.
	 * @param Shortcode_Controller $shortcode_controller  The controller for handling the shortcode.
	 */
	public function __construct( $assets_url, Shortcode_Controller $shortcode_controller ) {
		parent::__construct( $assets_url );
		$this->shortcode_rest_controller = $shortcode_controller;
	}

	/**
	 * Returns the block's title.
	 *
	 * @return string The block's title.
	 */
	protected function title() {
		return __( 'BigCommerce Products', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'BigCommerce Products', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the image associated with the block.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return '';
	}

	/**
	 * Returns an array of keywords related to the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		$keywords = parent::keywords();
		$keywords[] = __( 'store', 'bigcommerce' );
		$keywords[] = __( 'catalog', 'bigcommerce' );
		return $keywords;
	}

	/**
	 * Returns the JavaScript configuration for the block.
	 *
	 * @return array The JavaScript configuration.
	 */
	public function js_config() {
		$config = parent::js_config();

		$config[ 'preview_url' ] = $this->get_preview_url();

		$config[ 'inspector' ] = [
			'title' => __( 'Add BigCommerce Products', 'bigcommerce' ),
			'button_title' => __( 'Edit Products', 'bigcommerce' ),
		];
		return $config;
	}

	/**
	 * Returns the attributes for the block.
	 *
	 * @return array The block's attributes.
	 */
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

	/**
	 * Returns the preview URL for the block.
	 *
	 * @return string The preview URL.
	 */
	protected function get_preview_url() {
		return $this->shortcode_rest_controller->get_base_url() . '/html';
	}
}