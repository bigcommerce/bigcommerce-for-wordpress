<?php

namespace BigCommerce\Webhooks;

/**
 * Class Webhook_Listener
 *
 * @package BigCommerce\Webhooks
 */
class Webhook_Listener {

	/**
	 * @var Webhook[]
	 */
	private $hooks;

	/**
	 * Webhook_Listener constructor.
	 *
	 * @param Webhook[] $hooks
	 */
	public function __construct( array $hooks ) {
		foreach ( $hooks as $hook ) {
			$this->hooks[ $hook->get_name() ] = $hook;
		}
	}

	/**
	 * @param array $args
	 */
	public function handle_request( $args ) {
		if ( array_key_exists( $args[ 0 ], $this->hooks ) ) {
			$this->hooks[ $args[ 0 ] ]->receive();
		}
	}
}