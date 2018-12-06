<?php


namespace BigCommerce\Customizer;


use BigCommerce\Customizer\Sections\Colors;

class Styles {

	private $template_file;

	private $black = Colors::COLOR_BC_BLACK;
	private $white = '#fff';

	/**
	 * Styles constructor.
	 *
	 * @param string $template_file Path to the CSS template file
	 */
	public function __construct( $template_file ) {
		$this->template_file = $template_file;
	}

	/**
	 * @return string
	 */
	public function get_styles() {
		if ( ! $this->using_plugin_css() ) {
			return '';
		}

		$vars = [
			'button-color'       => $this->button_color(),
			'button-text'        => $this->button_text(),
			'sale-color'         => $this->sale_color(),
			'sale-text'          => $this->sale_text(),
			'availability-color' => $this->availability_color(),
			'condition-color'    => $this->condition_color(),
			'condition-text'     => $this->condition_text(),
		];

		$template = file_get_contents( $this->template_file );
		foreach ( $vars as $key => $value ) {
			$template = str_replace( sprintf( 'var(--%s)', $key ), $value, $template );
		}
		$css = apply_filters( 'bigcommerce/css/customizer_styles', $template );

		return $css;
	}

	/**
	 * @return void
	 * @action wp_head
	 */
	public function print_styles() {
		$css = $this->get_styles();

		if ( ! empty( $css ) ) {
			echo "\n<style type='text/css'>\n", $css, "\n</style>\n"; // WPCS: XSS okay. CSS is clean.
		}
	}

	/**
	 * @return void
	 * @action wp_head
	 */
	public function print_css() {
		$css = $this->get_styles();

		if ( ! empty( $css ) ) {
			echo $css; // WPCS: XSS okay. CSS is clean.
		}
	}

	/**
	 * @return bool
	 */
	private function using_plugin_css() {
		return ( get_theme_mod( Colors::CSS, Colors::CSS_FULL ) !== Colors::CSS_OFF );
	}

	private function button_color() {
		$color = sanitize_hex_color( get_theme_mod( Colors::BUTTON_COLOR, Colors::COLOR_BC_BLUE ) );

		return $color ?: Colors::COLOR_BC_BLUE;
	}

	private function button_text() {
		$color = get_theme_mod( Colors::BUTTON_TEXT, Colors::TEXT_LIGHT );

		return ( $color == Colors::TEXT_DARK ) ? $this->black : $this->white;
	}

	private function sale_color() {
		$color = sanitize_hex_color( get_theme_mod( Colors::SALE_COLOR, Colors::COLOR_BC_GREEN ) );

		return $color ?: Colors::COLOR_BC_GREEN;
	}

	private function sale_text() {
		$color = get_theme_mod( Colors::SALE_TEXT, Colors::TEXT_LIGHT );

		return ( $color == Colors::TEXT_DARK ) ? $this->black : $this->white;
	}

	private function availability_color() {
		$color = sanitize_hex_color( get_theme_mod( Colors::AVAILABILITY_COLOR, Colors::COLOR_BC_BLACK ) );

		return $color ?: Colors::COLOR_BC_BLACK;
	}

	private function condition_color() {
		$color = sanitize_hex_color( get_theme_mod( Colors::CONDITION_COLOR, Colors::COLOR_BC_LIGHT_GREY ) );

		return $color ?: Colors::COLOR_BC_LIGHT_GREY;
	}

	private function condition_text() {
		$color = get_theme_mod( Colors::CONDITION_TEXT, Colors::TEXT_DARK );

		return ( $color == Colors::TEXT_DARK ) ? $this->black : $this->white;
	}

}
