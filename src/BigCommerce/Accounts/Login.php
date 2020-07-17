<?php


namespace BigCommerce\Accounts;


use BigCommerce\Accounts\Roles\Customer as Customer_Role;
use Bigcommerce\Api;
use BigCommerce\Api_Factory;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Pages\Wishlist_Page;
use WP_User;

class Login {
	const CUSTOMER_ID_META = 'bigcommerce_customer_id';

	/** @var Api_Factory */
	private $api_factory;

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
			$new_customer_data = apply_filters( 'bigcommerce/customer/create/args', $new_customer_data );

			$response = $api->createCustomer( $new_customer_data );

			if ( $response && ! empty( $response->id ) ) {
				$customer = new Customer( $user->ID );
				$customer->set_customer_id( $response->id );

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
	 * @param \WP_Error $error
	 *
	 * @return void
	 * @action lostpassword_post
	 */
	public function lostpassword_error_handler( $error ) {

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

		$api = $this->api_factory->customer();

		try {
			$matches = $api->getCustomers( [
				'email' => $username,
			] );

			if ( empty( $matches ) ) {
				return $user;
			}

			/** @var Api\Resources\Customer $customer */
			$found_customer = reset( $matches );

			$valid = $api->validatePassword( $found_customer->id, $password );
			if ( ! $valid ) {
				return new \WP_Error( 'incorrect_password',
					sprintf(
						__( 'The password you entered for the email address %s is incorrect.', 'bigcommerce' ),
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

			$customer = new Customer( $user_id );
			$customer->set_customer_id( $found_customer->id );

			return new \WP_User( $user_id );

		} catch ( \Exception $e ) {
			return $user;
		}
	}

	/**
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
