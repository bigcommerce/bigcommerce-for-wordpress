<?php


namespace BigCommerce\Templates;


class Login_Form extends Controller {
	const FORM          = 'form';
	const REGISTER_LINK = 'register_link';
	const MESSAGE       = 'message';

	protected $template = 'components/accounts/login-form.php';

	protected function parse_options( array $options ) {
		return [];
	}

	public function get_data() {
		return [
			self::FORM          => $this->get_login_form(),
			self::REGISTER_LINK => $this->get_register_link(),
			self::MESSAGE       => $this->get_message(),
		];
	}

	protected function get_login_form() {
		if ( is_user_logged_in() ) {
			return '';
		}
		ob_start();
		wp_login_form( [
			'echo'           => true,
			'remember'       => true,
			'value_remember' => true,
			'redirect'       => isset( $_GET[ 'redirect_to' ] ) ? wp_sanitize_redirect( $_GET[ 'redirect_to' ] ) : home_url( '/' ),
			'label_username' => __( 'Email Address', 'bigcommerce' ),
			'label_log_in'   => __( 'Sign In', 'bigcommerce' ),
		] );
		do_action( 'login_form' );

		return ob_get_clean();
	}

	private function get_register_link() {
		if ( ! get_option( 'users_can_register' ) ) {
			return '';
		}

		return wp_registration_url();
	}

	private function get_message() {
		if ( empty( $_GET[ 'bc-message' ] ) ) {
			return '';
		}
		switch ( $_GET[ 'bc-message' ] ) {
			case 'loggedout':
				$message = Message::factory( [
					Message::CONTENT => __( 'You are now logged out.', 'bigcommerce' ),
					Message::TYPE    => Message::SUCCESS,
				] );

				return $message->render();
			case 'empty_username':
			case 'empty_password':
				$message = Message::factory( [
					Message::CONTENT => __( 'Please enter an email address and password.', 'bigcommerce' ),
					Message::TYPE    => Message::ERROR,
				] );

				return $message->render();
			case 'invalid_username':
			case 'invalid_email':
			case 'incorrect_password':
			case 'existing_user_email':
				$message = Message::factory( [
					Message::CONTENT => __( 'Please check that you have entered your email address and password correctly.', 'bigcommerce' ),
					Message::TYPE    => Message::ERROR,
				] );

				return $message->render();
			case 'checkemail':
				$message = Message::factory( [
					Message::CONTENT => __( 'Check your email for the confirmation link.', 'bigcommerce' ),
					Message::TYPE    => Message::NOTICE,
				] );

				return $message->render();
			default:
				return '';
		}
	}
}
