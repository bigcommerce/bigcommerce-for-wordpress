<?php


namespace BigCommerce\Pages;

use BigCommerce\Shortcodes;

class Registration_Page extends Required_Page {
	const NAME = 'bigcommerce_registration_page_id';

	protected function get_title() {
		return _x( 'Register', 'title of the registration page', 'bigcommerce' );
	}

	protected function get_slug() {
		return _x( 'register', 'slug of the registration page', 'bigcommerce' );
	}

	public function get_content() {
		return sprintf( '[%s]', Shortcodes\Registration_Form::NAME );
	}

	public function get_post_state_label() {
		return __( 'Registration Page', 'bigcommerce' );
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/accounts/after_page_field/page= . self::NAME
	 */
	public function enable_registration_notice() {
		if ( get_option( 'users_can_register' ) ) {
			return;
		}
		printf(
			'<p class="description">%s</p>',
			sprintf(
				esc_html( __( 'To enable this page feature, please go to %sGeneral Settings%s and check the box by "Anyone can register"', 'bigcommerce' ) ),
				sprintf( '<a href="%s">', esc_url( admin_url( 'options-general.php' ) ) ),
				'</a>'
				
			)
		);
	}

}