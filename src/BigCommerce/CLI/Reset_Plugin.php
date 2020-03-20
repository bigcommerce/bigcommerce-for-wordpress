<?php


namespace BigCommerce\CLI;

use BigCommerce\Webhooks\Webhook_Versioning;

/**
 * Class Reset_Plugin
 *
 * Usage:
 *        wp bigcommerce dev reset-plugin
 */
class Reset_Plugin extends Command {

	protected function command() {
		return 'dev reset-plugin';
	}

	protected function description() {
		return __( 'Resets database options to bring you back to the beginning of the account onboarding flow.', 'bigcommerce' );
	}

	protected function arguments() {
		return [];
	}

	public function run( $args, $assoc_args ) {
		$options_to_delete = [
			'bigcommerce_account_id',
			'bigcommerce_store_id',
			'bigcommerce_channel_id',
			'bigcommerce_channel_id',
			'bigcommerce_webhooks',
			'schema-' . Webhook_Versioning::class,
			'bigcommerce_store_url',
			'bigcommerce_client_id',
			'bigcommerce_client_secret',
			'bigcommerce_access_token',
			'bigcommerce_nav_setup_complete',
			'bigcommerce_store_type_option_complete',
			'bigcommerce_enable_mini_cart',
		];

		foreach ( $options_to_delete as $option ) {
			\WP_CLI::debug( sprintf( __( 'Deleting option %s', 'bigcommerce' ), $option ) );
			delete_option( $option );
		}
		\WP_CLI::success( __( 'Reset complete. Your site is ready to begin account onboarding.', 'bigcommerce' ) );
	}

}
