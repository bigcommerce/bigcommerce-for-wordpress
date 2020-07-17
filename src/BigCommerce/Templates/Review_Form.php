<?php

namespace BigCommerce\Templates;

class Review_Form extends Controller {
	const PRODUCT  = 'product';
	const ERRORS   = 'errors';
	const DEFAULTS = 'defaults';
	const OPTIONS  = 'options';
	const MESSAGES = 'messages';

	protected $template = 'components/reviews/review-form.php';
	protected $wrapper_tag = 'div';
	protected $wrapper_classes = [ 'bc-product-review-form-wrapper' ];
	protected $wrapper_attributes = [
		'data-js' => 'bc-product-review-form-wrapper',
	];

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT => null,
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = [
			self::PRODUCT  => $this->options[ self::PRODUCT ],
			self::DEFAULTS => $this->get_form_defaults(),
			self::OPTIONS  => $this->get_rating_options(),
			self::MESSAGES => $this->get_messages(),
		];

		$error_data           = $this->get_error_data();
		$data[ self::ERRORS ] = $error_data ? $error_data['error']->get_error_codes() : [];
		if ( $error_data && array_key_exists( 'submission', $error_data ) ) {
			$data[ self::DEFAULTS ] = $this->restore_submission( $data[ self::DEFAULTS ], $error_data['submission'] );
		}

		return $data;
	}

	protected function get_form_defaults() {
		return [
			'rating'  => 0,
			'name'    => '',
			'email'   => '',
			'subject' => '',
			'content' => '',
		];
	}

	private function get_error_data() {
		if ( empty( $_REQUEST['bc-error'] ) ) {
			return false;
		}

		$bc_error = filter_var_array( $_REQUEST, [ 'bc-error' => FILTER_SANITIZE_STRING ] );
		$data     = get_transient( $bc_error[ 'bc-error' ] );
		if ( empty( $data['error'] ) || ! array_key_exists( 'user_id', $data ) ) {
			return false;
		}
		if ( $data['user_id'] != get_current_user_id() ) {
			return false;
		}
		if ( ! is_wp_error( $data['error'] ) || count( $data['error']->get_error_codes() ) < 1 ) {
			return false;
		}

		return $data;
	}

	/**
	 * If the user has submitted the form, restore their submission
	 * so they don't have to re-type everything
	 *
	 * @param array $data
	 * @param array $submission
	 *
	 * @return array
	 */
	private function restore_submission( $data, $submission ) {
		$submission = array_key_exists( 'bc-review', $submission ) ? $submission['bc-review'] : [];
		$submission = array_intersect_key( $submission, $data ); // only keep keys that we already know about

		return array_merge( $data, $submission );
	}

	private function get_rating_options() {
		return [
			1 => __( '1 star (worst)', 'bigcommerce' ),
			2 => __( '2 stars', 'bigcommerce' ),
			3 => __( '3 stars (average)', 'bigcommerce' ),
			4 => __( '4 stars', 'bigcommerce' ),
			5 => __( '5 stars (best)', 'bigcommerce' ),
		];
	}

	protected function get_messages() {
		return apply_filters( 'bigcommerce/forms/messages', '' );
	}

}