<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Pages\Required_Page;

trait WithPages {
	public function render_page_field( $args ) {
		/** @var Required_Page $page */
		$page   = $args[ 'page' ];
		$option = $page->get_option_name();
		$value  = (int) get_option( $option, 0 );

		do_action( 'bigcommerce/settings/accounts/before_page_field', $page, $value );
		do_action( 'bigcommerce/settings/accounts/before_page_field/page=' . $option, $page, $value );

		$candidates = $page->get_post_candidates();
		$options    = array_map( function ( $post_id ) use ( $value ) {
			return sprintf( '<option value="%d" %s>%s</option>', $post_id, selected( $post_id, $value, false ), esc_html( get_the_title( $post_id ) ) );
		}, $candidates );
		if ( empty( $options ) ) {
			$options     = [
				sprintf( '<option value="0">&mdash; %s &mdash;</option>', __( 'No pages available', 'bigcommerce' ) ),
			];
			$description = sprintf( __( 'Create a page with the %s shortcode, then select it here.', 'bigcommerce' ), $page->get_content() );
		} else {
			array_unshift( $options, sprintf( '<option value="0">&mdash; %s &mdash;</option>', sprintf( __( 'Select %s', 'bigcommerce' ), $page->get_post_state_label() ) ) );
		}

		printf( '<select id="field-%s" name="%s" class="regular-text bc-field-choices">%s</select>', esc_attr( $option ), esc_attr( $option ), implode( "\n", $options ) );
		if ( ! empty( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}

		do_action( 'bigcommerce/settings/accounts/after_page_field', $page, $value );
		do_action( 'bigcommerce/settings/accounts/after_page_field/page=' . $option, $page, $value );
	}
}
