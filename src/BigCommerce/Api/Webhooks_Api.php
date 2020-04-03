<?php
/**
 * Creates an adapter class for the BC v2 API webhook endpoing.
 *
 * @package BigCommerce
 */

namespace BigCommerce\Api;

class Webhooks_Api extends v2ApiAdapter {
	public function listWebhooks(  ) {
		return call_user_func( [ $this->client_class, 'listWebhooks' ] );
	}

	public function createWebhook( $object ) {
		return call_user_func( [ $this->client_class, 'createWebhook' ], $object );
	}

	public function deleteWebhook( $id ) {
		return call_user_func( [ $this->client_class, 'deleteWebhook' ], $id );
	}
}
