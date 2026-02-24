<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;
use BigCommerce\Settings\Onboarding_Videos;
use BigCommerce\Settings\Sections\New_Account_Section;

class Create_Account_Screen extends Onboarding_Screen {
	const NAME = 'bigcommerce_new_account';

	const SUBMITTED_DATA = 'bigcommerce_new_account_submission';

	protected function get_page_title() {
		return __( 'Create Account', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Create Account', 'bigcommerce' );
	}

	protected function get_header() {
		return sprintf(
			'<div class="bc-new-account__copy">%s<header class="bc-new-account__header"><h1 class="bc-settings-connect__title">%s</h1></div></header><div class="bc-onboarding__video bc-new-account-video"><div class="bc-onboarding__video-embed">%s</div></div>',
			$this->before_title(),
			__( 'We just need a few details to create your store.', 'bigcommerce' ),
			$this->get_video()
		);
	}

	protected function parent_slug() {
		return null;
	}

	protected function submit_button() {
		$this->onboarding_submit_button( 'bc-settings-create-account-button', 'bc-onboarding-arrow', __( 'Create My Account', 'bigcommerce' ), true );
	}

	public function should_register() {
		return $this->configuration_status === Settings::STATUS_NEW;
	}

	/**
	 * Submit to admin-post.php, as this is not data saved as options
	 *
	 * @return string
	 */
	protected function form_action_url() {
		return admin_url( 'admin-post.php' );
	}

	/**
	 * Set action and nonce for processing with admin-post.php
	 *
	 * @return void
	 */
	protected function settings_fields() {
		printf( '<input type="hidden" name="action" value="%s" />', esc_attr( static::NAME ) );
		wp_nonce_field( self::NAME );
	}

	/**
	 * @return void
	 * @action admin_post_ . self::NAME
	 */
	public function handle_submission() {
		if ( ! check_admin_referer( self::NAME ) || empty( $_POST[ New_Account_Section::STORE_INFO ] ) ) {
			return;
		}

		$submission = $_POST;
		unset( $submission[ 'action' ], $submission[ '_wpnonce' ] );

		update_option( self::SUBMITTED_DATA, $submission, false );

		$errors = new \WP_Error();
		/**
		 * Validates the request for creating a new BigCommerce account.
		 *
		 * This action is triggered when a request is made to create a new account. It validates
		 * the submission data and checks for any errors before processing the request.
		 *
		 * @param array $submission The submitted data from the account creation form.
		 * @param array $errors     The array of validation errors, if any.
		 * @return void
		 */
		do_action( 'bigcommerce/create_account/validate_request', $submission, $errors );
		$this->handle_errors( $errors );

        /**
         * Action to handle the submission of account creation requests.
         *
         * This action processes data submitted for account creation, logs errors,
         * and invokes the Create_Account service to handle the request.
         *
         * @param array $data Submitted data for account creation.
         * @param array $errors An array of errors, if any, encountered during submission.
         * @return void
         * @throws \Exception If an error occurs during account creation.
         */
		do_action( 'bigcommerce/create_account/submit_request', $submission[ New_Account_Section::STORE_INFO ], $errors );
		$this->handle_errors( $errors );

		add_settings_error( self::NAME, 'submitted', __( 'Request Submitted', 'bigcommerce' ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect( esc_url_raw( add_query_arg( [ 'settings-updated' => 1 ], $this->get_url() ) ), 303 );
	}

	private function handle_errors( \WP_Error $errors ) {
		if ( count( $errors->get_error_codes() ) > 0 ) {
			foreach ( $errors->get_error_codes() as $code ) {
				foreach ( $errors->get_error_messages( $code ) as $message ) {
					add_settings_error( $code, $code, $message );
				}
			}
			set_transient( 'settings_errors', get_settings_errors(), 30 );
			wp_safe_redirect( esc_url_raw( add_query_arg( [ 'settings-updated' => 1 ], $this->get_url() ) ), 303 );
			exit();
		}
	}

	private function get_video() {
		return $this->make_video_embed( Onboarding_Videos::CREATE_ACCOUNT );
	}

}
