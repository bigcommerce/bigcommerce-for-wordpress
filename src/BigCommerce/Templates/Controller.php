<?php


namespace BigCommerce\Templates;


use BigCommerce\Currency\With_Currency;

abstract class Controller {
	use With_Currency;

	protected $template = '';
	protected $options  = [];

	/**
	 * @var string The tag for a wrapper element around this template
	 */
	protected $wrapper_tag = '';

	/**
	 * @var string[] The classes for the wrapper element around this template
	 */
	protected $wrapper_classes = [];

	/**
	 * @var string[] The data-js attribute for the wrapper tag
	 */
	protected $wrapper_attributes = [];

	/**
	 * Creates an instance of the controller
	 *
	 * @param array  $options
	 * @param string $template
	 *
	 * @return static
	 */
	public static function factory( array $options = [], $template = '' ) {
		/**
		 * Filter the factory class that instantiates template controllers
		 *
		 * @param Controller_Factory $factory   The instance of the factory class
		 * @param string             $classname The name of the requested class
		 */
		$factory = apply_filters( 'bigcommerce/template/controller_factory', new Controller_Factory(), static::class );
		if ( ! $factory instanceof Controller_Factory ) {
			throw new \RuntimeException( sprintf( __( 'Template controller factory must extend %s', 'bigcommerce' ), Controller_Factory::class ) );
		}

		return $factory->get_controller( static::class, $options, $template );
	}

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

		/**
		 * Filter the rendered output of the template
		 *
		 * @param string $output   The rendered template
		 * @param string $template The template path
		 * @param array  $data     The data passed to the template
		 * @param array  $options  The options from the template controller
		 */
		$output = apply_filters( 'bigcommerce/template=' . $this->template . '/output', $template->render( $data ), $this->template, $data, $this->options );

		return $this->wrap( $output );
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

	/**
	 * Wrap the template output in an optional tag. This provides us a mechanism
	 * to ensure that some elements and classes are consistently available
	 * for JavaScript targeting, despite possible template overrides.
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	protected function wrap( $html ) {
		/**
		 * Filter the HTML tag of the wrapper for a template
		 *
		 * @param string $tag      The tag name. Should be a valid HTML element, or an empty string
		 * @param string $template The template path
		 */
		$tag = sanitize_html_class( apply_filters( 'bigcommerce/template/wrapper/tag', $this->get_wrapper_tag(), $this->template ) );
		if ( empty( $tag ) ) {
			return $html;
		}

		/**
		 * Filter the HTML tag of the wrapper for a template
		 *
		 * @param string[] $classes  An array of class names
		 * @param string   $template The template path
		 */
		$classes = apply_filters( 'bigcommerce/template/wrapper/classes', $this->get_wrapper_classes(), $this->template );
		$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

		$attributes = apply_filters( 'bigcommerce/template/wrapper/attributes', $this->get_wrapper_attributes(), $this->template );

		$attrs = array_map( function ( $key ) use ( $attributes ) {
			if ( is_bool( $attributes[ $key ] ) ) {
				return $attributes[ $key ] ? sanitize_title_with_dashes( $key ) : '';
			}

			return sanitize_title_with_dashes( $key ) . '="' . esc_attr( $attributes[ $key ] ) . '"';
		}, array_keys( $attributes ) );

		return sprintf( '<%s class="%s" %s>%s</%s>', $tag, implode( ' ', $classes ), implode( ' ', $attrs ), $html, $tag );
	}

	protected function get_wrapper_tag() {
		return $this->wrapper_tag;
	}

	protected function get_wrapper_classes() {
		return $this->wrapper_classes;
	}

	protected function get_wrapper_attributes() {
		return $this->wrapper_attributes;
	}

	/**
	 * Build a string of HTML attributes that can safely be
	 * injected into a template out of a list of key/value pairs
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	protected function build_attribute_string( $attributes ) {
		$rendered = [];
		foreach ( $attributes as $attr => $value ) {
			$attr       = sanitize_title_with_dashes( $attr );
			$value      = esc_attr( $value );
			$rendered[] = sprintf( '%s="%s"', $attr, $value );
		}

		return implode( ' ', $rendered );
	}
}