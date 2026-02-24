<?php


namespace BigCommerce\Accounts;


use BigCommerce\Accounts\Roles\Customer as Customer_Role;
use BigCommerce\Api_Factory;
use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Settings\Sections\Account_Settings;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Webhooks\Customer\Customer_Channel_Updater;
use WP_User;

/**
 * Handles the login and lost password logic for the BigCommerce account integration with WordPress.
 * This class connects WordPress users to BigCommerce customers, processes customer login, and provides the necessary functionality for user management.
 */
class Login {
	/**
	 * The constant for storing the BigCommerce customer ID meta key.
	 */
	const CUSTOMER_ID_META = 'bigcommerce_customer_id';

	/**
	 * @var Api_Factory The factory class for creating API instances.
	 */
	private $api_factory;

	/**
	 * Login constructor.
	 *
	 * @param Api_Factory $api_factory The API factory to be used for creating API instances.
	 */
	public function __construct( Api_Factory $api_factory ) {
		$this->api_factory = $api_factory;
	}

	/**
	 * Connect the user to a BigCommerce account, if it exists
	 *
	 * @param string  $username
	 * @param WP_User $user
	 *
	 * @return void
	 * @action wp_login
	 */
	public function connect_customer_id( $username, $user ) {
		if ( ! ( $user instanceof WP_User ) ) {
			return; // don't have an authenticated user
		}

		$customer    = new Customer( $user->ID );
		$customer_id = $customer->get_customer_id();
		if ( ! empty( $customer_id ) ) {
			return; // already connected
		}

		$customer_id = $this->find_customer_id_by_email( $user->user_email );
		if ( $customer_id ) {
			$customer->set_customer_id( $customer_id );

			return;
		}

		$this->create_customer_from_user( $user );
	}

	/**
	 * Find the customer ID associated with the given email address
	 *
	 * @param string $email
	 *
	 * @return int The customer ID, 0 if not found
	 */
	private function find_customer_id_by_email( $email ) {
		return $this->api_factory->customer()->find_customer_id_by_email( $email );
	}

	/**
	 * Create BC customer from wp user
	 * @param \WP_User $user
	 *
	 * @return int The new customer's ID, 0 on failure
	 */
	private function create_customer_from_user( $user ) {
		try {
			$api               = $this->api_factory->customer();
			$new_customer_data = [
				'first_name' => $user->first_name ?: $user->user_login,
				'last_name'  => $user->last_name ?: __( 'User', 'bigcommerce' ),
				'email'      => $user->user_email,
			];

			/**
			 * Filters customer create arguments.
			 *
			 * @param array $new_customer_data Customer data.
			 */
			$new_customer_data = apply_filters( 'bigcommerce/customer/create/args', $new_customer_data );

			$response = $api->createCustomer( $new_customer_data );

			if ( $response && ! empty( $response->id ) ) {
				$customer = new Customer( $user->ID );
				$customer->set_customer_id( $response->id );

				$connections  = new Connections();
				$channel      = $connections->primary();
				$channel_id   = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
				$this->api_factory->customers()->customersPut( [
						'origin_channel_id' => ( int ) $channel_id,
						'channels_ids' => [ ( int ) $channel_id ],
				] );

				return $response->id;
			}
		} catch ( \Exception $e ) {
			return 0;
		}

		return 0;
	}

	/**
	 * Filters the login URL to point to the front-end login page
	 *
	 * @param string $login_url    The login URL. Not HTML-encoded.
	 * @param string $redirect     The path to redirect to on login, if supplied.
	 * @param bool   $force_reauth Whether to force reauthorization, even if a cookie is present.
	 *
	 * @return string
	 * @filter login_url
	 */
	public function login_url( $login_url, $redirect, $force_reauth ) {
		$login_page_id = get_option( Login_Page::NAME, 0 );
		if ( empty( $login_page_id ) ) {
			return $login_url;
		}

		$login_url = get_permalink( $login_page_id );

		if ( ! empty( $redirect ) ) {
			$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
		}

		if ( $force_reauth ) {
			$login_url = add_query_arg( 'reauth', '1', $login_url );
		}

		return $login_url;
	}

	/**
	 * Handle errors from the login form, redirecting back
	 * to the front-end login page.
	 *
	 * Note that is hooked in on a filter, not an action,
	 * since the latter was not available.
	 *
	 * @param \WP_Error $errors
	 * @param string    $redirect
	 *
	 * @return \WP_Error
	 * @filter wp_login_errors 100
	 */
	public function login_error_handler( $errors, $redirect ) {
		if ( ! $errors->get_error_code() ) {
			return $errors; // no errors. why are we here?
		}

		$url = wp_login_url( $redirect );
		$url = add_query_arg( [ 'bc-message' => urlencode( $errors->get_error_code() ) ], $url );
		wp_safe_redirect( esc_url_raw( $url ) );
		exit();
	}

	/**
	 * Get lost password url
	 * @param string $login_url
	 * @param string $redirect
	 *
	 * @return string
	 * @filter lostpassword_url
	 */
	public function lostpassword_url( $login_url, $redirect ) {
		$login_page_id = get_option( Login_Page::NAME, 0 );
		if ( empty( $login_page_id ) ) {
			return $login_url;
		}

		$login_url = get_permalink( $login_page_id );

		$args = [ 'action' => 'lostpassword' ];
		if ( ! empty( $redirect ) ) {
			$args[ 'redirect_to' ] = urlencode( $redirect );
		}

		$login_url = add_query_arg( $args, $login_url );

		return $login_url;
	}


	/**
	 * If a user exists only on BC, try to sync before reset password email is sent.
	 *
	 * @param WP_User|false $user_data WP_User object if found, false if the user does not exist.
	 * @param \WP_Error      $errors    A WP_Error object containing any errors generated
	 *                                 by using invalid credentials.
	 *
	 * @return \WP_User|false
	 * @action lostpassword_user_data
	 */
	public function before_reset_password_email( $user_data, $errors ) {
		if ( $errors->get_error_code() === 'invalid_email' ) {
			$user_login  = filter_input( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );
			$customer_id = $this->find_customer_id_by_email( $user_login );

			if ( ! $customer_id ) {
				return false;
			}

			$user_id = wp_create_user( $user_login, wp_generate_password(), $user_login );
			if ( is_wp_error( $user_id ) ) {
				return false;
			}
			$user = new \WP_User( $user_id );

			/**
			 * Filter the default role given to new users
			 *
			 * @param string $role
			 */
			$role = apply_filters( 'bigcommerce/user/default_role', Customer_Role::NAME );
			$user->set_role( $role );

			// all future password validation will be against the API for this user
			update_user_meta( $user_id, User_Profile_Settings::SYNC_PASSWORD, true );

			$customer = new Customer( $user_id );
			$customer->set_customer_id( $customer_id );

			// Remove the error code to remove it from the front end to display to to user.
			$errors->remove( 'invalid_email' );

			return $user;
		}

		return $user_data;
	}

	/**
	 * @param \WP_Error $error
	 *
	 * @return void
	 * @action lostpassword_post
	 */
	public function lostpassword_error_handler( $error ) {
		// Don't intercept admin reset password link
		if ( is_admin() ) {
			return;
		}

		if ( ! $error->get_error_code() ) {
			$user_login = filter_input( INPUT_POST, 'user_login', FILTER_SANITIZE_STRING );
			if ( strpos( $user_login, '@' ) !== false ) {
				return; // WP has already checked it as an email address
			}
			// WP doesn't add this as an error until after lostpassword_post
			$user_data = get_user_by( 'login', $user_login );

			if ( ! empty( $user_data ) ) {
				return; // no errors
			} else {
				$error->add( 'invalid_email', __( 'Please enter a valid email address.', 'bigcommerce' ) );
			}
		}
		$url = wp_lostpassword_url();
		$url = add_query_arg( [ 'bc-message' => urlencode( $error->get_error_code() ) ], $url );
		wp_safe_redirect( esc_url_raw( $url ) );
		exit();
	}

	/**
     * Handle registration url
     *
	 * @param string $url
	 *
	 * @return string
	 * @filter register_url
	 */
	public function register_url( $url ) {
		$page_id = get_option( Registration_Page::NAME, 0 );
		if ( empty( $page_id ) ) {
			return $url;
		}
		$url = get_permalink( $page_id );

		return $url;
	}

	/**
	 * Redirect all account pages to the login screen
	 * for unauthenticated users.
	 *
	 * @return void
	 * @action template_redirect
	 */
	public function redirect_account_pages_to_auth() {
		if ( ! is_singular() || is_user_logged_in() ) {
			return;
		}
		$page          = get_queried_object_id();
		$account_pages = array_filter( [
			get_option( Account_Page::NAME, 0 ),
			get_option( Address_Page::NAME, 0 ),
			get_option( Orders_Page::NAME, 0 ),
			get_option( Wishlist_Page::NAME, 0 ),
		] );
		if ( in_array( $page, $account_pages ) ) {
			$url = esc_url_raw( wp_login_url( get_permalink( $page ) ) );
			wp_safe_redirect( esc_url_raw( $url ) );
			exit();
		}
	}

	/**
	 * Redirect the login/registration pages to the
	 * account page for logged in users.
	 *
	 * @return void
	 * @action template_redirect
	 */
	public function redirect_auth_pages_to_account() {
		if ( ! is_singular() || ! is_user_logged_in() ) {
			return;
		}
		$page       = get_queried_object_id();
		$auth_pages = array_filter( [
			get_option( Login_Page::NAME, 0 ),
			get_option( Registration_Page::NAME, 0 ),
		] );
		if ( in_array( $page, $auth_pages ) ) {
			$account_page = get_option( Account_Page::NAME, 0 );
			$url          = $account_page ? get_permalink( $account_page ) : home_url( '/' );
			/**
			 * Filter the URL to the account profile page
			 *
			 * @param string $url The account profile page URL
			 */
			$url = apply_filters( 'bigcommerce/account/profile/permalink', $url );

			// Go to default WP login page if it's a confirm email action.
			$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
			if ( $action === 'confirm_admin_email' ) {
				remove_filter( 'login_url', bigcommerce()->accounts->login_url, 10 );
				$url = add_query_arg(
					[
						'action'  => $action,
						'wp_lang' => filter_input( INPUT_GET, 'wp_lang', FILTER_SANITIZE_STRING ),
					],
					wp_login_url( $url )
				);
			}

			wp_safe_redirect( esc_url_raw( $url ) );
			exit();
		}
	}

	/**
	 * If a user logs in with credentials for a user in BigCommerce
	 * that do not match a user in WordPress, create a user account
	 * and log the user in.
	 *
	 * @param \WP_User|\WP_Error|null $user
	 * @param string                  $username
	 * @param string                  $password
	 *
	 * @return \WP_User|\WP_Error|null
	 * @filter authenticate 40
	 */
	public function authenticate_new_user( $user, $username, $password ) {
		if ( $user instanceof WP_User ) {
			return $user;
		}

		if ( ! is_email( $username ) ) {
			return $user;
		}

		$matching_user = get_user_by( 'email', $username );
		if ( $matching_user ) {
			return $user; // don't try to create a new user if we already have one with that email
		}

		$api_v3 = $this->api_factory->customers();
		$api    = $this->api_factory->customer();

		try {
			$matches = $api_v3->customersGet( [
				'email:in' => $username,
			] )->getData();

			if ( empty( $matches ) ) {
				return $user;
			}

			/** @var \BigCommerce\Api\v3\Model\Customer $customer */
			$found_customer = reset( $matches );

			$valid = $api->validatePassword( $found_customer->getId(), $password );
			if ( ! $valid ) {
				return new \WP_Error( 'incorrect_password',
					sprintf(
						__( 'The password you entered for the email address %s is incorrect.', 'bigcommerce' ),
						$username
					)
				);
			}

			$connections            = new Connections();
			$channel                = $connections->primary();
			$channel_id             = get_term_meta( $channel->term_id, Channel::CHANNEL_ID, true );
			$is_allow_global_logins = (bool) get_option( Account_Settings::ALLOW_GLOBAL_LOGINS, true ) === true;
			$is_belong_to_channel   = in_array( $channel_id, ( array ) $found_customer->getChannelIds() );
			// Do not allow logging in users from another channels on MSF while global login is disabled
			if ( Store_Settings::is_msf_on() && ! $is_allow_global_logins && ! $is_belong_to_channel ) {
				return new \WP_Error( 'incorrect_email',
					sprintf(
						__( 'The access for %s is prohibited. Please create a new user or try another one', 'bigcommerce' ),
						$username
					)
				);
			}

			// we have a valid user, so create a WP account

			$user_id = wp_create_user( $username, $password, $username );
			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}
			$user = new \WP_User( $user_id );

			/**
			 * Filter the default role given to new users
			 *
			 * @param string $role
			 */
			$role = apply_filters( 'bigcommerce/user/default_role', Customer_Role::NAME );
			$user->set_role( $role );

			// all future password validation will be against the API for this user
			update_user_meta( $user_id, User_Profile_Settings::SYNC_PASSWORD, true );
			update_user_meta( $user_id, Customer_Channel_Updater::CUSTOMER_CHANNEL_META, ( array ) $found_customer->getChannelIds() );
			update_user_meta( $user_id, Customer_Channel_Updater::CUSTOMER_ORIGIN_CHANNEL, $found_customer->getOriginChannelId() );
			$customer = new Customer( $user_id );
			$customer->set_customer_id( $found_customer->getId() );

			return new \WP_User( $user_id );

		} catch ( \Exception $e ) {
			return $user;
		}
	}

	/**
	 * Validate password for accounts
	 *
	 * @param bool       $match    Whether the passwords match.
	 * @param string     $password The plaintext password.
	 * @param string     $hash     The hashed password.
	 * @param string|int $user_id  User ID. Can be empty.
	 *
	 * @return bool
	 * @filter check_password
	 */
	public function check_password_for_linked_accounts( $match, $password, $hash, $user_id ) {
		$sync = get_user_meta( $user_id, User_Profile_Settings::SYNC_PASSWORD, true );
		if ( ! $sync ) {
			return $match;
		}

		$customer    = new Customer( $user_id );
		$customer_id = $customer->get_customer_id();
		if ( ! $customer_id ) {
			/*
			 * If an account is set to sync with BigCommerce, but we don't know
			 * the customer ID, we'll look it up here. Presuming we find it,
			 * we can validate the password against that ID.
			 *
			 * After a successful login, the customer ID will be set in
			 * self::connect_customer_id() on the wp_login action.
			 */
			$user        = new \WP_User( $user_id );
			$customer_id = $this->find_customer_id_by_email( $user->user_email );
			if ( ! $customer_id ) {
				return $match;
			}
		}

		$api = $this->api_factory->customer();
		try {
			return $api->validatePassword( $customer_id, $password );
		} catch ( \InvalidArgumentException $e ) {
			// The user no longer exists in BigCommerce. Delete it.
			$this->delete_user( $user_id, $customer_id );

			return false;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Delete WP user
	 *
	 * @param $user_id
	 *
	 * @param $customer_id
	 */
	private function delete_user( $user_id, $customer_id ) {
		/**
		 * Filter whether to delete WordPress users tied to BigCommerce
		 * customer accounts that no longer exist.
		 *
		 * @param bool $delete      Whether to delete the user. Default: true
		 * @param int  $user_id     The ID of the user that will be deleted
		 * @param int  $customer_id The former customer ID of the user
		 */
		if ( ! apply_filters( 'bigcommerce/accounts/login/delete_missing_user', true, $user_id, $customer_id ) ) {
			return;
		}
		require_once( ABSPATH . 'wp-admin/includes/user.php' );
		wp_delete_user( $user_id );
	}

}
