<?php


namespace BigCommerce\Settings;


use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Required_Page;

class Account_Settings extends Settings_Section {
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
	 * @action bigcommerce/settings/register
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
				[ 'page' => $page ]
			);
		}

		add_settings_field(
			self::SUPPORT_EMAIL,
			esc_html( __( 'Support Email', 'bigcommerce' ) ),
			[ $this, 'render_field', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'option' => self::SUPPORT_EMAIL,
				'label'  => __( 'Support Email', 'bigcommerce' ),
				'type'   => 'text',
			]
		);
		register_setting( Settings_Screen::NAME, self::SUPPORT_EMAIL, [
			'sanitize_callback' => 'is_email',
		] );
	}

	public function render_page_field( $args ) {
		/** @var Required_Page $page */
		$page   = $args[ 'page' ];
		$option = $page->get_option_name();
		$value  = (int) get_option( $option, 0 );

		do_action( 'bigcommerce/settings/accounts/before_page_field', $page, $value );
		do_action( 'bigcommerce/settings/accounts/before_page_field/page=' . $option, $page, $value );

		$candidates = $page->get_post_candidates();
		$options    = array_map( function ( $post_id ) use ( $value ) {
			return sprintf( '<option value="%d" %s>%s</option>', $post_id, selected( $post_id, $value, false ), esc_html( get_the_title( $post_id ) ) );
		}, $candidates );
		if ( empty( $options ) ) {
			$options     = [
				sprintf( '<option value="0">&mdash; %s &mdash;</option>', __( 'No pages available', 'bigcommerce' ) ),
			];
			$description = sprintf( __( 'Create a page with the %s shortcode, then select it here.', 'bigcommerce' ), $page->get_content() );
		} else {
			array_unshift( $options, sprintf( '<option value="0">&mdash; %s &mdash;</option>', sprintf( __( 'Select %s', 'bigcommerce' ), $page->get_post_state_label() ) ) );
		}

		printf( '<select name="%s" class="regular-text">%s</select>', esc_attr( $option ), implode( "\n", $options ) );
		if ( ! empty( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}

		do_action( 'bigcommerce/settings/accounts/after_page_field', $page, $value );
		do_action( 'bigcommerce/settings/accounts/after_page_field/page=' . $option, $page, $value );
	}

}