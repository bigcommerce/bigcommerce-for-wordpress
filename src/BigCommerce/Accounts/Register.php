<?php

namespace BigCommerce\Accounts;

use BigCommerce\Api_Factory;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Webhooks\Customer\Customer_Channel_Updater;

/**
 * Class Register
 *
 * Handles customer creation and synchronization with BigCommerce.
 */
class Register {

    /** @var \BigCommerce\Api_Factory */
    private $factory;

    /** @var \BigCommerce\Taxonomies\Channel\Connections */
    private $connections;

    /**
     * Register constructor.
     *
     * @param Api_Factory $api_factory   The API factory for interacting with BigCommerce's API.
     * @param Connections $connections   The connections instance for managing channels.
     */
    public function __construct( Api_Factory $api_factory, Connections $connections ) {
        $this->factory     = $api_factory;
        $this->connections = $connections;
    }

    /**
     * Checks if the customer exists on BigCommerce, creates a new customer if not,
     * and associates the customer with the primary channel.
     *
     * @param int   $user_id   The user ID.
     * @param array $userdata  The user data array.
     *
     * @return void
     */
    public function maybe_create_new_customer( $user_id, $userdata ) {
        if ( empty( $userdata['role'] ) ||  $userdata['role'] !== \BigCommerce\Accounts\Roles\Customer::NAME ) {
            return;
        }

        $customer_id = get_user_meta( $user_id, Customer::CUSTOMER_ID_META, true );

        // Customer already connected
        if ( ! empty( $customer_id ) ) {
            return;
        }

        $customer_id = $this->is_customer_mail_in_use( $userdata['user_email'] );

        if ( $customer_id ) {
            // Connect existing customer
            $customer = new Customer( $user_id );
            $customer->set_customer_id( $customer_id );
            return;
        }

        $this->register_new_customer( $user_id, $userdata );

        $channel    = $this->connections->primary();
        $channel_id = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
        update_user_meta( $user_id, Customer_Channel_Updater::CUSTOMER_CHANNEL_META, [ $channel_id] );
        update_user_meta( $user_id, Customer_Channel_Updater::CUSTOMER_ORIGIN_CHANNEL, $channel_id );
    }

    /**
     * Checks if the customer email is already registered in BigCommerce.
     *
     * @param string $email The customer email to check.
     *
     * @return bool|int Returns the customer ID if found, or false if not found.
     */
    protected function is_customer_mail_in_use( $email ) {
        try {
            $customer_api = $this->factory->customer();
            $matches = $customer_api->getCustomers( [
                'email' => $email,
            ] );

            if ( ! empty( $matches ) ) {
                $found_customer = reset( $matches );
                return $found_customer->id;
            }

            return false;
        } catch( \Exception $exception ) {
            /**
             * Log debug information when unable to check if a user exists.
             *
             * @param string $level The log level (DEBUG)
             * @param string $message The log message
             * @param array $context Additional context data
             */
            do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Could not check if user exists.', 'bigcommerce' ), [
                'user_email' => $email,
            ] );

            /**
             * Log debug information about the exception that occurred.
             *
             * @param string $level The log level (DEBUG)
             * @param string $message The exception message
             * @param array $context Additional context data including stack trace
             */
            do_action( 'bigcommerce/log', Error_Log::DEBUG, $exception->getMessage() , [
                'trace' => $exception->getTraceAsString(),
            ] );
            // We are not able to check and due to this we should avoid customer creation
            return true;
        }
    }

    /**
     * Creates a new customer using the V3 API and associates them with the primary channel.
     *
     * @param int   $user_id   The user ID.
     * @param array $userdata  The user data array.
     *
     * @return int Returns the customer ID if successful, or 0 if creation failed.
     */
    protected function register_new_customer( $user_id, $userdata ) {
        try {
            $customer_api = $this->factory->customers();
            $channel      = $this->connections->primary();
            $channel_id   = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );

            $new_customer_data = [
                'first_name'        => $userdata['first_name'] ?: $userdata['user_login'],
                'last_name'         => $userdata['last_name'] ?: __( 'User', 'bigcommerce' ),
                'email'             => $userdata['user_email'],
                'customer_group_id' => 0,
                'authentication'    => [
                    'force_password_reset' => true,
                    'new_password'         => $userdata['user_pass']
                ],
                'origin_channel_id' => ( int ) $channel_id,
                'channels_ids'      => [ ( int ) $channel_id ],
            ];

            /**
             * Filters customer create arguments.
             *
             * @param array $new_customer_data Customer data.
             */
            $new_customer_data = apply_filters( 'bigcommerce/customer/create/args', $new_customer_data );

            $response = $customer_api->customersPost( [ $new_customer_data ] );

            if ( $response && ! empty( $response->id ) ) {
                $customer = new Customer( $user_id );
                $customer->set_customer_id( $response->id );

                return $response->id;
            }
        } catch ( \Exception $exception ) {
            /**
             * Log debug information when unable to create a customer.
             *
             * @param string $level The log level (DEBUG)
             * @param string $message The log message
             * @param array $context Additional context data
             */
            do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Unable to create customer.', 'bigcommerce' ), [
                'user_id'  => $user_id,
                'userdata' => $userdata,
            ] );

            /**
             * Log debug information about the exception that occurred.
             *
             * @param string $level The log level (DEBUG)
             * @param string $message The exception message
             * @param array $context Additional context data including stack trace
             */
            do_action( 'bigcommerce/log', Error_Log::DEBUG, $exception->getMessage() , [
                'trace' => $exception->getTraceAsString(),
            ] );

        }

        return 0;
    }
	
}
