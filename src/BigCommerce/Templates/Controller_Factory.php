<?php


namespace BigCommerce\Templates;


class Controller_Factory {
	/**
	 * Creates an instance of the requested controller
	 *
	 * @param string $classname
	 * @param array  $options
	 * @param string $template
	 *
	 * @return Controller
	 */
	public function get_controller( $classname, array $options = [], $template = '' ) {
		return new $classname( $options, $template );
	}
}