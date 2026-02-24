<?php


namespace BigCommerce\Settings;

use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Channels;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

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
				<button type="button" class="bc-welcome-anchor--startover" data-js="bc-welcome-start-over-trigger" data-content="bc-welcome-start-over"><i class="bc-icon icon-bc-undo"></i> %s</button>
				<script data-js="bc-welcome-start-over" type="text/template">
				<div class="bc-account-reset-logo"></div>
					<div class="bc-welcome__account-reset-content-wrapper">
						<p class="bc-welcome__account-reset-message">%s</p>
						<p class="bc-welcome__account-reset-message">%s</p>
						<div class="bc-welcome__account-reset-actions">
							<button type="button" class="bc-admin-btn" data-js="bc-welcome-account-reset-confirm" data-url="%s">%s</button>
							<button type="button" class="bc-admin-btn bc-admin-btn--outline" data-js="bc-welcome-account-reset-cancel">%s</button>
						</div>
					</div>
				</script>
			</div>
			',
			esc_html( __( 'Start over', 'bigcommerce' ) ),
			esc_html( __( 'You are about to exit the BigCommerce Store Setup. Your progress will not be saved.', 'bigcommerce' ) ),
			esc_html( __( 'Are you sure you want to quit?', 'bigcommerce' ) ),
			esc_url( $url ),
			esc_html( __( "Yes, I'm Sure", 'bigcommerce' ) ),
			esc_html( __( 'Nevermind', 'bigcommerce' ) )
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

		$connections = new Connections();
		$connected = $connections->active();
		foreach ( $connected as $channel ) {
			update_post_meta( $channel->term_id, Channel::STATUS, Channel::STATUS_DISCONNECTED );
		}
		/**
		 * Filters the URL to reset onboarding progress.
		 *
		 * @param string $url The default URL for resetting onboarding.
		 * @return string The modified URL.
		 */
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