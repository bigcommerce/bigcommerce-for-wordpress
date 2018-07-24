<?php


namespace BigCommerce\Templates;


abstract class Form_Controller extends Controller {
	const DEFAULTS = 'defaults';
	const ERRORS   = 'errors';

	protected $submission_key = '';

	public function get_data() {
		$data = [
			self::DEFAULTS => $this->get_form_defaults(),
		];

		$error_data = apply_filters( 'bigcommerce/form/state/errors', null );

		$data[ self::ERRORS ] = $error_data ? $error_data[ 'error' ]->get_error_codes() : [];

		if ( $error_data && array_key_exists( 'submission', $error_data ) ) {
			$data[ self::DEFAULTS ] = $this->restore_submission( $this->submission_key, $data[ self::DEFAULTS ], $error_data[ 'submission' ] );
		}

		return $data;
	}

	protected function get_form_defaults() {
		return [];
	}

	/**
	 * If the user has submitted the form, restore their submission
	 * so they don't have to re-type everything
	 *
	 * @param string $field      The name of the field containing the values to restore
	 * @param array  $data       The default data to render in the form
	 * @param array  $submission The data the user submitted
	 *
	 * @return array
	 */
	protected function restore_submission( $field, $data, $submission ) {
		$submission = array_key_exists( $field, $submission ) ? (array) $submission[ $field ] : [];
		$submission = array_intersect_key( $submission, $data ); // only keep keys that we already know about

		return array_merge( $data, $submission );
	}
}