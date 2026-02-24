<?php


namespace BigCommerce\Settings\Screens;

abstract class Onboarding_Screen extends Abstract_Screen {

	protected function get_admin_body_class() {
		return parent::get_admin_body_class() . ' bigcommerce-onboarding-page';
	}

	public function render_settings_page() {
		return $this->onboarding_page_header() . parent::render_settings_page();
	}

	protected function progress_bar() {
		ob_start();
		/**
		 * Registers a callback to render the onboarding progress bar on the 'bigcommerce/settings/onboarding/progress' hook.
		 *
		 * @param  callable $progress_bar The callback function to render the progress bar.
		 * @param  int      $priority     The priority at which the callback should be executed. Defaults to 10.
		 * @param  int      $accepted_args The number of arguments the callback accepts. Defaults to 0.
		 */
		do_action( 'bigcommerce/settings/onboarding/progress' );
		return ob_get_clean();
	}

	protected function onboarding_page_header() {
		echo '<div class="bc-plugin-page-header">';
		printf( '<img class="bc-settings-save__logo" src="%s" alt="%s" />', trailingslashit( $this->assets_url ) . 'img/admin/big-commerce-logo.svg', esc_html( __( 'BigCommerce', 'bigcommerce' ) ) );
		echo '</div>';
	}

	protected function onboarding_submit_button( $data_attr_name, $classes, $button_label, $hide_label = false ) {
		$label_class = $hide_label ? ' screen-reader-text' : '';
		printf( '<button type="submit" class="button button-primary bc-admin-btn %s" aria-label="%s" data-js="%s"><i class="bc-icon icon-bc-arrow-right"></i> <span class="bc-submit-button-label%s">%s</span></button>', $classes, $button_label, $data_attr_name, $label_class, $button_label );
	}

	protected function make_video_embed( $url, $width = 1280, $height = 720 ) {
		return $GLOBALS['wp_embed']->shortcode( [ 'width' => $width, 'height' => $height ], $url );
	}
}
