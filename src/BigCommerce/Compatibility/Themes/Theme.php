<?php


namespace BigCommerce\Compatibility\Themes;

/**
 * Base class for themes, providing functionality for rendering templates and checking version compatibility.
 * Specific theme implementations should extend this class and define templates and their supported versions.
 *
 * @package BigCommerce
 * @subpackage Compatibility\Themes
 */
abstract class Theme {

	protected $supported_version = '1.0.0';
	protected $templates = [];

	/**
	 * Render the specified theme template with options.
	 *
	 * @param string $template_name The name of the template to render.
	 * @param array  $options       The options to pass to the template controller.
	 *
	 * @return void
	 */
	public function render_template( $template_name, $options = [] ) {
		$controller = isset( $this->templates[ $template_name ] ) ? $this->templates[ $template_name ] : false;

		if ( $controller ) {
			echo $controller::factory( $options )->render();
		}
	}
	
	/**
	 * Load theme-specific compatibility functions.
	 *
	 * This method is intended to be overridden by child classes to load additional functions.
	 *
	 * @return void
	 */
	public function load_compat_functions() {
		return;
	}

	/**
	 * Check if the theme version is supported.
	 *
	 * @param string $version The version to check.
	 *
	 * @return bool True if the version is supported, false otherwise.
	 */
	public function is_version_supported( $version ) {
		if ( version_compare( $version, $this->supported_version, '>=' ) ) {
			return true;
		}
		return false;
	}

}