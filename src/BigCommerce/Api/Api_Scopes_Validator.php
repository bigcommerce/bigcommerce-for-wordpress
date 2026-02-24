<?php

namespace BigCommerce\Api;

use BigCommerce\Logging\Error_Log;

/**
 * Validates the API scopes for several resources, ensuring that the correct permissions
 * are in place during the onboarding process. This includes validating scopes for 
 * customers, orders, and payment methods.
 *
 * @package BigCommerce\Api
 */
class Api_Scopes_Validator extends v2ApiAdapter {

    /**
     * Customers resource endpoint.
     * @var string
     */
    const CUSTOMERS_RESOURCE       = '/customers';

    /**
     * Marketing resource endpoint (gift certificates).
     * @var string
     */
    const MARKETING_RESOURCE       = '/gift_certificates';

    /**
     * Orders resource endpoint.
     * @var string
     */
    const ORDERS_RESOURCE          = '/orders';

    /**
     * Payment methods resource endpoint.
     * @var string
     */
    const PAYMENT_METHODS_RESOURCE = '/payments/methods';

    /**
     * Validates scopes for several API items to ensure that the necessary permissions
     * are in place during the onboarding process.
     *
     * This method checks the scopes for customers, orders, and payment methods and
     * throws an exception if any of them are invalid.
     *
     * @return bool Returns true if all scopes are valid.
     * 
     * @throws \Exception If any of the scopes are invalid.
     */
    public function validate() {
        $customer = $this->validate_customers_modify_scope();

        if ( ! $customer ) {
            throw new \Exception( __( 'Customers API scopes are invalid', 'bigcommerce' ), 500 );
        }

        $this->cleanup_scope_check_resource( self::CUSTOMERS_RESOURCE, $customer );

        $resources = [
            self::ORDERS_RESOURCE,
            self::PAYMENT_METHODS_RESOURCE,
        ];

        foreach ( $resources as $path ) {
            if ( ! $this->validate_scopes( $path ) ) {
                throw new \Exception( sprintf( __( 'API scopes are invalid. Requested resource: %s', 'bigcommerce' ), $path ), 500 );
            }
        }

        return true;
    }

    private function validate_customers_modify_scope() {
        try {
            $result = $this->createResource( self::CUSTOMERS_RESOURCE, [
                'company'    => 'Bigcommerce',
                'email'      => sprintf( 'api-scopecheck-%d@gmail.com', time() ),
                'first_name' => 'Api',
                'last_name'  => 'Scope',
                'phone'      => '1234567890',
            ] );

            if ( ! $result || ! isset( $result->id ) ) {
                return false;
            }

            return $result->id;
        } catch ( \Exception $e ) {
            /**
             * Fires when customer API scope validation fails
             *
             * @param string     $level   The error level (Error_Log::ERROR)
             * @param string     $message The error message
             * @param array      $data    Additional error data including stack trace
             */
            do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not proceed with current API scopes for customers', 'bigcommerce' ), [
                'trace' => $e->getTraceAsString(),
            ] );

            return false;
        }
    }

    private function validate_scopes( $path = '' ) {
        try {
            $result = $this->getResource( $path );

            if ( ! $result ) {
                return false;
            }

            return true;
        } catch ( \Exception $e ) {
            /**
             * Fires when marketing API scope validation fails
             *
             * @param string     $level   The error level (Error_Log::ERROR)
             * @param string     $message The error message
             * @param array      $data    Additional error data including stack trace
             */
            do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not proceed with current API scopes for Marketing', 'bigcommerce' ), [
                'trace' => $e->getTraceAsString(),
            ] );

            return false;
        }
    }

    private function cleanup_scope_check_resource( $path, $id ) {
        $deletePath = sprintf( $path . '/%d', $id );
        $this->deleteResource( $deletePath );
    }
}
