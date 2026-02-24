<?php

namespace BigCommerce\Container;

use Pimple\Container;

/**
 * This class provides methods to handle post metadata related tasks in the container. It extends the Provider class
 * and registers dependencies related to post metadata processing within the container.
 */
class Post_Meta extends Provider {

    /**
     * Registers the necessary post metadata services in the container.
     *
     * This function is responsible for adding any services or dependencies related to post metadata handling to the container.
     * It is called during the registration phase of the container setup.
     *
     * @param Container $container The container instance used to register the services.
     */
	public function register( Container $container ) {
		$this->import( $container );
	}

	private function import( Container $container ) {

	}
}
