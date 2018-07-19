<?php


namespace BigCommerce\Container;

use BigCommerce\Settings\Account_Settings;
use BigCommerce\Settings\Analytics as Analytics_Settings;
use BigCommerce\Settings\Api_Credentials;
use BigCommerce\Settings\Connection_Status;
use BigCommerce\Settings\Cart as Cart_Settings;
use BigCommerce\Settings\Gift_Certificates as Gift_Ceritifcate_Settings;
use BigCommerce\Settings\Import as Import_Settings;
use BigCommerce\Settings\Import_Now;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Settings_Screen;
use Pimple\Container;

class Settings extends Provider {
	const SCREEN                   = 'settings.screen';
	const API_SECTION              = 'settings.api';
	const API_STATUS               = 'settings.api_status';
	const CART_SECTION             = 'settings.cart';
	const GIFT_CERTIFICATE_SECTION = 'settings.gift_certificates';
	const CURRENCY_SECTION         = 'settings.currency';
	const IMPORT_SECTION           = 'settings.import';
	const ACCOUNTS_SECTION         = 'settings.accounts';
	const ANALYTICS_SECTION        = 'settings.analytics';
	const IMPORT_NOW               = 'settings.import_now';
	const IMPORT_STATUS            = 'settings.import_status';

	public function register( Container $container ) {
		$this->settings_screen( $container );
		$this->api_credentials( $container );
		$this->api_status_indicator( $container );
		$this->cart( $container );
		$this->gift_certificates( $container );
		$this->import( $container );
		$this->currency( $container );
		$this->accounts( $container );
		$this->analytics( $container );
	}

	private function settings_screen( Container $container ) {
		$container[ self::SCREEN ] = function ( Container $container ) {
			return new Settings_Screen();
		};
		add_action( 'admin_menu', $this->create_callback( 'settings_screen_admin_menu', function () use ( $container ) {
			$container[ self::SCREEN ]->register_settings_page();
		} ), 10, 0 );
	}

	private function api_credentials( Container $container ) {
		$container[ self::API_SECTION ] = function ( Container $container ) {
			return new Api_Credentials();
		};
		add_action( 'bigcommerce/settings/register', $this->create_callback( 'api_credentials_register', function () use ( $container ) {
			$container[ self::API_SECTION ]->register_settings_section();
		} ), 15, 0 );
		add_action( 'bigcommerce/settings/render/credentials', $this->create_callback( 'api_credentials_description', function () use ( $container ) {
			$container[ self::API_SECTION ]->render_help_text();
		} ), 10, 0 );
	}

	private function api_status_indicator( Container $container ) {
		$container[ self::API_STATUS ] = function ( Container $container ) {
			return new Connection_Status( $container[ Api::FACTORY ]->catalog(), $container[ Api::CONFIG_COMPLETE ] );
		};

		add_action( 'admin_notices', $this->create_callback( 'credentials_required', function () use ( $container ) {
			$container[ self::API_STATUS ]->credentials_required_notice( $container[ self::SCREEN ] );
		} ), 10, 0 );

		$flush = $this->create_callback( 'api_status_flush', function () use ( $container ) {
			$container[ self::API_STATUS ]->flush_status_cache();
		} );

		add_action( 'bigcommerce/settings/register', $this->create_callback( 'api_status_register', function ( $hook ) use ( $container, $flush ) {
			$container[ self::API_STATUS ]->register_field();

			add_action( 'load-' . $hook, $flush, 10, 0 );
		} ), 14, 1 );

	}

	private function cart( Container $container ) {
		$container[ self::CART_SECTION ] = function ( Container $container ) {
			return new Cart_Settings( $container[ Pages::CART_PAGE ] );
		};
		add_action( 'bigcommerce/settings/register', $this->create_callback( 'cart_settings_register', function () use ( $container ) {
			$container[ self::CART_SECTION ]->register_settings_section();
		} ), 30, 0 );
	}

	private function gift_certificates( Container $container ) {
		$container[ self::GIFT_CERTIFICATE_SECTION ] = function ( Container $container ) {
			$pages = [
				$container[ Pages::GIFT_PURCHACE ],
				$container[ Pages::GIFT_BALANCE ],
			];

			return new Gift_Ceritifcate_Settings( $pages );
		};
		add_action( 'bigcommerce/settings/register', $this->create_callback( 'gift_certificate_settings_register', function () use ( $container ) {
			$container[ self::GIFT_CERTIFICATE_SECTION ]->register_settings_section();
		} ), 35, 0 );
	}

	private function import( Container $container ) {
		$container[ self::IMPORT_SECTION ] = function ( Container $container ) {
			return new Import_Settings();
		};
		add_action( 'bigcommerce/settings/register', $this->create_callback( 'import_register', function () use ( $container ) {
			$container[ self::IMPORT_SECTION ]->register_settings_section();
		} ), 20, 0 );

		$container[ self::IMPORT_NOW ] = function ( Container $container ) {
			return new Import_Now( $container[ Api::FACTORY ], $container[ self::SCREEN ] );
		};
		add_action( 'bigcommerce/settings/render/frequency', $this->create_callback( 'import_now_render', function () use ( $container ) {
			$container[ self::IMPORT_NOW ]->render_button();
		} ), 10, 0 );
		add_action( 'admin_post_' . Import_Now::ACTION, $this->create_callback( 'import_now_handle', function () use ( $container ) {
			$container[ self::IMPORT_NOW ]->handle_request();
		} ), 10, 0 );

		$container[ self::IMPORT_STATUS ] = function ( Container $container ) {
			return new Import_Status();
		};
		add_action( 'bigcommerce/settings/render/frequency', $this->create_callback( 'import_status_render', function () use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				$container[ self::IMPORT_STATUS ]->render_status();
			}
		} ), 20, 0 );
	}

	private function currency( Container $container ) {
		$container[ self::CURRENCY_SECTION ] = function ( Container $container ) {
			return new \BigCommerce\Settings\Currency();
		};
		add_action( 'bigcommerce/settings/register', $this->create_callback( 'currency_settings_register', function () use ( $container ) {
			$container[ self::CURRENCY_SECTION ]->register_settings_section();
		} ), 50, 0 );
	}

	private function accounts( Container $container ) {
		$container[ self::ACCOUNTS_SECTION ] = function ( Container $container ) {
			$pages = [
				$container[ Pages::LOGIN_PAGE ],
				$container[ Pages::REGISTRATION_PAGE ],
				$container[ Pages::ACCOUNT_PAGE ],
				$container[ Pages::ORDERS_PAGE ],
				$container[ Pages::ADDRESS_PAGE ],
			];

			return new Account_Settings( $pages );
		};

		add_action( 'bigcommerce/settings/register', $this->create_callback( 'accounts_settings_register', function () use ( $container ) {
			$container[ self::ACCOUNTS_SECTION ]->register_settings_section();
		} ), 50, 0 );
	}

	private function analytics( Container $container ) {
		$container[ self::ANALYTICS_SECTION ] = function ( Container $container ) {
			return new Analytics_Settings( $container[ Api::FACTORY ]->store() );
		};

		add_action( 'bigcommerce/settings/register', $this->create_callback( 'analytics_settings_register', function () use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->register_settings_section();
		} ), 60, 0 );

		add_action( 'update_option_' . Analytics::FACEBOOK_PIXEL, $this->create_callback( 'update_pixel_id', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->update_pixel_option( $old_value, $new_value );
		} ), 10, 2 );
	}
}