<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Pages\Required_Page;
use BigCommerce\Settings\Screens\Settings_Screen;

class Account_Settings extends Settings_Section {
	use WithPages;

	const NAME = 'accounts';

	const SUPPORT_EMAIL = 'bigcommerce_support_email';

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
		register_setting( Settings_Screen::NAME, self::SUPPORT_EMAIL, [
			'sanitize_callback' => 'is_email',
		] );
	}

}