<?php


namespace BigCommerce\Settings\Sections;


abstract class Settings_Section {

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function render_field( $args ) {
		$option       = $args['option'];
		$default      = isset( $args['default'] ) ? $args['default'] : '';
		$autocomplete = isset( $args['autocomplete'] ) ? $args['autocomplete'] : '';
		$value        = get_option( $option, $default );
		printf( '<input id="field-%s" type="%s" value="%s" class="regular-text code" name="%s" autocomplete="%s" data-lpignore="true" />', esc_attr( $args[ 'option' ] ), esc_attr( $args[ 'type' ] ), esc_attr( $value ), esc_attr( $option ), esc_attr( $autocomplete ) );
		if ( ! empty( $args[ 'description' ] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args[ 'description' ] ) );
		}
	}

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function render_number_field( $args ) {
		$option  = $args[ 'option' ];
		$default = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
		$value   = get_option( $option, $default );
		$min     = isset( $args[ 'min' ] ) ? sprintf( 'min="%d"', $args[ 'min' ] ) : '';
		$max     = isset( $args[ 'max' ] ) ? sprintf( 'max="%d"', $args[ 'max' ] ) : '';
		$step    = isset( $args[ 'step' ] ) ? sprintf( 'step="%s"', filter_var( $args[ 'step' ], FILTER_SANITIZE_NUMBER_FLOAT ) ) : '';
		printf( '<input id="field-%s" type="number" value="%s" class="code" name="%s" data-lpignore="true" %s %s %s />', esc_attr( $args[ 'option' ] ), esc_attr( $value ), esc_attr( $option ), $min, $max, $step );
		if ( ! empty( $args[ 'description' ] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args[ 'description' ] ) );
		}
	}
}