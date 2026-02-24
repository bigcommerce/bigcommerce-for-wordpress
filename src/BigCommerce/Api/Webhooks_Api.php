<?php

namespace BigCommerce\Api;

/**
 * Creates an adapter class for the BC v2 API webhook endpoint.
 *
 * Provides methods for managing webhooks through the BigCommerce v2 API, including listing,
 * creating, deleting, and updating webhooks.
 *
 * @package BigCommerce
 */
class Webhooks_Api extends v2ApiAdapter {

    /**
     * List all webhooks.
     *
     * Retrieves a list of all webhooks through the client class's `listWebhooks` method.
     *
     * @return mixed The list of webhooks.
     */
	public function listWebhooks(  ) {
		return call_user_func( [ $this->client_class, 'listWebhooks' ] );
	}

    /**
     * Create a new webhook.
     *
     * Creates a new webhook by calling the client class's `createWebhook` method.
     *
     * @param mixed $object The webhook object to create.
     *
     * @return mixed The result of the create operation.
     */
	public function createWebhook( $object ) {
		return call_user_func( [ $this->client_class, 'createWebhook' ], $object );
	}

    /**
     * Delete a webhook by ID.
     *
     * Deletes the specified webhook by calling the client class's `deleteWebhook` method.
     *
     * @param int $id The ID of the webhook to delete.
     *
     * @return mixed The result of the delete operation.
     */
	public function deleteWebhook( $id ) {
		return call_user_func( [ $this->client_class, 'deleteWebhook' ], $id );
	}

    /**
     * Update an existing webhook.
     *
     * Updates the specified webhook by calling the client class's `updateWebhook` method.
     *
     * @param int   $id     The ID of the webhook to update.
     * @param mixed $object The updated webhook data.
     *
     * @return mixed The result of the update operation.
     */
	public function updateWebhook( $id, $object ) {
		return call_user_func( [ $this->client_class, 'updateWebhook' ], $id, $object );
	}
}
