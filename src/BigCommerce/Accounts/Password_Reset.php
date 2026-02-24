<?php

namespace BigCommerce\Accounts;

use BigCommerce\Api\Customer_Api;

/**
 * Class Password_Reset
 *
 * Responsible for handling customer password reset and synchronization with BigCommerce.
 */
class Password_Reset {

    /** @var Customer_Api */
    private $customer_api;

    /**
     * Password_Reset constructor.
     *
     * @param Customer_Api $customer_api The Customer_Api instance for interacting with BigCommerce's API.
     */
    public function __construct( Customer_Api $customer_api ) {
        $this->customer_api = $customer_api;
    }

    /**
     * Syncs the password reset with BigCommerce when a user submits the reset password form on the front end.
     *
     * @param \WP_User $user     The WordPress user object.
     * @param string   $new_pass The new user password.
     *
     * @return void
     * @action after_password_reset
     */
    public function sync_reset_password_with_bigcommerce( $user, $new_pass ) {
        $sync = get_user_meta( $user->ID, User_Profile_Settings::SYNC_PASSWORD, true );
        if ( ! $sync ) {
            return;
        }

        $this->set_password( $user, $new_pass );
    }

    /**
     * Syncs the password change with BigCommerce when a user's password is updated from the admin.
     *
     * @param int      $user_id       The user ID.
     * @param \WP_User $old_user_data The WP_User object containing user's data prior to update.
     *
     * @return void
     * @action profile_update
     */
    public function sync_password_change_with_bigcommerce( $user_id, $old_user_data ) {
        // $_POST is the only place we can find the plain text password
        $pass1 = filter_input( INPUT_POST, 'pass1', FILTER_UNSAFE_RAW ); // phpcs:ignore
        if ( empty( $pass1 ) ) {
            return; // not a request to update a user's password
        }
        $sync = get_user_meta( $user_id, User_Profile_Settings::SYNC_PASSWORD, true );
        if ( ! $sync ) {
            return;
        }
        $current_user = new \WP_User( $user_id );

        if ( $current_user->user_pass === $old_user_data->user_pass ) {
            return; // nothing changes
        }

        $this->set_password( $current_user, $pass1 );
    }

    /**
     * Sets the customer's password in BigCommerce.
     *
     * @param \WP_User $user     The WordPress user object.
     * @param string   $password The new password to set.
     *
     * @return bool Whether the password was successfully updated in BigCommerce.
     */
    private function set_password( $user, $password ) {
        $customer    = new Customer( $user->ID );
        $customer_id = $customer->get_customer_id();
        if ( ! $customer_id ) {
            /*
             * If an account is set to sync with BigCommerce, but we don't know
             * the customer ID, we'll look it up here.
             */
            $customer_id = $this->customer_api->find_customer_id_by_email( $user->user_email );
        }
        if ( ! $customer_id ) {
            return false;
        }

        $profile = [
            '_authentication' => [
                'password' => $password,
            ],
        ];

        try {
            $this->customer_api->updateCustomer( $customer_id, $profile );

            return true;
        } catch ( \Exception $e ) {
            return false;
        }
    }
}
