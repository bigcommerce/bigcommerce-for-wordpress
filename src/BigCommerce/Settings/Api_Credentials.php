<?php


namespace BigCommerce\Settings;

class Api_Credentials extends Settings_Section {
	const NAME                 = 'credentials';
	const OPTION_STORE_URL     = 'bigcommerce_store_url';
	const OPTION_CLIENT_ID     = 'bigcommerce_client_id';
	const OPTION_CLIENT_SECRET = 'bigcommerce_client_secret';
	const OPTION_ACCESS_TOKEN  = 'bigcommerce_access_token';

	/**
	 * @return void
	 * @action bigcommerce/settings/register
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'API Credentials', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/credentials', $section );
			},
			Settings_Screen::NAME
		);

		$options = [
			self::OPTION_STORE_URL     => [
				'label'       => __( 'Base API Path', 'bigcommerce' ),
				'description' => __( 'The API Path for your BigCommerce store. E.g., https://api.bigcommerce.com/stores/abc9defghi/v3/', 'bigcommerce' ),
			],
			self::OPTION_CLIENT_ID     => [
				'label'       => __( 'Client ID', 'bigcommerce' ),
				'description' => __( 'The Client ID provided for your API account. E.g., abcdefg24hijk6lmno8pqrs1tu3vwxyz', 'bigcommerce' ),
				'type'        => 'password',
			],
			self::OPTION_CLIENT_SECRET => [
				'label'       => __( 'Client Secret', 'bigcommerce' ),
				'description' => __( 'The Client Secret provided for your API account. E.g., 0bcdefg24hijk6lmno8pqrs1tu3vw5x', 'bigcommerce' ),
				'type'        => 'password',
			],
			self::OPTION_ACCESS_TOKEN  => [
				'label'       => __( 'Access Token', 'bigcommerce' ),
				'description' => __( 'The Access Token provided for your API account. E.g., ab24cdef68gh13ijkl5mn7opqrst9u2v', 'bigcommerce' ),
				'type'        => 'password',
			],
		];

		foreach ( $options as $key => $args ) {
			$args = wp_parse_args( $args, [
				'option' => $key,
				'label'  => '',
				'type'   => 'text',
			] );
			add_settings_field(
				$key,
				esc_html( $args[ 'label' ] ),
				[ $this, 'render_field', ],
				Settings_Screen::NAME,
				self::NAME,
				$args
			);
			register_setting( Settings_Screen::NAME, $key );
		}
	}

	/**
	 * Add a link to set up the API account
	 *
	 * @return void
	 * @action bigcommerce/settings/render/credentials
	 */
	public function render_help_text() {
		$api_accounts_url = 'https://login.bigcommerce.com/deep-links/settings/auth/api-accounts';
		$help_text        = sprintf(
			__( 'After signing in on BigCommerce with your account owner login, the following credentials can be obtained from the <a href="%s" target="_blank">API Accounts</a> section.', 'bigcomerce' ),
			$api_accounts_url
		);
		printf( '<p class="description">%s</p>', $help_text );
	}
}