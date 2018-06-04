<?php


namespace BigCommerce\Templates;


class Template {
	private $path;

	/**
	 * Template constructor.
	 *
	 * @param string $path Absolute filesystem path to the template
	 */
	public function __construct( $path ) {
		$this->path = $path;
	}

	/**
	 * Render the template to a string
	 *
	 * @param array $context Variables that will be passed to the template
	 *
	 * @return string
	 */
	public function render( array $context ) {
		extract( $context );
		ob_start();
		include $this->path;
		return ob_get_clean();
	}
}