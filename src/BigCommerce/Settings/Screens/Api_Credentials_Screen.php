<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Api\Base_Client;
use BigCommerce\Api\Configuration;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Container\Settings;
use BigCommerce\Settings\Sections\Api_Credentials;

class Api_Credentials_Screen extends Onboarding_Screen {
	const NAME = 'bigcommerce_api_credentials';

	protected function get_page_title() {
		return __( 'Enter Your Account Credentials', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Welcome', 'bigcommerce' );
	}

	protected function parent_slug() {
		return null;
	}

	protected function get_header() {
		$api_accounts_url    = 'https://login.bigcommerce.com/deep-links/settings/auth/api-accounts';
		$documentation_url   = 'https://developer.bigcommerce.com/bigcommerce-for-wordpress/setup/multi-site';
		$descriptive_text = sprintf(
			'<p>%s</p><p><a href="%s">%s</a></p>',
			sprintf(
				_x( 'If you have multiple WordPress sites and this is not the first site you\'ve connected to BigCommerce, it\'s best to %1$screate an API account%2$s for this site in BigCommerce.', 'placeholders are for html anchor opening/closing tags', 'bigcommerce' ),
				sprintf( '<a href="%s" target="_blank">', esc_url( $api_accounts_url ) ),
				'</a>'
			),
			esc_url( $documentation_url ),
			__( 'Click here for more information on creating API credentials, including which scopes to select.', 'bigcommerce' )
		);
		return sprintf(
			'%s<header class="bc-connect__header"><h1 class="bc-settings-connect__title">%s</h1>%s</header>',
			$this->before_title(),
			$this->get_page_title(),
			$descriptive_text
		);
	}

	protected function before_form() {
		parent::before_form();
	}

	protected function submit_button() {
		echo '<div class="bc-settings-save">';
		$this->onboarding_submit_button( 'bc-settings-create-account-button', 'bc-onboarding-arrow', __( 'Connect Using API Credentials', 'bigcommerce' ), true );
		echo '</div>';
	}

	public function should_register() {
		return $this->configuration_status === Settings::STATUS_NEW;
	}


	/**
	 * Validate API credentials before saving
	 *
	 * @return void
	 * @action admin_action_update
	 */
	public function validate_credentials() {
		if ( filter_input( INPUT_POST, 'option_page', FILTER_SANITIZE_STRING ) !== Api_Credentials_Screen::NAME ) {
			return;
		}
		$config = new Configuration();
		$config->setHost( untrailingslashit( filter_input( INPUT_POST, Api_Credentials::OPTION_STORE_URL, FILTER_SANITIZE_URL ) ) );
		$config->setClientId( filter_input( INPUT_POST, Api_Credentials::OPTION_CLIENT_ID, FILTER_SANITIZE_STRING ) );
		$config->setAccessToken( filter_input( INPUT_POST, Api_Credentials::OPTION_ACCESS_TOKEN, FILTER_SANITIZE_STRING ) );
		$config->setClientSecret( filter_input( INPUT_POST, Api_Credentials::OPTION_CLIENT_SECRET, FILTER_SANITIZE_STRING ) );
		$config->setCurlTimeout( apply_filters( 'bigcommerce/api/timeout', 15 ) );
		$client = new Base_Client( $config );
		$api = new CatalogApi( $client );

		try {
			// throws an exception on any non-2xx response
			$api->catalogSummaryGet();
		} catch ( \Exception $e ) {
			add_settings_error( self::NAME, 'submitted', __( 'Unable to connect to the BigCommerce API. Please re-enter your credentials.', 'bigcommerce' ), 'error' );
			set_transient( 'settings_errors', get_settings_errors(), 30 );

			// prevent saving of the options
			wp_safe_redirect( esc_url_raw( add_query_arg( [ 'settings-updated' => 1 ], $this->get_url() ) ), 303 );
			exit();
		}
	}

}
