<?php


namespace BigCommerce\Settings;

use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Channels;

/**
 * Class Start_Over
 *
 * Resets credentials, allowing the merchant to start the onboarding
 * process again
 */
class Start_Over {
	const ACTION = 'bigcommerce_reset';

	/**
	 * @return void
	 * @action bigcommerce/settings/after_form/page= . Api_Credentials_Screen::NAME
	 * @action bigcommerce/settings/after_form/page= . Create_Account_Screen::NAME
	 * @action bigcommerce/settings/after_form/page= . Connect_Channel_Screen::NAME
	 * @action bigcommerce/settings/after_content/page= . Pending_Account_Screen::NAME
	 */
	public function add_link_to_settings_screen() {
		$url = $this->get_reset_url();
		printf(
			'
			<div class="bc-welcome-reset">
				<button type="button" class="bc-welcome-anchor--startover bc-admin-btn bc-admin-btn--outline" data-js="bc-welcome-start-over-trigger" data-content="bc-welcome-start-over">%s</button>
				<script data-js="bc-welcome-start-over" type="text/template">
					<p class="bc-welcome__account-reseet-message">%s</p>
					<div class="bc-welcome__account-reset-actions">
						<button type="button" class="bc-admin-btn" data-js="bc-welcome-account-reset-confirm" data-url="%s">%s</button>
						<button type="button" class="bc-admin-btn bc-admin-btn--outline" data-js="bc-welcome-account-reset-cancel">%s</button>
					</div>
				</script>
			</div>
			',
			__( 'Start over', 'bigcommerce' ),
			__( 'You are about to remove all of your account credentials stored in WordPress. Would you like to continue?', 'bigcommerce' ),
			esc_url( $url ),
			__( 'Confirm', 'bigcommerce' ),
			__( 'Cancel', 'bigcommerce' )
		);
	}

	/**
	 * @return void
	 * @action admin_post_ . self::NAME
	 */
	public function reset_credentials() {
		check_admin_referer( self::ACTION );

		$options = [
			Api_Credentials::OPTION_STORE_URL,
			Api_Credentials::OPTION_CLIENT_ID,
			Api_Credentials::OPTION_CLIENT_SECRET,
			Api_Credentials::OPTION_ACCESS_TOKEN,
			Channels::CHANNEL_ID,
			Channels::CHANNEL_NAME,
			Onboarding_Api::ACCOUNT_ID,
			Onboarding_Api::STORE_ID,
		];
		foreach ( $options as $name ) {
			delete_option( $name );
		}

		$redirect = apply_filters( 'bigcommerce/onboarding/reset', admin_url() );
		wp_safe_redirect( $redirect, 303 );
		exit();
	}

	private function get_reset_url() {
		$url = admin_url( 'admin-post.php' );
		$url = add_query_arg( [
			'action' => self::ACTION,
		], $url );
		$url = wp_nonce_url( $url, self::ACTION );

		return $url;
	}
}