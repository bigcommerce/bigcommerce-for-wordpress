<?php


namespace BigCommerce\CLI;

use BigCommerce\Webhooks\Webhook_Versioning;

/**
 * Resets various options in WordPress to bring the site back to the beginning of the BigCommerce account onboarding flow.
 * This command deletes key options related to the BigCommerce integration, allowing the user to restart the onboarding process.
 *
 * Usage: wp bigcommerce dev reset-plugin
 * 
 * @package BigCommerce
 * @subpackage CLI
 */
class Reset_Plugin extends Command {

    /**
     * Declare the command name.
     *
     * @return string The command name for resetting the plugin.
     */
    protected function command() {
        return 'dev reset-plugin';
    }

    /**
     * Add a command description.
     *
     * @return string|void A description of the reset-plugin command.
     */
    protected function description() {
        return __( 'Resets database options to bring you back to the beginning of the account onboarding flow.', 'bigcommerce' );
    }

    /**
     * Declare command arguments.
     *
     * @return array[] Command arguments for the reset-plugin command.
     */
    protected function arguments() {
        return [];
    }

    /**
     * Executes the reset process by deleting specific options from the database.
     *
     * This method deletes a set of options that are related to the BigCommerce integration, effectively resetting the state of the plugin
     * so that the user can restart the onboarding process.
     *
     * @param array $args Arguments passed to the command.
     * @param array $assoc_args Associated arguments.
     * @return void
     */
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
            'bigcommerce_allow_global_logins',
        ];

        foreach ( $options_to_delete as $option ) {
            \WP_CLI::debug( sprintf( __( 'Deleting option %s', 'bigcommerce' ), $option ) );
            delete_option( $option );
        }
        \WP_CLI::success( __( 'Reset complete. Your site is ready to begin account onboarding.', 'bigcommerce' ) );
    }

}
