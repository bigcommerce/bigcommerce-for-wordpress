<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;

/**
 * Class Shortcode_Block
 *
 * A Gutenberg Block that acts as a wrapper for a shortcode
 */
abstract class Shortcode_Block extends Gutenberg_Block {
	protected $assets_url;
	protected $shortcode;
	protected $icon = '';
	protected $category = 'widgets';

	public function __construct( $assets_url ) {
		parent::__construct();
		$this->assets_url = $assets_url;
	}

	protected function image_url( $file ) {
		return trailingslashit( $this->assets_url ) . 'img/admin/' . $file;
	}


	/**
	 * Render the block. The default behavior is to convert the block
	 * into a shortcode, which will then be rendered by do_shortcode
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes ) {
		if ( empty( $attributes[ 'shortcode' ] ) ) {
			return sprintf( '[%s]', $this->shortcode );
		}
		return $attributes[ 'shortcode' ];
	}

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

	abstract protected function title();

	protected function icon() {
		return $this->icon;
	}

	protected function category() {
		return $this->category;
	}

	protected function keywords() {
		return [
			__( 'ecommerce', 'bigcommerce' ),
		];
	}

	protected function shortcode() {
		return $this->shortcode;
	}

	abstract protected function html_title();

	abstract protected function html_image();
}