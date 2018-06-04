<?php


namespace BigCommerce\Shortcodes;


interface Shortcode {

	/**
	 * Return the rendered markup for this shortcode.
	 *
	 * @param array $attr
	 * @param int   $instance
	 *
	 * @return string
	 */
	public function render( $attr, $instance );

}