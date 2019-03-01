<?php


namespace BigCommerce\Container;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Merchant\Channel_Connector;
use BigCommerce\Merchant\Connect_Account;
use BigCommerce\Merchant\Create_Account;
use BigCommerce\Merchant\Account_Status;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Merchant\Routes;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Pages\Shipping_Returns_Page;
use BigCommerce\Settings\Screens\Pending_Account_Screen;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Channels as Channel_Settings;
use Pimple\Container;

class Merchant extends Provider {
	const CHANNEL_CONNECTOR = 'merchant.channels.connector';
	const MIDDLEMAN_URL     = 'merchant.middleman.url';
	const ONBOARDING_API    = 'merchant.onboarding.api';
	const CREATE_ACCOUNT    = 'merchant.onboarding.create_account';
	const CONNECT_ACCOUNT   = 'merchant.onboarding.connect_account';
	const ACCOUNT_STATUS    = 'merchant.onboarding.account_status';
	const ROUTES            = 'merchant.routes';

	public function register( Container $container ) {
		$this->account_onboarding( $container );
		$this->channels( $container );
		$this->routes( $container );
	}

	private function account_onboarding( Container $container ) {
		$container[ self::MIDDLEMAN_URL ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/oauth_connector/url', 'https://wp-login.bigcommerce.com/v1' );
		};

		$container[ self::ONBOARDING_API ] = function ( Container $container ) {
			return new Onboarding_Api( $container[ self::MIDDLEMAN_URL ] );
		};

		$this->create_account( $container );
		$this->connect_account( $container );
		$this->account_status( $container );
	}

	private function create_account( Container $container ) {
		$container[ self::CREATE_ACCOUNT ] = function ( Container $container ) {
			return new Create_Account( $container[ self::ONBOARDING_API ] );
		};

		add_action( 'bigcommerce/create_account/submit_request', $this->create_callback( 'request_account', function ( $data, $errors ) use ( $container ) {
			$container[ self::CREATE_ACCOUNT ]->request_account( $data, $errors );
		} ), 10, 2 );
	}

	private function connect_account( Container $container ) {
		$container[ self::CONNECT_ACCOUNT ] = function ( Container $container ) {
			return new Connect_Account( $container[ self::ONBOARDING_API ] );
		};

		add_filter( 'bigcommerce/settings/connect_account_url', $this->create_callback( 'connect_account_url', function ( $url ) use ( $container ) {
			return $container[ self::CONNECT_ACCOUNT ]->connect_account_url( $url );
		} ), 10, 1 );

		add_action( 'admin_post_' . Connect_Account::CONNECT_ACTION, $this->create_callback( 'connect_account_handler', function () use ( $container ) {
			$container[ self::CONNECT_ACCOUNT ]->connect_account();
		} ), 10, 0 );
	}

	private function account_status( Container $container ) {
		$container[ self::ACCOUNT_STATUS ] = function ( Container $container ) {
			return new Account_Status( $container[ self::ONBOARDING_API ] );
		};

		add_action( 'bigcommerce/settings/after_content/page=' . Pending_Account_Screen::NAME, $this->create_callback( 'account_status_placeholder', function () use ( $container ) {
			$container[ self::ACCOUNT_STATUS ]->render_status_placeholder();
		} ), 10, 0 );
		add_action( 'wp_ajax_' . Account_Status::STATUS_AJAX, $this->create_callback( 'ajax_account_status', function () use ( $container ) {
			$container[ self::ACCOUNT_STATUS ]->handle_account_status_request();
		} ), 10, 0 );
		add_action( 'bigcommerce/pending_account/check_status', $this->create_callback( 'pending_check_status', function ( $errors ) use ( $container ) {
			$container[ self::ACCOUNT_STATUS ]->handle_refresh_status_request( $errors );
		} ), 10, 1 );
	}

	private function channels( Container $container ) {
		$container[ self::CHANNEL_CONNECTOR ] = function ( Container $container ) {
			return new Channel_Connector( $container[ Api::FACTORY ]->channels() );
		};

		add_filter( 'sanitize_option_' . Channel_Settings::CHANNEL_ID, $this->create_callback( 'save_channel_id_handler', function ( $value ) use ( $container ) {
			return $container[ self::CHANNEL_CONNECTOR ]->handle_connect_request( $value );
		} ), 100, 1 );
		add_filter( 'sanitize_option_' . Channel_Settings::CHANNEL_NAME, $this->create_callback( 'save_channel_name_handler', function ( $value ) use ( $container ) {
			return $container[ self::CHANNEL_CONNECTOR ]->handle_rename_request( $value );
		} ), 100, 1 );

		$after_save_channel_id = $this->create_callback( 'after_save_channel_id', function ( $old_value, $new_value ) use ( $container ) {
			$container[ self::CHANNEL_CONNECTOR ]->handle_channel_updated( $old_value, $new_value );
		} );
		add_action( 'add_option_' . Channel_Settings::CHANNEL_ID, $after_save_channel_id, 10, 2 );
		add_action( 'update_option_' . Channel_Settings::CHANNEL_ID, $after_save_channel_id, 10, 2 );

		add_filter( 'bigcommerce/settings/api/disabled/field=' . Api_Credentials::OPTION_STORE_URL, $this->create_callback( 'prevent_store_url_changes', function ( $disabled ) use ( $container ) {
			return $container[ self::CHANNEL_CONNECTOR ]->prevent_store_url_changes( $disabled );
		} ), 10, 1 );
	}


	private function routes( Container $container ) {
		$container[ self::ROUTES ] = function ( Container $container ) {
			return new Routes( $container[ Api::FACTORY ]->sites() );
		};
		add_action( 'bigcommerce/channel/updated_channel_id', $this->create_callback( 'set_routes_for_channel', function ( $channel_id ) use ( $container ) {
			$container[ self::ROUTES ]->set_routes( $channel_id );
		} ), 10, 1 );

		$update_routes = $this->create_callback( 'update_routes', function () use ( $container ) {
			$container[ self::ROUTES ]->update_routes();
		} );

		$route_changed = $this->create_callback( 'route_changed', function () use ( $update_routes ) {
			add_action( 'shutdown', $update_routes, 10, 0 );
		} );

		add_action( 'update_option_show_on_front', $route_changed, 10, 0 );
		add_action( 'update_option_permalink_structure', $route_changed, 10, 0 );
		add_action( 'update_option_' . Cart_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Login_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Account_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Shipping_Returns_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Product_Archive::ARCHIVE_SLUG, $route_changed, 10, 0 );

		//for when site is updated
		add_action( 'update_option_home', $this->create_callback( 'update_site_home', function () use ( $container ) {
			$container[ self::ROUTES ]->update_site_home();
		} ), 10, 0 );

		//for when route assigned pages changed permalink
		add_action( 'post_updated', $this->create_callback( 'update_route_permalink', function ( $post_id, $new_post, $old_post ) use ( $container ) {
			$container[ self::ROUTES ]->update_route_permalink( $post_id, $new_post, $old_post );
		} ), 10, 3 );
	}
}
