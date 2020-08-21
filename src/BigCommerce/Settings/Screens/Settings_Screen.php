<?php


namespace BigCommerce\Settings\Screens;


use BigCommerce\Container\Settings;
use BigCommerce\Import\Runner\Status;

class Settings_Screen extends Abstract_Screen {
	const NAME = 'bigcommerce';

	protected function get_page_title() {
		return __( 'BigCommerce Plugin Settings', 'bigcommerce' );
	}

	protected function get_menu_title() {
		return __( 'Settings', 'bigcommerce' );
	}

	public function get_hook_suffix() {
		return $this->hook_suffix;
	}

	/**
	 * @return void
	 * @action admin_menu
	 */
	public function register_settings_page() {
		parent::register_settings_page();
		/**
		 * Triggered after registering the main settings screen
		 *
		 * @param string $hook_suffix
		 *
		 * @deprecated 2018-08-30 Use 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME
		 */
		do_action( 'bigcommerce/settings/register', $this->hook_suffix );
	}

	public function get_header() {
		ob_start();

		/**
		 * Filters the message that displays at the top of the settings page
		 *
		 * @param string $welcome_message
		 */
		$welcome_message = apply_filters( 'bigcommerce/settings/header/welcome_message', __( 'Welcome back.', 'bigcommerce' ) );

		$powered_by = sprintf( __( 'Powered by <a href="%1$s">BigCommerce</a>' ), esc_url( 'https://www.bigcommerce.com/' ) );

		$last_import_date = $this->last_import_date();


		echo '<div class="bc-settings-header bg-geometric-bg">';

		echo '<div class="wp-header-end"></div>'; // placeholder to tell WP where to put notices

		echo '<div class="bc-settings-header__welcome">';
		printf( '<h1 class="bc-settings-header__welcome-title">%s</h1>', esc_html( $welcome_message ) );
		printf( '<div class="bc-settings-header__welcome-text">%s</div>', $powered_by );
		echo '</div>'; // bc-settings__welcome

		echo '<div class="bc-settings-header__cta">';
		printf( '<h2 class="bc-settings-header__cta-title">%s</h2>', esc_html( __( 'Manage your products.', 'bigcommerce' ) ) );
		if ( $last_import_date ) {
			printf( '<p class="bc-settings-header__cta-text">%s</p>', sprintf(
				esc_html( __( 'Your last sync was on %s', 'bigcommerce' ) ),
				$last_import_date
			) );
		}
		echo '<div class="bc-settings-header__cta-btn" data-js="bc-product-sync-button">';
		printf( '<a href="%s" target="_blank" rel="noopener" class="bc-admin-btn bc-settings-header__manage-button">%s</a>',
			esc_url( $this->manage_products_url() ),
			esc_html( __( 'Manage on BigCommerce', 'bigcommerce' ) )
		);

		/**
		 * Triggered after rendering the last import date in the settings header
		 */
		do_action( 'bigcommerce/settings/header/import_status' );

		echo '</div>'; // bc-settings__cta-buttons

		echo '</div>'; // bc-settings__cta

		echo '</div>'; // bc-settings__header

		return ob_get_clean();
	}

	protected function submit_button() {
		echo '<div class="bc-plugin-page-header">';
		printf( '<a href="%s" target="_blank" rel="noopener"><img class="bc-settings-save__logo" src="%s" alt="%s" /></a>', esc_url( $this->login_url() ), trailingslashit( $this->assets_url ) . 'img/admin/big-commerce-logo.svg', esc_html( __( 'BigCommerce', 'bigcommerce' ) ) );
		submit_button();
		echo '</div>';
	}

	/**
	 * @return string The date of the last import. Empty if not available.
	 */
	private function last_import_date() {
		$status    = new Status();
		$previous  = $status->previous_status();
		$timestamp = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', (int) $previous[ 'timestamp' ] ) ) );
		$date      = date_i18n( get_option( 'date_format', 'Y-m-d' ), $timestamp, false );
		switch ( $previous[ 'status' ] ) {
			case Status::COMPLETED:
			case Status::FAILED:
				return $date;
			case Status::NOT_STARTED:
			default:
				return '';
		}
	}

	private function login_url() {
		return 'https://login.bigcommerce.com/';
	}

	private function manage_products_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/products';
	}

	/**
	 * Render a link to support documentation at BigCommerce
	 *
	 * @return void
	 * @action bigcommerce/settings/after_form/page= . self::NAME
	 */
	public function render_support_link() {
		$support_link = 'https://support.bigcommerce.com/s/article/BigCommerce-for-WordPress-Resources';
		printf(
			'<p><a href="%s">%s</a></p>',
			esc_url( $support_link ),
			esc_html( __( 'Have questions? Need help?', 'bigcommerce' ) )
		);
	}

	public function should_register() {
		return $this->configuration_status >= Settings::STATUS_COMPLETE;
	}
}
