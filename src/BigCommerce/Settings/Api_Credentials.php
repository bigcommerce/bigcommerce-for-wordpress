<?php


namespace BigCommerce\Settings;

class Api_Credentials extends Settings_Section {
	const NAME                 = 'credentials';
	const OPTION_STORE_URL     = 'bigcommerce_store_url';
	const OPTION_CLIENT_ID     = 'bigcommerce_client_id';
	const OPTION_CLIENT_SECRET = 'bigcommerce_client_secret';
	const OPTION_ACCESS_TOKEN  = 'bigcommerce_access_token';

	private $option_to_env_map = [
		self::OPTION_STORE_URL     => 'BIGCOMMERCE_API_URL',
		self::OPTION_CLIENT_ID     => 'BIGCOMMERCE_CLIENT_ID',
		self::OPTION_CLIENT_SECRET => 'BIGCOMMERCE_CLIENT_SECRET',
		self::OPTION_ACCESS_TOKEN  => 'BIGCOMMERCE_ACCESS_TOKEN',
	];

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 * @action bigcommerce/settings/register/screen= . Connect_Account_Screen::NAME
	 */
	public function register_settings_section( $suffix, $screen ) {

		add_settings_section(
			self::NAME,
			__( 'API Credentials', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/credentials', $section );
			},
			$screen
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
				$screen,
				self::NAME,
				$args
			);

			if ( ! $this->get_option_from_env( $key ) ) { // prevents clearing out value in DB when constant is set
				register_setting( $screen, $key );
			}
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

	public function render_field( $args ) {
		$option = $args[ 'option' ];
		if ( array_key_exists( $option, $this->option_to_env_map ) ) {
			$env = bigcommerce_get_env( $this->option_to_env_map[ $option ] );
		}
		$disabled = empty( $env ) ? false : __( 'Set in wp-config.php.', 'bigcommerce' );
		/**
		 * Filter whether to disable the API settings field
		 *
		 * @param bool|string $disabled Empty if the field should be enabled, a string indicating why it's disabled otherwise.
		 */
		$disabled = apply_filters( 'bigcommerce/settings/api/disabled/field=' . $option, $disabled );
		if ( ! $disabled ) {
			parent::render_field( $args );
		} else {
			$args[ 'disabled' ] = $disabled;
			$this->render_text( $args );
		}
	}

	/**
	 * Render the field as a disabled text field without a name. Password
	 * fields will be rendered with bullets in place of the value.
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	protected function render_text( $args ) {
		$option = $args[ 'option' ];
		if ( $args[ 'type' ] === 'password' ) {
			$value = '••••••••••••••••••••••••••••••••';
		} else {
			$default = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
			$value   = get_option( $option, $default );
		}
		printf( '<input type="text" value="%s" class="regular-text code" disabled="disabled" data-lpignore="true" />', esc_attr( $value ) );
		if ( ! empty( $args[ 'description' ] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args[ 'description' ] ) );
		}
		if ( ! empty( $args[ 'disabled' ] ) && is_string( $args[ 'disabled' ] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args[ 'disabled' ] ) );
		}
	}

	/**
	 * @param bool|mixed $value
	 * @param string     $option
	 * @param mixed      $default
	 *
	 * @return mixed
	 * @filter pre_option_ . self::OPTION_STORE_URL
	 * @filter pre_option_ . self::OPTION_CLIENT_ID
	 * @filter pre_option_ . self::OPTION_CLIENT_SECRET
	 * @filter pre_option_ . self::OPTION_ACCESS_TOKEN
	 */
	public function filter_option_with_env( $value, $option, $default ) {
		if ( empty( $value ) ) {
			$env = $this->get_option_from_env( $option );
			if ( $env ) {
				return $env;
			}
		}

		return $value;
	}

	/**
	 * Get the options corresponding environment variable/constant
	 * value if it is set. Returns false if it is not.
	 *
	 * @param string $option
	 *
	 * @return bool|string
	 */
	private function get_option_from_env( $option ) {
		if ( array_key_exists( $option, $this->option_to_env_map ) ) {
			return bigcommerce_get_env( $this->option_to_env_map[ $option ] );
		}

		return false;
	}
}