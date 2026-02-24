<?php


namespace BigCommerce\Forms;


use BigCommerce\Accounts\Customer;

/**
 * Handles the updating and creation of customer addresses.
 *
 * This class is responsible for validating and processing form submissions for updating or creating
 * customer addresses. It handles the submission of address forms, validates the data, updates the
 * customer's address in BigCommerce, and provides appropriate success or error messages.
 *
 * @package BigCommerce\Forms
 */
class Update_Address_Handler implements Form_Handler {

    /**
     * The action name for the form submission.
     *
     * @var string
     */
    const ACTION = 'edit-address';

    /**
     * Handles the form submission for updating or creating an address.
     *
     * @param array $submission The form submission data.
     * 
     * @return void
     */
    public function handle_request( $submission ) {
        if ( ! $this->should_handle_request( $submission ) ) {
            return;
        }

        $user   = wp_get_current_user();
        $errors = $this->validate_submission( $submission, $user );

        if ( count( $errors->get_error_codes() ) > 0 ) {
            do_action( 'bigcommerce/form/error', $errors, $submission );

            return;
        }

        $customer   = new Customer( get_current_user_id() );
        $address_id = $submission[ 'bc-address' ][ 'id' ];
        $address    = $this->get_address( $submission[ 'bc-address' ] );

        if ( empty( $address_id ) ) {
            $success = $customer->add_address( $address );
        } else {
            $success = $customer->update_address( $address_id, $address );
        }

        if ( ! $success ) {
            $errors->add( 'api_error', __( 'There was an error saving your request. Please try again.', 'bigcommerce' ) );
            do_action( 'bigcommerce/form/error', $errors, $submission );

            return;
        }

        if ( empty( $address_id ) ) {
            /**
             * Filters address form created message.
             *
             * @param string $message Message.
             */
            $message = apply_filters( 'bigcommerce/form/address/created_message', __( 'Address created.', 'bigcommerce' ) );
            do_action( 'bigcommerce/form/success', $message, $submission, null, [ 'key' => 'address_created' ] );
        } else {
            /**
             * Filters address form updated message.
             *
             * @param string $message Message.
             */
            $message = apply_filters( 'bigcommerce/form/address/updated_message', __( 'Address saved.', 'bigcommerce' ) );
            do_action( 'bigcommerce/form/success', $message, $submission, null, [ 'key' => 'address_saved' ] );
        }
    }

    private function should_handle_request( $submission ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }
        if ( empty( $submission[ 'bc-action' ] ) || $submission[ 'bc-action' ] !== self::ACTION ) {
            return false;
        }
        if ( empty( $submission[ '_wpnonce' ] ) || ! isset( $submission[ 'bc-address' ][ 'id' ] ) ) {
            return false;
        }

        return true;
    }

    /**
     * Validates the form submission for address creation or update.
     *
     * @param array    $submission The form submission data.
     * @param \WP_User $user The current WordPress user.
     * 
     * @return \WP_Error A WP_Error object containing any validation errors.
     */
    private function validate_submission( $submission, \WP_User $user ) {
        $errors = new \WP_Error();

        if ( ! wp_verify_nonce( $submission[ '_wpnonce' ], self::ACTION . $submission[ 'bc-address' ][ 'id' ] ) ) {
            $errors->add( 'invalid_nonce', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
        }

        if ( empty( $submission[ 'bc-address' ][ 'first_name' ] ) ) {
            $errors->add( 'first_name', __( 'First Name is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'last_name' ] ) ) {
            $errors->add( 'last_name', __( 'Last Name is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'phone' ] ) ) {
            $errors->add( 'phone', __( 'Phone number is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'street_1' ] ) ) {
            $errors->add( 'street_1', __( 'Address Line 1 is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'city' ] ) ) {
            $errors->add( 'city', __( 'City is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'state' ] ) ) {
            $errors->add( 'state', __( 'State/Province is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'zip' ] ) ) {
            $errors->add( 'zip', __( 'Zip/Postcode is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission[ 'bc-address' ][ 'country' ] ) ) {
            $errors->add( 'country', __( 'Country is required.', 'bigcommerce' ) );
        }

        /**
         * Filters update profile address form errors.
         *
         * @param \WP_Error $errors     WP error.
         * @param array     $submission Submitted data.
         */
        $errors = apply_filters( 'bigcommerce/form/update_address/errors', $errors, $submission );

        return $errors;
    }

    private function get_address( $submitted_address ) {
        $defaults          = [
            'first_name' => '',
            'last_name'  => '',
            'company'    => '',
            'street_1'   => '',
            'street_2'   => '',
            'city'       => '',
            'state'      => '',
            'zip'        => '',
            'country'    => '',
            'phone'      => '',
        ];
        $submitted_address = array_filter( $submitted_address, function ( $key ) use ( $defaults ) {
            return array_key_exists( $key, $defaults );
        }, ARRAY_FILTER_USE_KEY );
        $address           = wp_parse_args( $submitted_address, $defaults );

        $address = array_map( 'sanitize_text_field', $address );

        return $address;
    }
}
