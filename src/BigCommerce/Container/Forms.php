<?php


namespace BigCommerce\Container;


use BigCommerce\Forms\Delete_Address_Handler;
use BigCommerce\Forms\Error_Handler;
use BigCommerce\Forms\Form_Redirect;
use BigCommerce\Forms\Messages;
use BigCommerce\Forms\Registration_Handler;
use BigCommerce\Forms\Success_Handler;
use BigCommerce\Forms\Update_Address_Handler;
use BigCommerce\Forms\Update_Profile_Handler;
use Pimple\Container;

class Forms extends Provider {
	const DELETE_ADDRESS = 'forms.delete_address';
	const REGISTER       = 'forms.register';
	const UPDATE_ADDRESS = 'forms.udpate_address';
	const UPDATE_PROFILE = 'forms.update_profile';
	const ERRORS         = 'forms.errors';
	const SUCCESS        = 'forms.success';
	const REDIRECTS      = 'forms.redirects';
	const MESSAGING      = 'forms.messaging';

	public function register( Container $container ) {

		$this->actions( $container );
		$this->errors( $container );
		$this->success( $container );
		$this->redirects( $container );
		$this->messaging( $container );

	}

	private function actions( Container $container ) {
		/**
		 * Handle all form submissions with a bc-action argument
		 */
		add_action( 'parse_request', $this->create_callback( 'handle_form_action', function () use ( $container ) {
			if ( isset( $_REQUEST[ 'bc-action' ] ) ) {
				do_action( 'bigcommerce/form/action=' . $_REQUEST[ 'bc-action' ], stripslashes_deep( $_REQUEST ) );
			}
		} ), 10, 0 );

		$container[ self::DELETE_ADDRESS ] = function ( Container $container ) {
			return new Delete_Address_Handler();
		};
		add_action( 'bigcommerce/form/action=' . Delete_Address_Handler::ACTION, $this->create_callback( 'delete_address', function ( $submission ) use ( $container ) {
			$container[ self::DELETE_ADDRESS ]->handle_request( $submission );
		} ), 10, 1 );

		$container[ self::UPDATE_ADDRESS ] = function ( Container $container ) {
			return new Update_Address_Handler();
		};
		add_action( 'bigcommerce/form/action=' . Update_Address_Handler::ACTION, $this->create_callback( 'update_address', function ( $submission ) use ( $container ) {
			$container[ self::UPDATE_ADDRESS ]->handle_request( $submission );
		} ), 10, 1 );

		$container[ self::UPDATE_PROFILE ] = function ( Container $container ) {
			return new Update_Profile_Handler();
		};
		add_action( 'bigcommerce/form/action=' . Update_Profile_Handler::ACTION, $this->create_callback( 'update_profile', function ( $submission ) use ( $container ) {
			$container[ self::UPDATE_PROFILE ]->handle_request( $submission );
		} ), 10, 1 );

		$container[ self::REGISTER ] = function ( Container $container ) {
			return new Registration_Handler();
		};
		add_action( 'bigcommerce/form/action=' . Registration_Handler::ACTION, $this->create_callback( 'register', function ( $submission ) use ( $container ) {
			return $container[ self::REGISTER ]->handle_request( $submission );
		} ), 10, 1 );
	}

	private function errors( Container $container ) {
		$container[ self::ERRORS ] = function ( Container $container ) {
			return new Error_Handler();
		};
		add_action( 'bigcommerce/form/error', $this->create_callback( 'error', function ( \WP_Error $error, $submission, $redirect = '' ) use ( $container ) {
			$container[ self::ERRORS ]->form_error( $error, $submission, $redirect );
		} ), 10, 3 );
	}

	private function success( Container $container ) {
		$container[ self::SUCCESS ] = function ( Container $container ) {
			return new Success_Handler();
		};

		add_action( 'bigcommerce/form/success', $this->create_callback( 'success', function ( $message = '', $url = null ) use ( $container ) {
			$container[ self::SUCCESS ]->form_success( $message, $url );
		} ), 10, 2 );
	}

	private function redirects( Container $container ) {
		$container[ self::REDIRECTS ] = function ( Container $container ) {
			return new Form_Redirect();
		};
		add_action( 'bigcommerce/form/redirect', $this->create_callback( 'redirect', function ( $url ) use ( $container ) {
			$container[ self::REDIRECTS ]->redirect( $url );
		} ), 10, 1 );
	}

	private function messaging( Container $container ) {
		$container[ self::MESSAGING ] = function ( Container $container ) {
			return new Messages();
		};

		add_filter( 'the_content', $this->create_callback( 'messages', function ( $content ) use ( $container ) {
			return $container[ self::MESSAGING ]->render_messages_above_content( $content );
		} ), 5, 1 );
	}
}