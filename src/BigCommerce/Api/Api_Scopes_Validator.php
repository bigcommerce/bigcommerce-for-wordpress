<?php

namespace BigCommerce\Api;

use BigCommerce\Logging\Error_Log;

class Api_Scopes_Validator extends v2ApiAdapter {

	const CUSTOMERS_RESOURCE       = '/customers';
	const MARKETING_RESOURCE       = '/gift_certificates';
	const ORDERS_RESOURCE          = '/orders';
	const PAYMENT_METHODS_RESOURCE = '/payments/methods';

	/**
	 * Validate scopes for several API items in order to make pre check during onboarding process
	 *
	 * @return bool
	 *
	 * @throws \Exception
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
