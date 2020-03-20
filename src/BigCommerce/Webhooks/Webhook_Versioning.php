<?php

namespace BigCommerce\Webhooks;


use BigCommerce\Settings\Sections\Import;

class Webhook_Versioning {
	const VERSION = 2;

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
	 * @return void
	 * @action bigcommerce/import/fetched_store_settings
	 * @action bigcommerce/settings/webhoooks_updated
	 */
	public function maybe_update_webhooks() {
		if ( ! get_option( Import::ENABLE_WEBHOOKS , 1 ) ) {
			return;
		}

		$version_option = 'schema-' . self::class;
		if ( (int) get_option( $version_option, 0 ) !== self::VERSION ) {
			$this->update_webhooks();
			update_option( 'schema-' . self::class, self::VERSION );
		}
	}

	/**
	 * @return void Set new routes whenever any of the route list element gets updated
	 */
	private function update_webhooks() {
		foreach ( $this->hooks as $hook ) {
			$hook->update();
		}
	}

}
