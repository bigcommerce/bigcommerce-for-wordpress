<?php


namespace BigCommerce\Container;


use BigCommerce\Accounts\Countries;
use BigCommerce\Accounts\Nav_Menu;
use BigCommerce\Accounts\Sub_Nav;
use BigCommerce\Accounts\User_Profile_Settings;
use BigCommerce\Forms\Delete_Address_Handler;
use BigCommerce\Accounts\Login;
use Pimple\Container;

class Accounts extends Provider {
	const LOGIN          = 'accounts.login';
	const COUNTRIES      = 'accounts.countries';
	const COUNTRIES_PATH = 'accounts.countries.path';
	const DELETE_ADDRESS = 'accounts.delete_address';
	const NAV_MENU       = 'accounts.nav_menu';
	const SUB_NAV        = 'accounts.sub_nav';
	const USER_PROFILE   = 'accounts.user_profile';

	public function register( Container $container ) {
		$container[ self::LOGIN ] = function ( Container $container ) {
			return new Login( $container[ Api::FACTORY ] );
		};

		add_action( 'wp_login', $this->create_callback( 'connect_customer_id', function ( $username, $user ) use ( $container ) {
			$container[ self::LOGIN ]->connect_customer_id( $username, $user );
		} ), 10, 2 );

		add_filter( 'login_url', $this->create_callback( 'login_url', function ( $url, $redirect, $reauth ) use ( $container ) {
			return $container[ self::LOGIN ]->login_url( $url, $redirect, $reauth );
		} ), 10, 3 );

		add_filter( 'wp_login_errors', $this->create_callback( 'login_errors', function ( $errors, $redirect ) use ( $container ) {
			return $container[ self::LOGIN ]->login_error_handler( $errors, $redirect );
		} ), 10, 2 );

		add_filter( 'lostpassword_url', $this->create_callback( 'lostpassword_url', function ( $url, $redirect ) use ( $container ) {
			return $container[ self::LOGIN ]->lostpassword_url( $url, $redirect );
		} ), 10, 2 );

		add_action( 'lostpassword_post', $this->create_callback( 'lostpassword_post', function ( $error ) use ( $container ) {
			return $container[ self::LOGIN ]->lostpassword_error_handler( $error );
		} ), 10, 1 );

		add_filter( 'register_url', $this->create_callback( 'register_url', function ( $url ) use ( $container ) {
			return $container[ self::LOGIN ]->register_url( $url );
		} ), 10, 1 );

		add_action( 'template_redirect', $this->create_callback( 'redirects', function () use ( $container ) {
			$container[ self::LOGIN ]->redirect_account_pages_to_auth();
			$container[ self::LOGIN ]->redirect_auth_pages_to_account();
		} ), 10, 0 );

		add_filter( 'authenticate', $this->create_callback( 'authenticate_new_user', function ( $user, $username, $password ) use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				return $container[ self::LOGIN ]->authenticate_new_user( $user, $username, $password );
			}
			return $user;
		} ), 40, 3 );
		add_filter( 'check_password', $this->create_callback( 'check_password', function ( $match, $password, $hash, $user_id ) use ( $container ) {
			if ( $container[ Api::CONFIG_COMPLETE ] ) {
				return $container[ self::LOGIN ]->check_password_for_linked_accounts( $match, $password, $hash, $user_id );
			}
			return $match;
		} ), 10, 4 );

		$container[ self::COUNTRIES ] = function ( Container $container ) {
			return new Countries( $container[ self::COUNTRIES_PATH ] );
		};

		$container[ self::COUNTRIES_PATH ] = function ( Container $container ) {
			$file = plugin_dir_path( $container[ 'plugin_file' ] ) . 'assets/data/countries.json';

			return apply_filters( 'bigcommerce/countries/data_file', $file );
		};

		add_filter( 'bigcommerce/countries/data', $this->create_callback( 'countries', function ( $data ) use ( $container ) {
			return $container[ self::COUNTRIES ]->get_countries();
		} ), 5, 1 );
		add_filter( 'bigcommerce/js_config', $this->create_callback( 'js_config', function ( $config ) use ( $container ) {
			return $container[ self::COUNTRIES ]->js_config( $config );
		} ), 10, 1 );

		$container[ self::DELETE_ADDRESS ] = function ( Container $container ) {
			return new Delete_Address_Handler();
		};
		add_action( 'parse_request', $this->create_callback( 'handle_delete_address', function () use ( $container ) {
			$container[ self::DELETE_ADDRESS ]->handle_request( $_POST );
		} ), 10, 0 );

		$container[ self::NAV_MENU ] = function ( Container $container ) {
			return new Nav_Menu();
		};

		add_filter( 'wp_setup_nav_menu_item', $this->create_callback( 'loginregister_menu_item', function ( $item ) use ( $container ) {
			return $container[ self::NAV_MENU ]->filter_account_menu_items( $item );
		} ), 10, 1 );

		$container[ self::SUB_NAV ] = function ( Container $container ) {
			return new Sub_Nav();
		};
		add_filter( 'the_content', $this->create_callback( 'account_subnav', function ( $content ) use ( $container ) {
			return $container[ self::SUB_NAV ]->add_subnav_above_content( $content );
		} ), 10, 1 );

		$container[ self::USER_PROFILE ] = function ( Container $container ) {
			return new User_Profile_Settings();
		};
		$render_profile_settings         = $this->create_callback( 'render_profile_settings', function ( $user ) use ( $container ) {
			$container[ self::USER_PROFILE ]->render_profile_settings( $user );
		} );
		$save_profile_settings           = $this->create_callback( 'save_profile_settings', function ( $user_id ) use ( $container ) {
			$container[ self::USER_PROFILE ]->save_profile_settings( $user_id );
		} );
		add_action( 'show_user_profile', $render_profile_settings, 10, 1 );
		add_action( 'edit_user_profile', $render_profile_settings, 10, 1 );
		add_action( 'personal_options_update', $save_profile_settings, 10, 1 );
		add_action( 'edit_user_profile_update', $save_profile_settings, 10, 1 );
	}

}