<?php


namespace BigCommerce\Settings;


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


		echo '<div class="bc-settings-header">';

		echo '<div class="wp-header-end"></div>'; // placeholder to tell WP where to put notices

		echo '<div class="bc-settings-header__welcome">';
		printf( '<h1 class="bc-settings-header__welcome-title">%s</h1>', esc_html( $welcome_message ) );
		printf( '<div class="bc-settings-header__welcome-text">%s</div>', $powered_by );
		echo '</div>'; // bc-settings__welcome

		echo '<div class="bc-settings-header__cta">';
		printf( '<h2 class="bc-settings-header__cta-title">%s</h2>', __( 'Sync your products.', 'bigcommerce' ) );
		if ( $last_import_date ) {
			printf( '<p class="bc-settings-header__cta-text">%s</p>', sprintf(
				__( 'Your last sync was on %s', 'bigcommerce' ),
				$last_import_date
			) );
		}

		/**
		 * Triggered after rendering the last import date in the settings header
		 */
		do_action( 'bigcommerce/settings/header/import_status' );

		echo '</div>'; // bc-settings__status

		echo '</div>'; // bc-settings__header

		return ob_get_clean();
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

	protected function should_register() {
		return $this->plugin_configured;
	}
}
