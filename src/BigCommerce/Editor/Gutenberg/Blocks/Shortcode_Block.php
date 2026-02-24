<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;

/**
 * A Gutenberg Block that acts as a wrapper for a shortcode.
 */
abstract class Shortcode_Block extends Gutenberg_Block {
	/**
	 * URL for the assets.
	 *
	 * @var string
	 */
	protected $assets_url;

	/**
	 * The shortcode associated with the block.
	 *
	 * @var string
	 */
	protected $shortcode;

	/**
	 * The icon for the block.
	 *
	 * @var string
	 */
	protected $icon = '';

	/**
	 * The category under which the block is listed.
	 *
	 * @var string
	 */
	protected $category = 'widgets';

	/**
	 * Constructor for the block, setting the assets URL.
	 *
	 * @param string $assets_url The URL for assets.
	 */
	public function __construct( $assets_url ) {
		parent::__construct();
		$this->assets_url = $assets_url;
	}

	/**
	 * Generates the image URL for the given file.
	 *
	 * @param string $file The image file name.
	 *
	 * @return string The complete URL to the image.
	 */
	protected function image_url( $file ) {
		return trailingslashit( $this->assets_url ) . 'img/admin/' . $file;
	}


	/**
	 * Renders the block. The default behavior is to convert the block into a shortcode,
	 * which will then be rendered by do_shortcode.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string The rendered block content.
	 */
	public function render( $attributes ) {
		if ( empty( $attributes[ 'shortcode' ] ) ) {
			return sprintf( '[%s]', $this->shortcode );
		}
		return $attributes[ 'shortcode' ];
	}

	/**
	 * Returns the JavaScript configuration for the block.
	 *
	 * @return array The block's JS configuration.
	 */
	public function js_config() {
		return [
			'name'       => $this->name(),
			'title'      => $this->title(),
			'icon'       => $this->icon(),
			'category'   => $this->category(),
			'keywords'   => $this->keywords(),
			'shortcode'  => $this->shortcode(),
			'block_html' => [
				'title' => $this->html_title(),
				'image' => $this->html_image(),
			],
		];
	}

	/**
	 * Returns the title for the block.
	 *
	 * @return string The block's title.
	 */
	abstract protected function title();

	/**
	 * Returns the icon for the block.
	 *
	 * @return string The icon for the block.
	 */
	protected function icon() {
		return $this->icon;
	}

	/**
	 * Returns the category for the block.
	 *
	 * @return string The category for the block.
	 */
	protected function category() {
		return $this->category;
	}

	/**
	 * Returns an array of keywords for the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		return [
			__( 'ecommerce', 'bigcommerce' ),
		];
	}

	/**
	 * Returns the shortcode associated with the block.
	 *
	 * @return string The shortcode for the block.
	 */
	protected function shortcode() {
		return $this->shortcode;
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title for the block.
	 */
	abstract protected function html_title();

	/**
	 * Returns the HTML image for the block.
	 *
	 * @return string The HTML image for the block.
	 */
	abstract protected function html_image();
}