<?php


namespace BigCommerce\Forms;

use BigCommerce\Api\v3\Api\CartApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Cart\Cart;
use BigCommerce\Pages;
use BigCommerce\Settings;

/**
 * Handles the submission and processing of gift certificate purchase forms.
 * 
 * @package BigCommerce\Forms
 */
class Purchase_Gift_Certificate_Handler implements Form_Handler {

	/**
	 * The action string used to identify the gift certificate purchase form.
	 *
	 * @var string
	 */
	const ACTION = 'gift-purchase';

	/**
	 * Minimum amount for a gift certificate.
	 *
	 * @var float
	 */
	private $minimum = 1.0;

	/**
	 * Maximum amount for a gift certificate.
	 *
	 * @var float
	 */
	private $maximum = 1000.0;

	/**
	 * The CartApi instance used to interact with the cart and perform cart-related operations.
	 *
	 * @var CartApi
	 */
	private $api;

	/**
	 * Purchase_Gift_Certificate_Handler constructor.
	 *
	 * @param CartApi $api The Cart API instance.
	 */
	public function __construct( CartApi $api ) {
		$this->api = $api;
	}

	/**
	 * Handle the gift certificate purchase request.
	 *
	 * @param array $submission The submitted form data.
	 * @return void
	 */
	public function handle_request( $submission ) {

		if ( ! $this->should_handle_request( $submission ) ) {
			return;
		}

		$errors = $this->validate_submission( $submission );

		if ( count( $errors->get_error_codes() ) > 0 ) {

			/**
			 * Triggered when a form has errors that prevent completion.
			 *
			 * @param \WP_Error $errors     The message that will display to the user
			 * @param array     $submission The data submitted to the form
			 */
			do_action( 'bigcommerce/form/error', $errors, $submission );

			return;
		}

		$gift_certificate = $this->get_certificate_data( $submission[ 'bc-gift-purchase' ] );
		$cart             = new Cart( $this->api );


		try {
			$response         = $cart->add_gift_certificate( $gift_certificate );
		} catch ( ApiException $e ) {
			$url = get_permalink( get_option( Pages\Gift_Certificate_Page::NAME ) );
			if ( strpos( (string) $e->getCode(), '4' ) === 0 ) {
				$body = $e->getResponseBody();
				if ( $body && ! empty( $body->title ) ) {
					$message = sprintf( '[%d] %s', $e->getCode(), $body->title );
				} else {
					$message = $e->getMessage();
				}
				$error = new \WP_Error( 'api_error', sprintf(
					__( 'There was an error adding the gift certificate to your cart. Error message: "%s"', 'bigcommerce' ),
					$message
				), [ 'exception' => [ 'message' => $e->getMessage(), 'code' => $e->getCode() ] ] );
			} else {
				$error = new \WP_Error( 'api_error', __( 'There was an error adding the gift certificate to your cart.', 'bigcommerce' ), [ 'exception' => $e ] );
			}
			do_action( 'bigcommerce/form/error', $error, $submission, $url );
			return;
		}


		/**
		 * The message to display when a gift certificate is added to the cart
		 *
		 * @param string $message
		 */
		$message = apply_filters( 'bigcommerce/form/gift_certificate/success_message', __( 'Gift Certificate Created!', 'bigcommerce' ) );

		if ( get_option( Settings\Sections\Cart::OPTION_ENABLE_CART, true ) ) {
			$url = $cart->get_cart_url();
		} else {
			// no cart page, so redirect to bigcommerce for checkout
			$url = $cart->get_checkout_url( $response->getId() );
			wp_redirect( $url, 303 );
			exit();
		}

		/**
		 * Triggered when a form is successfully processed.
		 *
		 * @param string $message    The message that will display to the user
		 * @param array  $submission The data submitted with the form
		 * @param string $url        The URL to redirect the user to
		 */
		do_action( 'bigcommerce/form/success', $message, $submission, $url );
	}

	private function should_handle_request( $submission ) {
		if ( empty( $submission[ 'bc-action' ] ) || $submission[ 'bc-action' ] !== self::ACTION ) {
			return false;
		}
		if ( empty( $submission[ '_wpnonce' ] ) || empty( $submission[ 'bc-gift-purchase' ] ) ) {
			return false;
		}

		return true;
	}

	private function validate_submission( $submission ) {
		if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION ) ) {
			return false;
		}

		$errors = new \WP_Error();

		if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION ) ) {
			$errors->add( 'invalid_nonce', __( 'There was an error processing your request. Please try again.', 'bigcommerce' ) );
		}

		if ( empty( $submission[ 'bc-gift-purchase' ][ 'sender-name' ] ) ) {
			$errors->add( 'sender-name', __( 'Your Name is required.', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-gift-purchase' ][ 'recipient-name' ] ) ) {
			$errors->add( 'recipient-name', __( 'Recipient Name is required.', 'bigcommerce' ) );
		}

		if ( empty( $submission[ 'bc-gift-purchase' ][ 'sender-email' ] ) ) {
			$errors->add( 'sender-email', __( 'Your Email is required.', 'bigcommerce' ) );
		} elseif ( ! is_email( $submission[ 'bc-gift-purchase' ][ 'sender-email' ] ) ) {
			$errors->add( 'sender-email', __( 'Please verify that your email address is valid', 'bigcommerce' ) );
		}
		if ( empty( $submission[ 'bc-gift-purchase' ][ 'recipient-email' ] ) ) {
			$errors->add( 'recipient-email', __( 'Recipient Email is required.', 'bigcommerce' ) );
		} elseif ( ! is_email( $submission[ 'bc-gift-purchase' ][ 'recipient-email' ] ) ) {
			$errors->add( 'recipient-email', __( "Please verify that the recipient's email address is valid", 'bigcommerce' ) );
		}

		if ( empty( $submission[ 'bc-gift-purchase' ][ 'amount' ] ) ) {
			$errors->add( 'amount', __( 'Please enter an amount for the gift certificate', 'bigcommerce' ) );
		} else {
			$amount = $this->sanitize_amount( $submission[ 'bc-gift-purchase' ][ 'amount' ] );
			if ( $amount < 1.0 || $amount > 1000.0 ) {
				$errors->add( 'amount', sprintf(
					__( 'Please enter an amount between %s and %s', 'bigcommerce' ),
					/**
					 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
					 */
					apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $this->minimum ), $this->minimum ),
					/**
					 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
					 */
					apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $this->maximum ), $this->maximum )
				) );
			}
		}

		if ( empty( $submission[ 'bc-gift-purchase' ][ 'terms' ] ) ) {
			$errors->add( 'terms', __( 'Agree to the terms to proceed', 'bigcommerce' ) );
		}

		/**
		 * Filters update gift certificates form errors.
		 *
		 * @param \WP_Error $errors     WP error.
		 * @param array     $submission Submitted data.
		 */
		$errors = apply_filters( 'bigcommerce/form/gift_certificate/errors', $errors, $submission );

		return $errors;
	}

	private function sanitize_amount( $amount ) {
		return filter_var( $amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}

	private function get_certificate_data( $submission ) {
		$amount = $this->sanitize_amount( $submission[ 'amount' ] );
		$name   = sprintf(
			__( '%s Gift Certificate', 'bigcommerce' ),
			/**
			 * This filter is documented in src/BigCommerce/Currency/With_Currency.php.
			 */
			apply_filters( 'bigcommerce/currency/format', sprintf( '¤%0.2f', $amount ), $amount )
		);
		/**
		 * Filters gift certificates theme.
		 *
		 * @param string $theme Theme.
		 */
		$theme  = apply_filters( 'bigcommerce/gift_certificates/theme', $submission[ 'theme' ] );
		$data   = [
			'name'      => $name,
			'theme'     => $theme,
			'amount'    => $amount,
			'quantity'  => 1,
			'sender'    => [
				'name'  => sanitize_text_field( $submission[ 'sender-name' ] ),
				'email' => sanitize_email( $submission[ 'sender-email' ] ),
			],
			'recipient' => [
				'name'  => sanitize_text_field( $submission[ 'recipient-name' ] ),
				'email' => sanitize_email( $submission[ 'recipient-email' ] ),
			],
			'message'   => sanitize_textarea_field( $submission[ 'message' ] ),
		];

		return $data;
	}
}
