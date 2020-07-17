<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Pages\Required_Page;
use BigCommerce\Settings\Screens\Settings_Screen;

class Gift_Certificates extends Settings_Section {
	use WithPages;

	const NAME          = 'gift_certificates';
	const OPTION_ENABLE = 'bigcommerce_enable_gift_certificates';

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
			__( 'Gift Certificate Settings', 'bigcommerce' ),
			function ( $section ) {
				do_action( 'bigcommerce/settings/render/gift_certificates', $section );
			},
			Settings_Screen::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::OPTION_ENABLE
		);

		add_settings_field(
			self::OPTION_ENABLE,
			esc_html( __( 'Enable Gift Certificates', 'bigcommerce' ) ),
			[ $this, 'render_enable_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'label_for' => 'field-' . self::OPTION_ENABLE,
			]
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
	}

	public function render_enable_field() {
		$value    = (bool) get_option( self::OPTION_ENABLE, true );
		$checkbox = sprintf( '<input id="field-%s" type="checkbox" value="1" class="regular-text code" name="%s" %s />', esc_attr( self::OPTION_ENABLE ), esc_attr( self::OPTION_ENABLE ), checked( true, $value, false ) );
		printf( '<p class="description">%s %s</p>', $checkbox, esc_html( __( 'If enabled, customers will be able to purchase gift certificates for store credit on new pages that contain a form and a method to check balances.', 'bigcommerce' ) ) );
	}

}