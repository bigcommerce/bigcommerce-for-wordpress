<?php


namespace BigCommerce\Settings\Sections;


abstract class Settings_Section {

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function render_field( $args ) {
		$option = $args[ 'option' ];
		$default = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
		$value  = get_option( $option, $default );
		printf( '<input type="%s" value="%s" class="regular-text code" name="%s" data-lpignore="true" />', esc_attr( $args[ 'type' ] ), esc_attr( $value ), esc_attr( $option ) );
		if ( ! empty( $args[ 'description' ] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args[ 'description' ] ) );
		}
	}
}