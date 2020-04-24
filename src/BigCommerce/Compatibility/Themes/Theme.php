<?php


namespace BigCommerce\Compatibility\Themes;


abstract class Theme {

	protected $supported_version = '1.0.0';
	protected $templates = [];

	/**
	 * Render theme template
	 *
	 * @param string $template_name
	 * @param array $options
	 * @return void
	 */
	public function render_template( $template_name, $options = [] ) {
		$controller = isset( $this->templates[ $template_name ] ) ? $this->templates[ $template_name ] : false;

		if ( $controller ) {
			echo $controller::factory( $options )->render();
		}
	}
	
	public function load_compat_functions() {
		return;
	}

	public function is_version_supported( $version ) {
		if ( version_compare( $version, $this->supported_version, '>=' ) ) {
			return true;
		}
		return false;
	}

}