<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Pages\Required_Page;
use BigCommerce\Settings\Screens\Settings_Screen;

class Account_Settings extends Settings_Section {
	use WithPages;

	const NAME = 'accounts';

	const SUPPORT_EMAIL           = 'bigcommerce_support_email';
	const REGISTRATION_SPAM_CHECK = 'bigcommerce_registration_spam_check';

	/**
	 * @var Required_Page[]
	 */
	private $pages = [];

	public function __construct( array $pages ) {
		$this->pages = $pages;
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Accounts and Registration', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/accounts', $section );
			},
			Settings_Screen::NAME
		);

		foreach ( $this->pages as $page ) {
			register_setting(
				Settings_Screen::NAME,
				$page->get_option_name()
			);
			add_settings_field(
				$page->get_option_name(),
				$page->get_post_state_label(),
				[ $this, 'render_page_field' ],
				Settings_Screen::NAME,
				self::NAME,
				[
					'page'      => $page,
					'label_for' => 'field-' . $page->get_option_name(),
				]
			);
		}

		register_setting( Settings_Screen::NAME, self::SUPPORT_EMAIL, [
			'sanitize_callback' => 'is_email',
		] );

		add_settings_field(
			self::SUPPORT_EMAIL,
			esc_html( __( 'Support Email', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option'    => self::SUPPORT_EMAIL,
				'label'     => __( 'Support Email', 'bigcommerce' ),
				'label_for' => 'field-' . self::SUPPORT_EMAIL,
				'type'      => 'text',
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::REGISTRATION_SPAM_CHECK
		);

		add_settings_field(
			self::REGISTRATION_SPAM_CHECK,
			esc_html( __( 'Registration Spam Check', 'bigcommerce' ) ),
			[ $this, 'render_registration_spam_check_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'        => 'checkbox',
				'option'      => self::REGISTRATION_SPAM_CHECK,
				'label'       => __( 'Registration Spam Check', 'bigcommerce' ),
				'description' => __( 'Enable user registration spam check with Akismet.', 'bigcommerce' ),
			]
		);

	}

	public function render_registration_spam_check_field() {
		if ( ! is_plugin_active( 'akismet/akismet.php' ) ) {
			printf( '<p class="description">%s</p>', sprintf(
				esc_html( __( '%sAkismet plugin%s needs to be active and configured to enable this feature.', 'bigcommerce' ) ),
				sprintf( '<a target="__blank" href="%s">', esc_url( 'https://docs.akismet.com/getting-started/api-key/' ) ),
				'</a>'
			) );
			return;
		}
		$value    = (bool) get_option( self::REGISTRATION_SPAM_CHECK, true );
		$checkbox = sprintf( '<input id="field-%s" type="checkbox" value="1" class="regular-text code" name="%s" %s />', esc_attr( self::REGISTRATION_SPAM_CHECK ), esc_attr( self::REGISTRATION_SPAM_CHECK ), checked( true, $value, false ) );
		printf( '<p class="description">%s %s</p>', $checkbox, esc_html( __( 'If enabled, customer registration form will check for spam by using Akismet before creating new customers.', 'bigcommerce' ) ) );
	}

}