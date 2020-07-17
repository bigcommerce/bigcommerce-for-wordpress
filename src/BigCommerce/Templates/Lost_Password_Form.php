<?php


namespace BigCommerce\Templates;


class Lost_Password_Form extends Controller {
	const FORM_ACTION  = 'form_action';
	const LOGIN_URL    = 'login_url';
	const REGISTER_URL = 'register_url';
	const REDIRECT     = 'redirect_to';
	const MESSAGE      = 'message';

	protected $template = 'components/accounts/lostpassword-form.php';

	protected function parse_options( array $options ) {
		return [];
	}

	public function get_data() {
		return [
			self::FORM_ACTION  => network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ),
			self::LOGIN_URL    => $this->get_login_url(),
			self::REGISTER_URL => $this->get_register_url(),
			self::REDIRECT     => $this->get_redirect_url(),
			self::MESSAGE      => $this->get_message(),
		];
	}

	private function get_login_url() {
		return wp_login_url();
	}

	private function get_register_url() {
		if ( ! get_option( 'users_can_register' ) ) {
			return '';
		}

		return wp_registration_url();
	}

	private function get_redirect_url() {
		$redirect_to = filter_var_array( $_REQUEST, [ 'redirect_to' => FILTER_SANITIZE_URL ] );
		$url         = $redirect_to['redirect_to'] ?: wp_login_url();
		$url         = add_query_arg( [ 'bc-message' => 'checkemail' ], $url );

		return $url;
	}

	private function get_message() {
		if ( empty( $_GET[ 'bc-message' ] ) ) {
			return '';
		}
		switch ( $_GET[ 'bc-message' ] ) {
			case 'empty_username':
			case 'invalid_email':
				$message = Message::factory( [
					Message::CONTENT => __( 'Please enter a valid email address.', 'bigcommerce' ),
					Message::TYPE    => Message::ERROR,
				] );

				return $message->render();
			default:
				return '';
		}
	}
}
