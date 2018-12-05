<?php


namespace BigCommerce\Container;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Settings\Connection_Status;
use BigCommerce\Settings\Import_Now;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Screens\Abstract_Screen;
use BigCommerce\Settings\Screens\Api_Credentials_Screen;
use BigCommerce\Settings\Screens\Connect_Channel_Screen;
use BigCommerce\Settings\Screens\Create_Account_Screen;
use BigCommerce\Settings\Screens\Pending_Account_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Settings\Screens\Welcome_Screen;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Settings\Sections\Analytics as Analytics_Settings;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Channel_Select;
use BigCommerce\Settings\Sections\Channels as Channel_Settings;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;
use BigCommerce\Settings\Sections\Gift_Certificates as Gift_Ceritifcate_Settings;
use BigCommerce\Settings\Sections\Import as Import_Settings;
use BigCommerce\Settings\Sections\New_Account_Section;
use BigCommerce\Settings\Sections\Reviews;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use Pimple\Container;
use \BigCommerce\Container\Log;

class Settings extends Provider {
	const SETTINGS_SCREEN    = 'settings.screen.settings';
	const WELCOME_SCREEN     = 'settings.screen.welcome';
	const CREATE_SCREEN      = 'settings.screen.create';
	const CHANNEL_SCREEN     = 'settings.screen.channel';
	const PENDING_SCREEN     = 'settings.screen.pending';
	const CREDENTIALS_SCREEN = 'settings.screen.credentials';

	const API_SECTION              = 'settings.section.api';
	const CONNECT_ACCOUNT_SECTION  = 'settings.section.connect_account';
	const CART_SECTION             = 'settings.section.cart';
	const GIFT_CERTIFICATE_SECTION = 'settings.section.gift_certificates';
	const CURRENCY_SECTION         = 'settings.section.currency';
	const IMPORT_SECTION           = 'settings.section.import';
	const ACCOUNTS_SECTION         = 'settings.section.accounts';
	const ANALYTICS_SECTION        = 'settings.section.analytics';
	const REVIEWS_SECTION          = 'settings.section.reviews';
	const NEW_ACCOUNT_SECTION      = 'settings.section.new_account';
	const SELECT_CHANNEL_SECTION   = 'settings.section.select_channel';
	const CHANNEL_SECTION          = 'settings.section.channel';
	const DIAGNOSTICS_SECTION      = 'settings.section.diagnostics';

	const API_STATUS         = 'settings.api_status';
	const IMPORT_NOW         = 'settings.import_now';
	const IMPORT_STATUS      = 'settings.import_status';
	const IMPORT_LIVE_STATUS = 'settings.import_status_live';

	const CONFIG_STATUS            = 'settings.configuration_status';
	const STATUS_NEW               = 0;
	const STATUS_ACCOUNT_PENDING   = 10;
	const STATUS_API_CONNECTED     = 20;
	const STATUS_CHANNEL_CONNECTED = 40;

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
		$this->reviews( $container );
		$this->onboarding( $container );
		$this->diagnostics( $container );
	}

	private function settings_screen( Container $container ) {
		$container[ self::SETTINGS_SCREEN ] = function ( Container $container ) {
			return new Settings_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'settings_screen_admin_menu', function () use ( $container ) {
			$container[ self::SETTINGS_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		add_action( 'bigcommerce/settings/after_form/page=' . Settings_Screen::NAME, $this->create_callback( 'settings_support_message', function () use ( $container ) {
			$container[ self::SETTINGS_SCREEN ]->render_support_link();
		} ), 10, 0 );

		$container[ self::CONFIG_STATUS ] = function ( Container $container ) {
			if ( ! $container[ Api::CONFIG_COMPLETE ] ) {
				$store_id = get_option( Onboarding_Api::STORE_ID, '' );

				if ( empty( $store_id ) ) {
					return self::STATUS_NEW;
				}

				return self::STATUS_ACCOUNT_PENDING;
			}
			if ( ! get_option( Channel_Settings::CHANNEL_ID, false ) ) {
				return self::STATUS_API_CONNECTED;
			}

			return self::STATUS_CHANNEL_CONNECTED;
		};
	}

	private function api_credentials( Container $container ) {
		$container[ self::API_SECTION ] = function ( Container $container ) {
			return new Api_Credentials();
		};
		$register_callback              = $this->create_callback( 'api_credentials_register', function ( $suffix, $screen ) use ( $container ) {
			$container[ self::API_SECTION ]->register_settings_section( $suffix, $screen );
		} );
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $register_callback, 70, 2 );
		add_action( 'bigcommerce/settings/register/screen=' . Api_Credentials_Screen::NAME, $register_callback, 10, 2 );
		add_action( 'bigcommerce/settings/render/credentials', $this->create_callback( 'api_credentials_description', function () use ( $container ) {
			$container[ self::API_SECTION ]->render_help_text();
		} ), 10, 0 );
		$env_filter = $this->create_callback( 'api_credentials_env_override', function ( $value, $option, $default ) use ( $container ) {
			return $container[ self::API_SECTION ]->filter_option_with_env( $value, $option, $default );
		} );
		add_filter( 'pre_option_' . Api_Credentials::OPTION_STORE_URL, $env_filter, 10, 3 );
		add_filter( 'pre_option_' . Api_Credentials::OPTION_CLIENT_ID, $env_filter, 10, 3 );
		add_filter( 'pre_option_' . Api_Credentials::OPTION_CLIENT_SECRET, $env_filter, 10, 3 );
		add_filter( 'pre_option_' . Api_Credentials::OPTION_ACCESS_TOKEN, $env_filter, 10, 3 );
	}

	private function api_status_indicator( Container $container ) {
		$container[ self::API_STATUS ] = function ( Container $container ) {
			return new Connection_Status( $container[ Api::FACTORY ]->catalog(), $container[ self::CONFIG_STATUS ] );
		};

		add_action( 'admin_notices', $this->create_callback( 'credentials_required', function () use ( $container ) {
			$excluded = apply_filters( 'bigcommerce/settings/credentials_notice/excluded_screens', [
				$container[ self::WELCOME_SCREEN ]->get_hook_suffix(),
				$container[ self::CREATE_SCREEN ]->get_hook_suffix(),
				$container[ self::CHANNEL_SCREEN ]->get_hook_suffix(),
				$container[ self::PENDING_SCREEN ]->get_hook_suffix(),
				$container[ self::CREDENTIALS_SCREEN ]->get_hook_suffix(),
			] );
			$container[ self::API_STATUS ]->credentials_required_notice( $container[ self::WELCOME_SCREEN ], $excluded );
		} ), 10, 0 );

		$flush = $this->create_callback( 'api_status_flush', function () use ( $container ) {
			$container[ self::API_STATUS ]->flush_status_cache();
		} );

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'api_status_register', function ( $hook ) use ( $container, $flush ) {
			$container[ self::API_STATUS ]->register_field();

			add_action( 'load-' . $hook, $flush, 10, 0 );
		} ), 14, 1 );

	}

	private function cart( Container $container ) {
		$container[ self::CART_SECTION ] = function ( Container $container ) {
			return new Cart_Settings( $container[ Pages::CART_PAGE ], $container[ Pages::CHECKOUT_PAGE ] );
		};
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'cart_settings_register', function () use ( $container ) {
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
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'gift_certificate_settings_register', function () use ( $container ) {
			$container[ self::GIFT_CERTIFICATE_SECTION ]->register_settings_section();
		} ), 35, 0 );
	}

	private function import( Container $container ) {
		$container[ self::IMPORT_SECTION ] = function ( Container $container ) {
			return new Import_Settings();
		};

		$container[ self::IMPORT_LIVE_STATUS ] = function ( Container $container ) {
			return new Cron_Runner();
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'import_register', function () use ( $container ) {
			$container[ self::IMPORT_SECTION ]->register_settings_section();
		} ), 20, 0 );

		$container[ self::IMPORT_NOW ] = function ( Container $container ) {
			return new Import_Now( $container[ self::SETTINGS_SCREEN ] );
		};

		add_action( 'bigcommerce/settings/header/import_status', $this->create_callback( 'import_now_render', function () use ( $container ) {
			$container[ self::IMPORT_NOW ]->render_button();
		} ), 10, 0 );

		add_action( 'admin_post_' . Import_Now::ACTION, $this->create_callback( 'import_now_handle', function () use ( $container ) {
			$container[ self::IMPORT_NOW ]->handle_request();
		} ), 10, 0 );

		add_action( 'admin_notices', $this->create_callback( 'import_now_notices', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::IMPORT_NOW ]->list_table_notice();
			}
		} ), 0, 0 );

		$container[ self::IMPORT_STATUS ] = function ( Container $container ) {
			return new Import_Status();
		};

		add_action( 'bigcommerce/settings/section/after_fields/id=' . Import_Settings::NAME, $this->create_callback( 'import_status_render', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::IMPORT_STATUS ]->render_status();
			}
		} ), 20, 0 );

		add_action( 'bigcommerce/settings/import/product_list_table_notice', $this->create_callback( 'import_current_status_notice', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_CHANNEL_CONNECTED ) {
				$container[ self::IMPORT_STATUS ]->current_status_notice();
			}
		} ), 10, 0 );

		add_action( 'bigcommerce/import/before', $this->create_callback( 'cache_import_queue_size', function ( $status ) use ( $container ) {
			if ( in_array( $status, [ Status::MARKING_DELETED_PRODUCTS, Status::MARKED_DELETED_PRODUCTS ] ) ) {
				$container[ self::IMPORT_STATUS ]->cache_queue_size();
			}
		} ), 10, 1 );

		// Ajax actions
		add_action( 'wp_ajax_' . Import_Status::AJAX_ACTION_IMPORT_STATUS, $this->create_callback( 'import_current_status_message', function () use ( $container ) {
			$container[ self::IMPORT_STATUS ]->ajax_current_status();
		} ), 10, 0 );
	}

	private function currency( Container $container ) {
		$container[ self::CURRENCY_SECTION ] = function ( Container $container ) {
			return new \BigCommerce\Settings\Sections\Currency();
		};
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'currency_settings_register', function () use ( $container ) {
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
				$container[ Pages::SHIPPING_PAGE ],
			];

			return new Account_Settings( $pages );
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'accounts_settings_register', function () use ( $container ) {
			$container[ self::ACCOUNTS_SECTION ]->register_settings_section();
		} ), 50, 0 );
	}

	private function analytics( Container $container ) {
		$container[ self::ANALYTICS_SECTION ] = function ( Container $container ) {
			return new Analytics_Settings( $container[ Api::FACTORY ]->store() );
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'analytics_settings_register', function () use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->register_settings_section();
		} ), 60, 0 );

		add_action( 'update_option_' . Analytics::FACEBOOK_PIXEL, $this->create_callback( 'update_pixel_id', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->update_pixel_option( $old_value, $new_value );
		} ), 10, 2 );
	}

	private function reviews( Container $container ) {
		$container[ self::REVIEWS_SECTION ] = function ( Container $container ) {
			return new Reviews();
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'review_settings_register', function () use ( $container ) {
			$container[ self::REVIEWS_SECTION ]->register_settings_section();
		} ), 60, 0 );
	}

	private function onboarding( Container $container ) {
		$container[ self::WELCOME_SCREEN ] = function ( Container $container ) {
			$path = dirname( $container[ 'plugin_file' ] ) . '/templates/admin';

			return new Welcome_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ], $path );
		};
		add_action( 'admin_menu', $this->create_callback( 'welcome_screen_admin_menu', function () use ( $container ) {
			$container[ self::WELCOME_SCREEN ]->register_settings_page();
		} ), 10, 0 );
		$welcome_screen_url = $this->create_callback( 'welcome_screen_url', function ( $url ) use ( $container ) {
			return $container[ self::WELCOME_SCREEN ]->get_url();
		} );
		add_filter( 'bigcommerce/onboarding/error_redirect', $welcome_screen_url, 10, 1 );

		$container[ self::CREATE_SCREEN ] = function ( Container $container ) {
			return new Create_Account_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'create_screen_admin_menu', function () use ( $container ) {
			$container[ self::CREATE_SCREEN ]->register_settings_page();
		} ), 10, 0 );
		add_filter( 'bigcommerce/settings/create_account_url', $this->create_callback( 'create_account_url', function ( $url ) use ( $container ) {
			return $container[ self::CREATE_SCREEN ]->get_url();
		} ), 10, 1 );
		add_action( 'admin_post_' . Create_Account_Screen::NAME, $this->create_callback( 'handle_create_account', function () use ( $container ) {
			$container[ self::CREATE_SCREEN ]->handle_submission();
		} ), 10, 1 );

		$container[ self::NEW_ACCOUNT_SECTION ] = function ( Container $container ) {
			return new New_Account_Section();
		};
		add_action( 'bigcommerce/settings/register/screen=' . Create_Account_Screen::NAME, $this->create_callback( 'new_account_action_register', function () use ( $container ) {
			$container[ self::NEW_ACCOUNT_SECTION ]->register_settings_section();
		} ), 50, 0 );
		add_action( 'bigcommerce/create_account/validate_request', $this->create_callback( 'new_account_validate', function ( $submission, $errors ) use ( $container ) {
			$container[ self::NEW_ACCOUNT_SECTION ]->validate_request( $submission, $errors );
		} ), 10, 2 );

		$container[ self::CHANNEL_SCREEN ] = function ( Container $container ) {
			return new Connect_Channel_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'create_channel_screen_admin_menu', function () use ( $container ) {
			$container[ self::CHANNEL_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		$container [ self::SELECT_CHANNEL_SECTION ] = function ( Container $container ) {
			return new Channel_Select( $container[ Api::FACTORY ]->channels() );
		};
		add_action( 'bigcommerce/settings/register/screen=' . Connect_Channel_Screen::NAME, $this->create_callback( 'select_channel_section_register', function () use ( $container ) {
			$container[ self::SELECT_CHANNEL_SECTION ]->register_settings_section();
		} ), 10, 0 );

		$container [ self::CHANNEL_SECTION ] = function ( Container $container ) {
			return new Channel_Settings();
		};
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'channel_section_register', function () use ( $container ) {
			$container[ self::CHANNEL_SECTION ]->register_settings_section();
		} ), 80, 0 );

		$container[ self::PENDING_SCREEN ] = function ( Container $container ) {
			return new Pending_Account_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'pending_screen_admin_menu', function () use ( $container ) {
			$container[ self::PENDING_SCREEN ]->register_settings_page();
		} ), 10, 0 );
		$pending_screen_url = $this->create_callback( 'pending_screen_url', function ( $url ) use ( $container ) {
			return $container[ self::PENDING_SCREEN ]->get_url();
		} );
		add_filter( 'bigcommerce/onboarding/success_redirect', $pending_screen_url, 10, 1 );

		$container[ self::CREDENTIALS_SCREEN ] = function ( Container $container ) {
			return new Api_Credentials_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'credentials_screen_admin_menu', function () use ( $container ) {
			$container[ self::CREDENTIALS_SCREEN ]->register_settings_page();
		} ), 10, 0 );
		$api_credentials_url = $this->create_callback( 'api_credentials_url', function ( $url ) use ( $container ) {
			return $container[ self::CREDENTIALS_SCREEN ]->get_url();
		} );
		add_filter( 'bigcommerce/settings/credentials_url', $api_credentials_url );
		add_action( 'admin_action_update', $this->create_callback( 'validate_api_credentials', function () use ( $container ) {
			$container[ self::CREDENTIALS_SCREEN ]->validate_credentials();
		} ), 10, 0 );

		add_action( 'bigcommerce/settings/unregistered_screen', $this->create_callback( 'redirect_unregistered_screen', function ( $screen ) use ( $container ) {
			/** @var Abstract_Screen[] $possible_screens */
			$possible_screens = [
				$container[ self::SETTINGS_SCREEN ],
				$container[ self::WELCOME_SCREEN ],
				$container[ self::CHANNEL_SCREEN ],
				$container[ self::PENDING_SCREEN ],
			];
			foreach ( $possible_screens as $screen ) {
				if ( $screen->should_register() ) {
					$screen->redirect_to_screen();
				}
			}
		} ), 10, 1 );
	}

	/**
	 * @param Container $container
	 */
	private function diagnostics( Container $container ) {
		$container[ self::DIAGNOSTICS_SECTION ] = function ( Container $container ) {
			return new Troubleshooting_Diagnostics();
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'diagnostics_settings_register', function () use ( $container ) {
			$container[ self::DIAGNOSTICS_SECTION ]->register_settings_section();
		} ), 90, 0 );

		add_action( 'wp_ajax_' . Troubleshooting_Diagnostics::AJAX_ACTION, $this->create_callback( 'diagnostics_settings_action', function () use ( $container ) {
			$container[ self::DIAGNOSTICS_SECTION ]->get_diagnostics_data();
		} ), 10, 0 );

		add_action( 'wp_ajax_' . Troubleshooting_Diagnostics::AJAX_ACTION_IMPORT_ERRORS, $this->create_callback( 'diagnostics_settings_import_errors_action', function () use ( $container ) {
			$container[ self::DIAGNOSTICS_SECTION ]->get_import_errors( $container[ Log::LOGGER ] );
		} ), 10, 0 );

	}
}
