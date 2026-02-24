<?php


namespace BigCommerce\Container;

use BigCommerce\Api\Base_Client;
use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Api\ChannelsApi;
use BigCommerce\Import\Processors\Headless;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Nav_Menu\Nav_Items_Meta_Box;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Abort_Import;
use BigCommerce\Settings\Connection_Status;
use BigCommerce\Settings\Flush_Cache;
use BigCommerce\Settings\Import_Now;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Onboarding_Progress;
use BigCommerce\Settings\Screens\Abstract_Screen;
use BigCommerce\Settings\Screens\Api_Credentials_Screen;
use BigCommerce\Settings\Screens\Connect_Channel_Screen;
use BigCommerce\Settings\Screens\Create_Account_Screen;
use BigCommerce\Settings\Screens\Nav_Menu_Screen;
use BigCommerce\Settings\Screens\Onboarding_Complete_Screen;
use BigCommerce\Settings\Screens\Pending_Account_Screen;
use BigCommerce\Settings\Screens\Resources_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Settings\Screens\Store_Type_Screen;
use BigCommerce\Settings\Screens\Welcome_Screen;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Settings\Sections\Analytics as Analytics_Settings;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Cart as Cart_Settings;
use BigCommerce\Settings\Sections\Channel_Select;
use BigCommerce\Settings\Sections\Channels as Channel_Settings;
use BigCommerce\Settings\Sections\Gift_Certificates as Gift_Ceritifcate_Settings;
use BigCommerce\Settings\Sections\Import as Import_Settings;
use BigCommerce\Settings\Sections\Nav_Menu_Options;
use BigCommerce\Settings\Sections\New_Account_Section;
use BigCommerce\Settings\Sections\Next_Steps;
use BigCommerce\Settings\Sections\Onboarding_Import_Settings;
use BigCommerce\Settings\Sections\Reviews as Review_Settings;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use BigCommerce\Settings\Site_URL_Sync;
use BigCommerce\Settings\Start_Over;
use BigCommerce\Taxonomies\Channel\Channel;
use Pimple\Container;

/**
 * Provides various screens and settings for BigCommerce.
 * 
 * This class is responsible for registering settings screens, handling API credentials, 
 * and managing various onboarding steps in the BigCommerce plugin.
 */
class Settings extends Provider {
	/**
	 * Settings screen for general settings.
	 * @var string
	 */
	const SETTINGS_SCREEN = 'settings.screen.settings';

	/**
	 * Welcome screen for onboarding.
	 * @var string
	 */
	const WELCOME_SCREEN = 'settings.screen.welcome';

	/**
	 * Screen for creating an account.
	 * @var string
	 */
	const CREATE_SCREEN = 'settings.screen.create';

	/**
	 * Screen for selecting the store type.
	 * @var string
	 */
	const STORE_TYPE_SCREEN = 'settings.screen.store_type';

	/**
	 * Screen for channel settings.
	 * @var string
	 */
	const CHANNEL_SCREEN = 'settings.screen.channel';

	/**
	 * Screen for pending accounts.
	 * @var string
	 */
	const PENDING_SCREEN = 'settings.screen.pending';

	/**
	 * Screen for entering API credentials.
	 * @var string
	 */
	const CREDENTIALS_SCREEN = 'settings.screen.credentials';

	/**
	 * Screen for setting up navigation menus.
	 * @var string
	 */
	const MENU_SETUP_SCREEN = 'settings.screen.nav_menu';

	/**
	 * Screen for onboarding completion.
	 * @var string
	 */
	const COMPLETE_SCREEN = 'settings.screen.onboarding_complete';

	/**
	 * Resources screen.
	 * @var string
	 */
	const RESOURCES_SCREEN = 'settings.screen.resources';

	/**
	 * Section for API settings.
	 * @var string
	 */
	const API_SECTION = 'settings.section.api';

	/**
	 * Section for connecting an account.
	 * @var string
	 */
	const CONNECT_ACCOUNT_SECTION  = 'settings.section.connect_account';

	/**
	 * Section for cart-related settings.
	 * @var string
	 */
	const CART_SECTION             = 'settings.section.cart';
	
	/**
	 * Section for gift certificate settings.
	 * @var string
	 */
	const GIFT_CERTIFICATE_SECTION = 'settings.section.gift_certificates';

	/**
	 * Section for currency settings.
	 * @var string
	 */
	const CURRENCY_SECTION         = 'settings.section.currency';

	/**
	 * Section for data import settings.
	 * @var string
	 */
	const IMPORT_SECTION           = 'settings.section.import';

	/**
	 * Section for account-related settings.
	 * @var string
	 */
	const ACCOUNTS_SECTION         = 'settings.section.accounts';

	/**
	 * Section for analytics settings.
	 * @var string
	 */
	const ANALYTICS_SECTION        = 'settings.section.analytics';

	/**
	 * Section for managing reviews.
	 * @var string
	 */
	const REVIEWS_SECTION          = 'settings.section.reviews';

	/**
	 * Section for new account setup.
	 * @var string
	 */
	const NEW_ACCOUNT_SECTION      = 'settings.section.new_account';

	/**
	 * Section for selecting a channel.
	 * @var string
	 */
	const SELECT_CHANNEL_SECTION   = 'settings.section.select_channel';

	/**
	 * Section for configuring import settings.
	 * @var string
	 */
	const IMPORT_SETTINGS_SECTION  = 'settings.section.import_settings';

	/**
	 * Section for channel settings.
	 * @var string
	 */
	const CHANNEL_SECTION          = 'settings.section.channel';

	/**
	 * Section for diagnostics and troubleshooting.
	 * @var string
	 */
	const DIAGNOSTICS_SECTION      = 'settings.section.diagnostics';

	/**
	 * Section for navigation menu options.
	 * @var string
	 */
	const MENU_OPTIONS_SECTION     = 'settings.section.nav_menu_options';

	/**
	 * Section for next steps in the process.
	 * @var string
	 */
	const NEXT_STEPS_SECTION       = 'settings.section.next_steps';

	/**
	 * Represents the API connection status.
	 * @var string
	 */
	const API_STATUS          = 'settings.api_status';

	/**
	 * Trigger for importing data immediately.
	 * @var string
	 */
	const IMPORT_NOW          = 'settings.import_now';

	/**
	 * Status of the import process.
	 * @var string
	 */
	const IMPORT_STATUS       = 'settings.import_status';

	/**
	 * Live status of the ongoing import process.
	 * @var string
	 */
	const IMPORT_LIVE_STATUS  = 'settings.import_status_live';

	/**
	 * Action to restart the onboarding process.
	 * @var string
	 */	
	const START_OVER          = 'settings.start_over';

	/**
	 * Represents the progress of the onboarding process.
	 * @var string
	 */
	const ONBOARDING_PROGRESS = 'settings.onboarding.progress_bar';

	/**
	 * Synchronizes the site URL settings.
	 * @var string
	 */
	const SITE_URL_SYNC       = 'settings.site_url_sync';

	/**
	 * Action to abort the product import process.
	 * @var string
	 */
	const ABORT_IMPORT        = 'settings.abort_product_import';

	/**
	 * Action to clear cached data.
	 * @var string
	 */
	const FLUSH_CACHE         = 'settings.flush_cache';

	/**
	 * Indicates headless processing status.
	 * @var string
	 */
	const HEADLESS            = 'settings.headless_processing';

	/**
	 * Overall configuration status.
	 * @var string
	 */	
	const CONFIG_STATUS              = 'settings.configuration_status';

	/**
	 * Display menus configuration option.
	 * @var string
	 */
	const CONFIG_DISPLAY_MENUS       = 'settings.configuration_display_menus';

	/**
	 * Initial status for new configurations.
	 * @var string
	 */
	const STATUS_NEW                 = 0;

	/**
	 * Status indicating account connection is pending.
	 * @var string
	 */
	const STATUS_ACCOUNT_PENDING     = 10;

	/**
	 * Status indicating the API connection is established.
	 * @var string
	 */
	const STATUS_API_CONNECTED       = 20;

	/**
	 * Status indicating a channel is connected.
	 * @var string
	 */
	const STATUS_CHANNEL_CONNECTED   = 40;

	/**
	 * Status indicating a store type has been selected.
	 * @var string
	 */
	const STATUS_STORE_TYPE_SELECTED = 50;

	/**
	 * Status indicating menus have been created.
	 * @var string
	 */
	const STATUS_MENUS_CREATED       = 70;

	/**
	 * Final status indicating the process is complete.
	 * @var string
	 */
	const STATUS_COMPLETE            = 1000;

	/**
	 * Registers settings and screens in the container.
	 * 
	 * @param Container $container Dependency injection container.
	 * @return void
	 */
	public function register( Container $container ) {
		$this->settings_screen( $container );
		$this->api_credentials( $container );
		$this->api_status_indicator( $container );
		$this->cart( $container );
		$this->gift_certificates( $container );
		$this->next_steps( $container );
		$this->import( $container );
		$this->currency( $container );
		$this->accounts( $container );
		$this->analytics( $container );
		$this->reviews( $container );
		$this->onboarding( $container );
		$this->diagnostics( $container );
		$this->set_menus_default_visibility( $container );
		$this->resources( $container );
	}

	private function settings_screen( Container $container ) {
		$container[ self::SETTINGS_SCREEN ] = function ( Container $container ) {
			return new Settings_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};

		add_action( 'admin_menu', $this->create_callback( 'settings_screen_admin_menu', function () use ( $container ) {
			$container[ self::SETTINGS_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		add_filter( 'bigcommerce/settings/settings_url', $this->create_callback( 'settings_url', function ( $url ) use ( $container ) {
			return $container[ self::SETTINGS_SCREEN ]->get_url();
		} ), 10, 1 );

		add_action( 'bigcommerce/settings/after_form/page=' . Settings_Screen::NAME, $this->create_callback( 'settings_support_message', function () use ( $container ) {
			$container[ self::SETTINGS_SCREEN ]->render_support_link();
		} ), 10, 0 );

		$container[ self::CONFIG_STATUS ] = function ( Container $container ) {
			/*
			 * New -\------------------ Connected -- Channel ID -- Store Type --------------------- Complete
			 *       \                /                                       \                   /
			 *        \-- Pending --/                                           \-- Nav Setup --/
			 */
			$status   = self::STATUS_NEW;
			$store_id = get_option( Onboarding_Api::STORE_ID, '' );
			if ( $store_id ) {
				$status = self::STATUS_ACCOUNT_PENDING;
			}
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				$status = self::STATUS_API_CONNECTED;
			}

			// remaining statuses require an API connection
			if ( $status < self::STATUS_API_CONNECTED ) {
				return $status;
			}

			if ( get_option( Channel_Settings::CHANNEL_ID, false ) ) {
				$status = self::STATUS_CHANNEL_CONNECTED;
			} else {
				return $status; // remaining statuses require a channel ID
			}

			if ( get_option( Store_Type_Screen::COMPLETE_FLAG, 0 ) ) {
				$status = self::STATUS_STORE_TYPE_SELECTED;
			} else {
				return $status;
			}

			if ( get_option( Nav_Menu_Screen::COMPLETE_FLAG, 0 ) ) {
				$status = self::STATUS_MENUS_CREATED;
			} else {
				return $status;
			}

			$status = self::STATUS_COMPLETE; // no more onboarding screens to go through

			return $status;
		};

		add_action( 'admin_init', $this->create_callback( 'activation_redirect', function () use ( $container ) {
			if ( get_transient( 'bigcommerce_activation_redirect' ) ) {
				delete_transient( 'bigcommerce_activation_redirect' );
				wp_safe_redirect( $container[ self::SETTINGS_SCREEN ]->get_url(), 303 );
			}
		} ), 0, 0 );

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

		$update_options_action = $this->create_callback( 'api_credentials_update_action', function ( $new_value, $old_value ) use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				$container[ self::API_SECTION ]->do_api_settings_updated_action( $new_value, $old_value );
			}
		} );

		$pre_update_options_action = $this->create_callback( 'api_credentials_pre_update_action', function ($new_value, $old_value, $option ) use ( $container ) {
			if ( $old_value === $new_value ) {
				return $old_value;
			}

			if ( $old_value === false ) {
				return $new_value;
			}

			$config       = $container[ Api::API_CONFIG_RENEWAL ]->renewal_config( $option, $new_value );
			$client       = new Base_Client( $config );
			$channels_api = new ChannelsApi( $client );
			$catalog_api  = new CatalogApi( $client );

			try {
				$channels_api->listChannels()->getData();
				$catalog_api->catalogSummaryGet();

				return $new_value;
			} catch ( \Exception $e ) {
				add_settings_error( Api_Credentials_Screen::NAME, 'submitted', __( 'Unable to connect to the BigCommerce API. Please re-enter your credentials.', 'bigcommerce' ), 'error' );
				add_settings_error( Api_Credentials_Screen::NAME, 'submitted', $e->getMessage(), 'error' );
				$container[ Api::API_CONFIG_RENEWAL ]->renewal_config( $option, $old_value );
				set_transient( 'settings_errors', get_settings_errors(), 30 );
				return $old_value;
			}
		} );

		add_filter( 'pre_update_option_' . Api_Credentials::OPTION_STORE_URL, $pre_update_options_action, 10, 3 );
		add_filter( 'pre_update_option_' . Api_Credentials::OPTION_CLIENT_ID, $pre_update_options_action, 10, 3 );
		add_filter( 'pre_update_option_' . Api_Credentials::OPTION_CLIENT_SECRET, $pre_update_options_action, 10, 3 );
		add_filter( 'pre_update_option_' . Api_Credentials::OPTION_ACCESS_TOKEN, $pre_update_options_action, 10, 3 );
		add_action( 'update_option_' . Api_Credentials::OPTION_STORE_URL, $update_options_action, 10, 2 );
		add_action( 'update_option_' . Api_Credentials::OPTION_CLIENT_ID, $update_options_action, 10, 2 );
		add_action( 'update_option_' . Api_Credentials::OPTION_CLIENT_SECRET, $update_options_action, 10, 2 );
		add_action( 'update_option_' . Api_Credentials::OPTION_ACCESS_TOKEN, $update_options_action, 10, 2 );
	}

	private function api_status_indicator( Container $container ) {
		$container[ self::API_STATUS ] = function ( Container $container ) {
			return new Connection_Status( $container[ Api::FACTORY ]->catalog(), $container[ self::CONFIG_STATUS ] );
		};

		add_action( 'admin_notices', $this->create_callback( 'credentials_required', function () use ( $container ) {
			/**
			 * Filters settings credentials notice for excluded screens.
			 *
			 * @param array $excluded_screens
			 */
			$excluded = apply_filters( 'bigcommerce/settings/credentials_notice/excluded_screens', [
				$container[ self::WELCOME_SCREEN ]->get_hook_suffix(),
				$container[ self::CREATE_SCREEN ]->get_hook_suffix(),
				$container[ self::CHANNEL_SCREEN ]->get_hook_suffix(),
				$container[ self::STORE_TYPE_SCREEN ]->get_hook_suffix(),
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
			return new Cart_Settings( $container[ Pages::CART_PAGE ], $container[ Pages::CHECKOUT_PAGE ], $container[ Pages::CHECKOUT_COMPLETE_PAGE ] );
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

		$container[ self::HEADLESS ] = static function ( Container $container ) {
			return new Headless();
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'import_register', function () use ( $container ) {
			$container[ self::IMPORT_SECTION ]->register_settings_section();
		} ), 20, 0 );

		add_action( 'bigcommerce/settings/register/screen=' . Connect_Channel_Screen::NAME, $this->create_callback( 'import_register_for_channels', function () use ( $container ) {
			$container[ self::IMPORT_SECTION ]->register_connect_channel_fields();
		} ), 20, 0 );

		$container[ self::IMPORT_NOW ] = function ( Container $container ) {
			return new Import_Now( $container[ self::SETTINGS_SCREEN ] );
		};

		add_action( 'bigcommerce/settings/header/import_status', $this->create_callback( 'import_now_render', function () use ( $container ) {
			$container[ self::IMPORT_NOW ]->render_button();
		} ), 10, 0 );

		add_filter( 'views_edit-' . Product::NAME, $this->create_callback( 'import_now_list_table_view', function ( $views ) use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_CHANNEL_CONNECTED ) {
				$views = $container[ self::IMPORT_NOW ]->list_table_link( $views );
			}

			return $views;
		} ), 5, 1 );

		add_action( 'admin_post_' . Import_Now::ACTION, $this->create_callback( 'import_now_handle', function () use ( $container ) {
			$container[ self::IMPORT_NOW ]->handle_request();
		} ), 10, 0 );

		add_action( 'admin_notices', $this->create_callback( 'import_now_notices', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_COMPLETE ) {
				$container[ self::IMPORT_NOW ]->list_table_notice();
			}
		} ), 0, 0 );

		$container[ self::IMPORT_STATUS ] = function ( Container $container ) {
			return new Import_Status( $container[ Import::TASK_MANAGER ] );
		};

		$render_import_status = $this->create_callback( 'import_status_render', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_COMPLETE ) {
				$container[ self::IMPORT_STATUS ]->render_status();
			}
		} );

		add_action( 'bigcommerce/settings/section/after_fields/id=' . Import_Settings::NAME, $render_import_status, 20, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Onboarding_Complete_Screen::NAME, $render_import_status, 0, 0 );
		add_action( 'bigcommerce/settings/import/product_list_table_notice', $this->create_callback( 'import_current_status_notice', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_COMPLETE ) {
				$container[ self::IMPORT_STATUS ]->current_status_notice();
			}
		} ), 10, 0 );

		add_action( 'bigcommerce/import/before', $this->create_callback( 'cache_import_queue_size', function ( $status ) use ( $container ) {
			if ( in_array( $status, [ Status::MARKING_DELETED_PRODUCTS, Status::MARKED_DELETED_PRODUCTS ] ) ) {
				$container[ self::IMPORT_STATUS ]->cache_queue_size();
			}
		} ), 10, 1 );

		// Ajax actions
		add_action( 'wp_ajax_' . Import_Status::AJAX_ACTION_IMPORT_STATUS, $this->create_callback( 'validate_current_status_ajax', function () use ( $container ) {
			$container[ self::IMPORT_STATUS ]->validate_ajax_current_status_request();
		} ), 0, 0 );


		add_action( 'wp_ajax_' . Import_Status::AJAX_ACTION_IMPORT_STATUS, $this->create_callback( 'import_current_status_message', function () use ( $container ) {
			$container[ self::IMPORT_STATUS ]->ajax_current_status();
		} ), 10, 0 );

		add_action( 'update_option_' . Import_Settings::HEADLESS_FLAG, $this->create_callback( 'change_import_behaviour', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::HEADLESS ]->maybe_switch_headless( $old_value, $new_value );
		} ), 10, 2 );
	}

	private function currency( Container $container ) {
		$container[ self::CURRENCY_SECTION ] = function ( Container $container ) {
			return new \BigCommerce\Settings\Sections\Currency();
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'currency_settings_register', function () use ( $container ) {
			$container[ self::CURRENCY_SECTION ]->register_settings_section();
		} ), 50, 0 );
	}

	private function next_steps( Container $container ) {
		$container[ self::NEXT_STEPS_SECTION ] = function ( Container $container ) {
			$path = dirname( $container['plugin_file'] ) . '/templates/admin';

			return new Next_Steps( $container[ Merchant::SETUP_STATUS ], $path );
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'next_steps_settings_register', function () use ( $container ) {
			$container[ self::NEXT_STEPS_SECTION ]->register_settings_section();
		} ), 10, 0 );
	}

	private function accounts( Container $container ) {
		$container[ self::ACCOUNTS_SECTION ] = function ( Container $container ) {
			$pages = [
				$container[ Pages::LOGIN_PAGE ],
				$container[ Pages::REGISTRATION_PAGE ],
				$container[ Pages::ACCOUNT_PAGE ],
				$container[ Pages::ORDERS_PAGE ],
				$container[ Pages::ADDRESS_PAGE ],
				$container[ Pages::WISHLIST_USER ],
				$container[ Pages::SHIPPING_PAGE ],
			];

			return new Account_Settings( $pages );
		};

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'accounts_settings_register', function () use ( $container ) {
			$container[ self::ACCOUNTS_SECTION ]->register_settings_section();
		} ), 50, 0 );

		add_action( 'init', $this->create_callback( 'add_default_global_logins', function () use ( $container ) {
			$container[ self::ACCOUNTS_SECTION ]->add_default_global_logins();
		} ) );

		add_action( 'update_option_' . Account_Settings::ALLOW_GLOBAL_LOGINS, $this->create_callback( 'update_allow_global_logins', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::ACCOUNTS_SECTION ]->maybe_sync_global_logins( $old_value, $new_value );
		} ), 10, 2 );
	}

	private function analytics( Container $container ) {
		$container[ self::ANALYTICS_SECTION ] = function ( Container $container ) {
			return new Analytics_Settings( $container[ Api::FACTORY ]->store(), $container[ Api::FACTORY ]->storefront_settings() );
		};
	
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'analytics_settings_register', function () use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->register_settings_section();
		} ), 60, 0 );
	
		add_action( 'update_option_' . Analytics_Settings::FACEBOOK_PIXEL, $this->create_callback( 'update_pixel_id', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->update_pixel_option( $old_value, $new_value );
		} ), 10, 2 );
	
		add_action( 'update_option_' . Analytics_Settings::GOOGLE_ANALYTICS, $this->create_callback( 'update_google_option', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::ANALYTICS_SECTION ]->update_google_option( $old_value, $new_value );
		} ), 10, 2 );
	}
	

	private function reviews( Container $container ) {
		$container[ self::REVIEWS_SECTION ] = function ( Container $container ) {
			return new Review_Settings();
		};
	
		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'review_settings_register', function () use ( $container ) {
			$container[ self::REVIEWS_SECTION ]->register_settings_section();
		} ), 60, 0 );
	}
	

	private function onboarding( Container $container ) {
		$container[ self::WELCOME_SCREEN ] = function ( Container $container ) {
			$path = dirname( $container['plugin_file'] ) . '/templates/admin';
	
			return new Welcome_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ], $path );
		};

		add_action( 'admin_menu', $this->create_callback( 'welcome_screen_admin_menu', function () use ( $container ) {
			$container[ self::WELCOME_SCREEN ]->register_settings_page();
		} ), 10, 0 );
	
		$welcome_screen_url = $this->create_callback( 'welcome_screen_url', function ( $url ) use ( $container ) {
			return $container[ self::WELCOME_SCREEN ]->get_url();
		} );

		add_filter( 'bigcommerce/onboarding/error_redirect', $welcome_screen_url, 10, 1 );
		add_filter( 'bigcommerce/onboarding/reset', $welcome_screen_url, 10, 1 );
	
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

		add_action( 'bigcommerce/settings/register/screen=' . Create_Account_Screen::NAME, $this->create_callback( 'new_account_action_register', function () use ( $container ) {
			$container[ self::NEW_ACCOUNT_SECTION ]->register_settings_section();
		} ), 50, 0 );

		add_action( 'bigcommerce/create_account/validate_request', $this->create_callback( 'new_account_validate', function ( $submission, $errors ) use ( $container ) {
			$container[ self::NEW_ACCOUNT_SECTION ]->validate_request( $submission, $errors );
		} ), 10, 2 );

		add_action( 'admin_menu', $this->create_callback( 'create_choose_blog_full_store_admin_menu', function () use ( $container ) {
			$container[ self::STORE_TYPE_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		add_action( 'admin_post_' . Store_Type_Screen::ACTION_BLOG, $this->create_callback( 'handle_choose_blog_request', function () use ( $container ) {
			$container[ self::STORE_TYPE_SCREEN ]->handle_submission_for_blog();
		} ), 10, 1 );

		add_action( 'admin_post_' . Store_Type_Screen::ACTION_FULL_STORE, $this->create_callback( 'handle_choose_full_store_request', function () use ( $container ) {
			$container[ self::STORE_TYPE_SCREEN ]->handle_submission_for_full_store();
		} ), 10, 1 );

		add_action( 'admin_menu', $this->create_callback( 'create_channel_screen_admin_menu', function () use ( $container ) {
			$container[ self::CHANNEL_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		add_action( 'bigcommerce/settings/register/screen=' . Connect_Channel_Screen::NAME, $this->create_callback( 'select_channel_section_register', function () use ( $container ) {
			$container[ self::SELECT_CHANNEL_SECTION ]->register_settings_section();
			$container[ self::IMPORT_SETTINGS_SECTION ]->register_settings_section();
		} ), 10, 0 );

		add_action( 'bigcommerce/settings/register/screen=' . Settings_Screen::NAME, $this->create_callback( 'channel_section_register', function () use ( $container ) {
			$container[ self::CHANNEL_SECTION ]->register_settings_section();
		} ), 80, 0 );

		add_action( 'admin_post_' . Channel_Settings::POST_ACTION, $this->create_callback( 'handle_channel_operation', function () use ( $container ) {
			if ( Channel::multichannel_enabled() ) {
				$container[ self::CHANNEL_SECTION ]->handle_action_submission( $container[ self::SETTINGS_SCREEN ]->get_url() . '#' . Channel_Settings::NAME );
			}
		} ), 10, 0 );

		add_action( 'bigcommerce/channel/promote', $this->create_callback( 'promote_channel', function ( \WP_Term $term ) use ( $container ) {
			$container[ self::CHANNEL_SECTION ]->promote_channel( $term );
		} ), 10, 1 );

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

		/**
		 * Provides the URL for the API credentials page.
		 *
		 * @param string $url The existing URL.
		 * @return string The URL for the API credentials page.
		 */
		$api_credentials_url = $this->create_callback( 'api_credentials_url', function ( $url ) use ( $container ) {
			return $container[ self::CREDENTIALS_SCREEN ]->get_url();
		} );
		add_filter( 'bigcommerce/settings/credentials_url', $api_credentials_url );

		add_action( 'admin_action_update', $this->create_callback( 'validate_api_credentials', function () use ( $container ) {
			$container[ self::CREDENTIALS_SCREEN ]->validate_credentials();
		} ), 10, 0 );

		/**
		 * Registers the Nav Menu setup screen in the admin menu.
		 *
		 * @param Container $container The container instance holding the service dependencies.
		 * @return void
		 */
		$container[ self::MENU_SETUP_SCREEN ] = function ( Container $container ) {
			return new Nav_Menu_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'nav_menu_screen_admin_menu', function () use ( $container ) {
			$container[ self::MENU_SETUP_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		/**
		 * Registers the Complete Onboarding screen in the admin menu.
		 *
		 * @param Container $container The container instance holding the service dependencies.
		 * @return void
		 */
		$container[ self::COMPLETE_SCREEN ] = function ( Container $container ) {
			$path = dirname( $container['plugin_file'] ) . '/templates/admin';

			return new Onboarding_Complete_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ], $path, $container[ Merchant::SETUP_STATUS ] );
		};
		add_action( 'admin_menu', $this->create_callback( 'complete_screen_admin_menu', function () use ( $container ) {
			$container[ self::COMPLETE_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		/**
		 * Registers the Nav Menu options section for settings.
		 *
		 * @return void
		 */
		$container[ self::MENU_OPTIONS_SECTION ] = function ( Container $container ) {
			return new Nav_Menu_Options();
		};
		add_action( 'bigcommerce/settings/register/screen=' . Nav_Menu_Screen::NAME, $this->create_callback( 'menu_options_section_register', function () use ( $container ) {
			$container[ self::MENU_OPTIONS_SECTION ]->register_settings_section();
		} ), 10, 0 );

		add_action( 'admin_post_' . Nav_Menu_Screen::NAME, $this->create_callback( 'handle_setup_nav_menu', function () use ( $container ) {
			$container[ self::MENU_SETUP_SCREEN ]->handle_submission();
		} ), 10, 0 );

		add_action( 'bigcommerce/settings/unregistered_screen', $this->create_callback( 'redirect_unregistered_screen', function ( $unregistered_screen ) use ( $container ) {
			/** @var Abstract_Screen[] $possible_screens */
			$possible_screens = [
				$container[ self::MENU_SETUP_SCREEN ],
				$container[ self::COMPLETE_SCREEN ],
				$container[ self::SETTINGS_SCREEN ],
				$container[ self::WELCOME_SCREEN ],
				$container[ self::STORE_TYPE_SCREEN ],
				$container[ self::CHANNEL_SCREEN ],
				$container[ self::PENDING_SCREEN ],
			];
			foreach ( $possible_screens as $screen ) {
				if ( $screen->should_register() ) {
					$screen->redirect_to_screen();
				}
			}
		} ), 10, 1 );

		
		$container[ self::START_OVER ] = function ( Container $container ) {
			return new Start_Over();
		};

		$start_over_link = $this->create_callback( 'start_over_link', function () use ( $container ) {
			$container[ self::START_OVER ]->add_link_to_settings_screen();
		} );
	
		add_action( 'bigcommerce/settings/after_form/page=' . Api_Credentials_Screen::NAME, $start_over_link );
		add_action( 'bigcommerce/settings/after_form/page=' . Create_Account_Screen::NAME, $start_over_link );
		add_action( 'bigcommerce/settings/after_form/page=' . Connect_Channel_Screen::NAME, $start_over_link );
		add_action( 'bigcommerce/settings/after_form/page=' . Store_Type_Screen::NAME, $start_over_link );
		add_action( 'bigcommerce/settings/after_content/page=' . Pending_Account_Screen::NAME, $start_over_link );

		add_action( 'admin_post_' . Start_Over::ACTION, function () use ( $container ) {
			$container[ self::START_OVER ]->reset_credentials();
		}, 10, 0 );

		$this->onboarding_progress_bar( $container );
	}

	private function onboarding_progress_bar( Container $container ) {
		$container[ self::ONBOARDING_PROGRESS ] = function ( Container $container ) {
			$path = dirname( $container['plugin_file'] ) . '/templates/admin';

			return new Onboarding_Progress( $container[ self::CONFIG_STATUS ], $path );
		};

		$progress_bar = $this->create_callback( 'onboarding_progress', function () use ( $container ) {
			if ( $container[ self::CONFIG_STATUS ] >= self::STATUS_COMPLETE ) {
				$container[ self::ONBOARDING_PROGRESS ]->render();
			}
		} );

		add_action( 'bigcommerce/settings/onboarding/progress', $progress_bar, 10, 0 );

		$subheader = $this->create_callback( 'onboarding_subheader', function () use ( $container ) {
			$container[ self::ONBOARDING_PROGRESS ]->step_subheader();
		} );

		add_action( 'bigcommerce/settings/before_title/page=' . Welcome_Screen::NAME, $subheader, 10, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Create_Account_Screen::NAME, $subheader, 10, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Api_Credentials_Screen::NAME, $subheader, 10, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Pending_Account_Screen::NAME, $subheader, 10, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Connect_Channel_Screen::NAME, $subheader, 10, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Store_Type_Screen::NAME, $subheader, 10, 0 );
		add_action( 'bigcommerce/settings/before_title/page=' . Nav_Menu_Screen::NAME, $subheader, 10, 0 );
	}

	/**
	 * Handles menus visibility for the settings screen and nav menu page
	 *
	 * @param Container $container
	 */
	private function set_menus_default_visibility( Container $container ) {
		$container[ self::CONFIG_DISPLAY_MENUS ] = function ( Container $container ) {
			return new Nav_Items_Meta_Box();
		};

		add_action( 'load-nav-menus.php', $this->create_callback( 'display_nav_menus_by_default', function () use ( $container ) {
			$container[ self::CONFIG_DISPLAY_MENUS ]->set_nav_menu_screen_options();
		} ) );
	}

	/**
	 * @param Container $container
	 */
	private function diagnostics( Container $container ) {
		$container[ self::DIAGNOSTICS_SECTION ] = function ( Container $container ) {
			$plugin_path = plugin_dir_path( $container['plugin_file'] );
			return new Troubleshooting_Diagnostics( $plugin_path );
		};

		$container[ self::SITE_URL_SYNC ] = function ( Container $container ) {
			return new Site_URL_Sync( $container[ Taxonomies::ROUTES ] , $container[ self::SETTINGS_SCREEN ] );
		};

		$container[ self::ABORT_IMPORT ] = function ( Container $container ) {
			return new Abort_Import( $container[ self::SETTINGS_SCREEN ] );
		};

		$container[ self::FLUSH_CACHE ] = function ( Container $container ) {
			return new Flush_Cache( $container[ self::SETTINGS_SCREEN ] );
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

		add_action( 'admin_post_' . Troubleshooting_Diagnostics::SYNC_SITE_URL, $this->create_callback( 'diagnostics_settings_sync_site_url_action', function () use ( $container ) {
			$container[ self::SITE_URL_SYNC ]->sync();
		} ), 10, 0 );

		add_action( 'admin_post_' . Troubleshooting_Diagnostics::ABORT_NAME, $this->create_callback( 'diagnostics_settings_abort_import_action', function () use ( $container ) {
			$container[ self::ABORT_IMPORT ]->abort( $container['import.cleanup'] );
		} ), 10, 0 );

		$flush_cache = $this->create_callback( 'diagnostics_settings_handle_cache_flush', function () use ( $container ) {
			$container[ self::FLUSH_CACHE ]->handle_request();
		} );

		add_action( 'admin_post_' . Troubleshooting_Diagnostics::FLUSH_USER, $flush_cache, 10, 0 );
		add_action( 'admin_post_' . Troubleshooting_Diagnostics::FLUSH_PRODUCTS, $flush_cache, 10, 0 );
	}

	private function resources( Container $container ) {
		$container[ self::RESOURCES_SCREEN ] = function ( Container $container ) {
			$path = dirname( $container['plugin_file'] ) . '/templates/admin';

			return new Resources_Screen( $container[ self::CONFIG_STATUS ], $container[ Assets::PATH ], $path );
		};

		add_action( 'admin_menu', $this->create_callback( 'resources_screen_register', function () use ( $container ) {
			$container[ self::RESOURCES_SCREEN ]->register_settings_page();
		} ), 10, 0 );

		add_filter( 'bigcommerce/settings/resources_url', $this->create_callback( 'resources_url', function ( $url ) use ( $container ) {
			return $container[ self::RESOURCES_SCREEN ]->get_url();
		} ), 10, 1 );
	}
}
