<?php


namespace BigCommerce\Templates;


abstract class Controller {

	protected $template = '';
	protected $options  = [];

	/**
	 * @param string $template
	 * @param array  $options
	 */
	public function __construct( array $options = [], $template = '' ) {
		$this->template = $template ?: $this->template;

		$options = $this->parse_options( $options );
		/**
		 * Filter the options passed in to a template controller
		 *
		 * @param array  $options  The options for the template
		 * @param string $template The template path
		 */
		$options = apply_filters( 'bigcommerce/template/options', $options, $this->template );
		/**
		 * Filter the options passed in to a template controller
		 *
		 * @param array  $options  The options for the template
		 * @param string $template The template path
		 */
		$options       = apply_filters( 'bigcommerce/template=' . $this->template . '/options', $options, $this->template );
		$this->options = $options;
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	abstract protected function parse_options( array $options );


	/**
	 * Render the template and return it as a string
	 *
	 * @return string The rendered template
	 */
	public function render() {
		$template = $this->get_template( $this->template );
		$data     = $this->get_data();
		/**
		 * Filter the data passed in to a template
		 *
		 * @param array  $data     The data for the template
		 * @param string $template The template path
		 * @param array  $options  The options from the template controller
		 */
		$data = apply_filters( 'bigcommerce/template/data', $data, $this->template, $this->options );
		/**
		 * Filter the data passed in to a template
		 *
		 * @param array  $data     The data for the template
		 * @param string $template The template path
		 * @param array  $options  The options from the template controller
		 */
		$data = apply_filters( 'bigcommerce/template=' . $this->template . '/data', $data, $this->template, $this->options );

		return $template->render( $data );
	}

	/**
	 * Build the data that will be available to the template
	 *
	 * @return array
	 */
	abstract public function get_data();

	/**
	 * @param string $relative_path
	 *
	 * @return Template
	 */
	private function get_template( $relative_path ) {
		$path = '';
		/**
		 * Filter the path to the directory within the theme to look in for template overrides
		 *
		 * @param string $directory The subdirectory of the theme dir to use
		 */
		$theme_dir = apply_filters( 'bigcommerce/template/directory/theme', '', $relative_path );
		/**
		 * Filter the path to the plugin directory to look in for templates
		 *
		 * @param string $directory The absolute path to the plugin directory
		 */
		$plugin_dir = apply_filters( 'bigcommerce/template/directory/plugin', '', $relative_path );
		if ( ! empty( $theme_dir ) ) {
			$path = locate_template( trailingslashit( $theme_dir ) . $relative_path );
		}

		if ( empty( $path ) && ! empty( $plugin_dir ) ) {
			$path = trailingslashit( $plugin_dir ) . $relative_path;
		}
		if ( empty( $path ) ) {
			throw new \RuntimeException( sprintf( __( 'Unable to locate template "%s"', 'bigcommerce' ), $relative_path ) );
		}

		/**
		 * Filter the path to a template
		 *
		 * @param string $path          The absolute path to the template
		 * @param string $relative_path The relative path of the requested template
		 */
		$path = apply_filters( 'bigcommerce/template/path', $path, $relative_path );
		/**
		 * Filter the path to a template
		 *
		 * @param string $path          The absolute path to the template
		 * @param string $relative_path The relative path of the requested template
		 */
		$path = apply_filters( 'bigcommerce/template=' . $this->template . '/path', $path, $relative_path );

		return new Template( $path );
	}

	protected function format_currency( $value, $return_empty_ammounts = true ) {
		if ( ! (float) $value && ! $return_empty_ammounts ) {
			return '';
		}

		/**
		 * Format a price for the current currency and locale
		 *
		 * @param string $formatted The formatted currency string
		 * @param float  $value     The price to format
		 */
		return apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $value ), $value );
	}
}