<?php

namespace BigCommerce\Assets\Admin;

/**
 * Class JS_Localization
 *
 * Provides the localization strings needed for the admin JavaScript files. 
 * These strings include messages, button labels, and error messages that are 
 * used in the admin UI and are available for translation.
 *
 * @package BigCommerce\Assets\Admin
 */
class JS_Localization {
	/**
	 * Stores all text strings needed in the admin scripts.js file
	 *
	 * The code below is an example of structure. Check the readme js section for more info on how to use.
	 *
	 * @return array
	 */
	public function get_data() {
		$js_i18n_array = [
			'buttons'    => [
				'add_product'         => __( 'Add Product', 'bigcommerce' ),
				'remove_product'      => __( 'Remove Product', 'bigcommerce' ),
				'remove_selected'     => __( '(Remove)', 'bigcommerce' ),
				'oauth_popup_trigger' => __( 'Click here to authorize WordPress to connect to your BigCommerce account', 'bigcommerce' ),
			],
			'messages'   => [
				'ajax_error'                             => __( 'There was an error submitting or retrieving your request. Please try again.', 'bigcommerce' ),
				'no_results'                             => __( 'We could not find any products that matched your query. Please clear your search and try again.', 'bigcommerce' ),
				'no_products'                            => __( 'We could not find any products that matched your query. Please edit this block and try again.', 'bigcommerce' ),
				'excessive_attempts'                     => __( 'We are still working on connecting your account. It should not be too much longer.', 'bigcommerce' ),
				'account_connection_error'               => __( 'There was an error connecting your account:', 'bigcommerce' ),
				'account_connection_code'                => __( 'Error Code:', 'bigcommerce' ),
				'account_connection_message'             => __( 'Error Message:', 'bigcommerce' ),
				'account_creation_message'               => __( "We're getting your account ready.", 'bigcommerce' ),
				'channel_confirmation'                   => __( "The %s channel will point to this site's URL and its route settings will be updated in BigCommerce.", 'bigcommerce' ),
				'diagnostics_success_message'            => __( 'Data retrieved successfully', 'bigcommerce' ),
				'diagnostics_request_error_header'       => __( 'Diagnostics Unavailable', 'bigcommerce' ),
				'diagnostics_request_error_message'      => __( 'There was an error trying to retrieve information about your site. Please try again.', 'bigcommerce' ),
				'diagnostics_template_overrides_message' => __( 'If you are experiencing issues, it may be due to template overrides that rely on older versions of the plugin. Please review your overrides and remove any that may not be necessary.', 'bigcommerce' ),
				'sync'                                   => [
					'success'      => __( 'Products Successfully Synced', 'bigcommerce' ),
					'error'        => __( 'There was an error syncing your products. Please try to import again. If the error persists, please contact support.', 'bigcommerce' ),
					'timeout'      => __( "The server is taking longer than expected to respond. We’ll keep trying, so don't worry. If the problem persists, try reducing the batch size in the Product Sync settings panel.", 'bigcommerce' ),
					'server_error' => __( 'The server sent an unexpected response. We’ll keep trying, but it may take a few minutes to get things moving again. If the problem persists, try turning on error logging in the Diagnostics settings panel.', 'bigcommerce' ),
					'unauthorized' => __( 'An error occurred while validating your request. Please refresh the page and try again.', 'bigcommerce' ),
				],
				'dismiss_notification'              => __( 'dismiss notification', 'bigcommerce' ),
				'no_resources_json_data'            => __( 'There was an error retrieving the resources data. Please refresh this page and try again.', 'bigcommerce' ),
			],
			'operations' => [
				'loading'                => __( 'Loading', 'bigcommerce' ),
				'query_string_separator' => __( '&', 'bigcommerce' ),
			],
			'text'       => [
				'id_prefix' => __( 'ID:', 'bigcommerce' ),
			],
		];

		/**
		 * Filters admin js localization data.
		 *
		 * Allows modification of the JS localization data before it is returned.
		 *
		 * @param array $js_i18n_array Js i18n data.
		 */
		return apply_filters( 'bigcommerce/admin/js_localization', $js_i18n_array );
	}

	/**
	 * Recursively sanitize all the strings with wp_kses
	 *
	 * @param string[]|string $strings The strings to sanitize.
	 *
	 * @return array|string The sanitized strings.
	 */
	private function kses_strings( $strings ) {
		if ( is_array( $strings ) ) {
			return array_map( [ $this, 'kses_strings' ], $strings );
		}
		if ( is_string( $strings ) ) {
			return wp_kses( $strings, 'data' );
		}

		return $strings;
	}
}
