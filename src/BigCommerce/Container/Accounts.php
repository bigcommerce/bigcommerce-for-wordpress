<?php


namespace BigCommerce\Container;

use BigCommerce\Accounts\Channel_Settings;
use BigCommerce\Accounts\Countries;
use BigCommerce\Accounts\Customer_Group_Proxy;
use BigCommerce\Accounts\Nav_Menu;
use BigCommerce\Accounts\Password_Reset;
use BigCommerce\Accounts\Register;
use BigCommerce\Accounts\Wishlists\Actions as Wishlist_Actions;
use BigCommerce\Accounts\Wishlists\Add_Item_View;
use BigCommerce\Accounts\Wishlists\Wishlist_Request_Parser;
use BigCommerce\Accounts\Sub_Nav;
use BigCommerce\Accounts\User_Profile_Settings;
use BigCommerce\Forms\Delete_Address_Handler;
use BigCommerce\Accounts\Login;
use BigCommerce\Api_Factory;
use BigCommerce\Taxonomies\Channel\Connections;
use Pimple\Container;

/**
 * Class Accounts
 *
 * Handles the registration and initialization of various account-related services
 * such as login, registration, countries, wishlists, profile settings, and customer groups.
 *
 * @package BigCommerce\Container
 */
class Accounts extends Provider {
	/**
	 * The constant for the login service.
	 * Represents the key for the accounts login service in the container.
	 *
	 * @var string
	 */
	const LOGIN = 'accounts.login';

	/**
	 * The constant for the register service.
	 * Represents the key for the accounts registration service in the container.
	 *
	 * @var string
	 */
	const REGISTER = 'accounts.register';

	/**
	 * The constant for the countries service.
	 * Represents the key for the accounts countries service in the container.
	 *
	 * @var string
	 */
	const COUNTRIES = 'accounts.countries';

	/**
	 * The constant for the countries data file path.
	 * Represents the key for the path to the countries data file in the container.
	 *
	 * @var string
	 */
	const COUNTRIES_PATH = 'accounts.countries.path';

	/**
	 * The constant for the delete address service.
	 * Represents the key for the accounts delete address service in the container.
	 *
	 * @var string
	 */
	const DELETE_ADDRESS = 'accounts.delete_address';

	/**
	 * The constant for the navigation menu service.
	 * Represents the key for the accounts navigation menu service in the container.
	 *
	 * @var string
	 */
	const NAV_MENU = 'accounts.nav_menu';

	/**
	 * The constant for the sub-navigation service.
	 * Represents the key for the accounts sub-navigation service in the container.
	 *
	 * @var string
	 */
	const SUB_NAV = 'accounts.sub_nav';

	/**
	 * The constant for the user profile settings service.
	 * Represents the key for the accounts user profile settings service in the container.
	 *
	 * @var string
	 */
	const USER_PROFILE = 'accounts.user_profile';

	/**
	 * The constant for the customer group proxy service.
	 * Represents the key for the accounts customer group proxy service in the container.
	 *
	 * @var string
	 */
	const GROUP_PROXY = 'accounts.groups.proxy';

	/**
	 * The constant for the password reset service.
	 * Represents the key for the accounts password reset service in the container.
	 *
	 * @var string
	 */
	const PASSWORD_RESET = 'accounts.password_reset';

	/**
	 * The constant for the channel settings service.
	 * Represents the key for the accounts channel settings service in the container.
	 *
	 * @var string
	 */
	const CHANNEL_SETTINGS = 'accounts.channel_settings';

	/**
	 * The constant for the public wishlist service.
	 * Represents the key for the public wishlist service in the container.
	 *
	 * @var string
	 */
	const PUBLIC_WISHLIST = 'accounts.wishlist.public';

	/**
	 * The constant for the wishlist router service.
	 * Represents the key for the wishlist router service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_ROUTER = 'accounts.wishlist.router';

	/**
	 * The constant for the wishlist creation service.
	 * Represents the key for the wishlist creation service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_CREATE = 'accounts.wishlist.create';

	/**
	 * The constant for the wishlist edit service.
	 * Represents the key for the wishlist edit service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_EDIT = 'accounts.wishlist.edit';

	/**
	 * The constant for the wishlist delete service.
	 * Represents the key for the wishlist delete service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_DELETE = 'accounts.wishlist.delete';

	/**
	 * The constant for the wishlist add item service.
	 * Represents the key for the wishlist add item service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_ADD = 'accounts.wishlist.add_item';

	/**
	 * The constant for the wishlist remove item service.
	 * Represents the key for the wishlist remove item service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_REMOVE = 'accounts.wishlist.remove_item';

	/**
	 * The constant for the wishlist add item view service.
	 * Represents the key for the wishlist add item view service in the container.
	 *
	 * @var string
	 */
	const WISHLIST_ADD_ITEM_VIEW = 'accounts.wishlist.add_item_view';

    /**
     * Registers various services into the container
     *
     * @param Container $container The dependency injection container
     */
    public function register( Container $container ) {
        $this->login( $container );
        $this->countries( $container );
        $this->profile( $container );
        $this->addresses( $container );
        $this->customer_groups( $container );
        $this->wishlists( $container );
        $this->passwords( $container );
        $this->channel_settings( $container );
    }

    /**
     * Registers and handles login-related actions.
     *
     * @param Container $container The dependency injection container
     */
    private function login( Container $container ) {
        /** Registering login service */
        $container[ self::LOGIN ] = function ( Container $container ) {
            return new Login( $container[ Api::FACTORY ] );
        };

        /** Registering register service */
        $container[ self::REGISTER ] = function ( Container $container ) {
            return new Register( $container[ Api::FACTORY ], new Connections() );
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

		add_filter( 'lostpassword_user_data', $this->create_callback( 'lostpassword_user_data', function ( $user_data, $errors ) use ( $container ) {
			return $container[ self::LOGIN ]->before_reset_password_email( $user_data, $errors );
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

		add_action( 'user_register', $this->create_callback( 'create_customer_from_admin', function ( $user_id, $userdata ) use ( $container ) {
			$container[ self::REGISTER ]->maybe_create_new_customer( $user_id, $userdata );
		} ), 10, 2 );

	}

    /**
     * Registers and handles country-related services and requests.
     *
     * @param Container $container The dependency injection container
     */
    private function countries( Container $container ) {
        /** Registering countries service */
        $container[ self::COUNTRIES ] = function ( Container $container ) {
            return new Countries( $container[ self::COUNTRIES_PATH ] );
        };

        /** Registering countries path */
        $container[ self::COUNTRIES_PATH ] = function ( Container $container ) {
            $file = plugin_dir_path( $container['plugin_file'] ) . 'assets/data/countries.json';

            /**
             * Filters the path of the countries data file.
             *
             * @param string $file Countries data json file path
             */
            return apply_filters( 'bigcommerce/countries/data_file', $file );
        };

        add_filter( 'bigcommerce/countries/data', $this->create_callback( 'countries', function ( $data ) use ( $container ) {
            return $container[ self::COUNTRIES ]->get_countries();
        } ), 5, 1 );

		$countries_js_config = $this->create_callback( 'countries_js_config', function ( $config ) use ( $container ) {
			return $container[ self::COUNTRIES ]->js_config( $config );
		} );

		add_filter( 'bigcommerce/js_config', $countries_js_config, 10, 1 );

		add_filter( 'bigcommerce/admin/js_config', $countries_js_config, 10, 1 );
    }

    /**
     * Registers profile-related services and actions.
     *
     * @param Container $container The dependency injection container
     */
    private function profile( Container $container ) {
        /** Registering navigation menu service */
        $container[ self::NAV_MENU ] = function ( Container $container ) {
            return new Nav_Menu();
        };

        add_filter( 'wp_setup_nav_menu_item', $this->create_callback( 'loginregister_menu_item', function ( $item ) use ( $container ) {
            return $container[ self::NAV_MENU ]->filter_account_menu_items( $item );
        } ), 10, 1 );

        /** Registering sub-navigation service */
        $container[ self::SUB_NAV ] = function ( Container $container ) {
            return new Sub_Nav();
        };

        add_filter( 'the_content', $this->create_callback( 'account_subnav', function ( $content ) use ( $container ) {
            return $container[ self::SUB_NAV ]->add_subnav_above_content( $content );
        } ), 10, 1 );

        /** Registering user profile settings service */
        $container[ self::USER_PROFILE ] = function ( Container $container ) {
            return new User_Profile_Settings();
        };

        /** Action to render and save profile settings */
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

    /**
     * Registers and handles address-related services and actions.
     *
     * @param Container $container The dependency injection container
     */
    private function addresses( Container $container ) {
        /** Registering delete address handler */
        $container[ self::DELETE_ADDRESS ] = function ( Container $container ) {
            return new Delete_Address_Handler();
        };

        add_action( 'parse_request', $this->create_callback( 'handle_delete_address', function () use ( $container ) {
            $container[ self::DELETE_ADDRESS ]->handle_request( $_POST );
        } ), 10, 0 );
    }

    /**
     * Registers customer group-related services and actions.
     *
     * @param Container $container The dependency injection container
     */
    private function customer_groups( Container $container ) {
        /** Registering customer group proxy service */
        $container[ self::GROUP_PROXY ] = function ( Container $container ) {
            return new Customer_Group_Proxy();
        };

        add_filter( 'bigcommerce/customer/group_info', $this->create_callback( 'set_customer_group_info', function ( $info, $group_id ) use ( $container ) {
            return $container[ self::GROUP_PROXY ]->filter_group_info( $info, $group_id );
        } ), 10, 2 );
    }

    /**
     * Registers and handles wishlist-related services and actions.
     *
     * @param Container $container The dependency injection container
     */
    private function wishlists( Container $container ) {
        /** Registering public wishlist parser */
        $container[ self::PUBLIC_WISHLIST ] = function ( Container $container ) {
            return new Wishlist_Request_Parser( $container[ Api::FACTORY ]->wishlists() );
        };

        add_action( 'parse_request', $this->create_callback( 'public_wishlist_request', function ( \WP $wp ) use ( $container ) {
            $container[ self::PUBLIC_WISHLIST ]->setup_wishlist_request( $wp );
        } ), 10, 1 );
		
		/** Initialize the wishlist request router */
		$container[ self::WISHLIST_ROUTER ] = function ( Container $container ) {
			return new Wishlist_Actions\Request_Router();
		};

		add_action( 'bigcommerce/action_endpoint/' . Wishlist_Actions\Request_Router::ACTION, $this->create_callback( 'handle_wishlist_action', function ( $args ) use ( $container ) {
			$container[ self::WISHLIST_ROUTER ]->handle_request( $args );
		} ), 10, 1 );
	
		/** Initialize the create wishlist action handler */
		$container[ self::WISHLIST_CREATE ] = function ( Container $container ) {
			return new Wishlist_Actions\Create_Wishlist( $container[ Api::FACTORY ]->wishlists() );
		};
	
		add_action( 'bigcommerce/wishlist_endpoint/' . Wishlist_Actions\Create_Wishlist::ACTION, $this->create_callback( 'create_wishlist', function ( $args ) use ( $container ) {
			$container[ self::WISHLIST_CREATE ]->handle_request( $args );
		} ), 10, 1 );
	
		/** Initialize the edit wishlist action handler */
		$container[ self::WISHLIST_EDIT ] = function ( Container $container ) {
			return new Wishlist_Actions\Edit_Wishlist( $container[ Api::FACTORY ]->wishlists() );
		};
	
		add_action( 'bigcommerce/wishlist_endpoint/' . Wishlist_Actions\Edit_Wishlist::ACTION, $this->create_callback( 'edit_wishlist', function ( $args ) use ( $container ) {
			$container[ self::WISHLIST_EDIT ]->handle_request( $args );
		} ), 10, 1 );
	
		/** Initialize the delete wishlist action handler */
		$container[ self::WISHLIST_DELETE ] = function ( Container $container ) {
			return new Wishlist_Actions\Delete_Wishlist( $container[ Api::FACTORY ]->wishlists() );
		};
	
		add_action( 'bigcommerce/wishlist_endpoint/' . Wishlist_Actions\Delete_Wishlist::ACTION, $this->create_callback( 'delete_wishlist', function ( $args ) use ( $container ) {
			$container[ self::WISHLIST_DELETE ]->handle_request( $args );
		} ), 10, 1 );
	
		/** Initialize the add item to wishlist action handler */
		$container[ self::WISHLIST_ADD ] = function ( Container $container ) {
			return new Wishlist_Actions\Add_Item( $container[ Api::FACTORY ]->wishlists() );
		};
	
		add_action( 'bigcommerce/wishlist_endpoint/' . Wishlist_Actions\Add_Item::ACTION, $this->create_callback( 'add_wishlist_item', function ( $args ) use ( $container ) {
			$container[ self::WISHLIST_ADD ]->handle_request( $args );
		} ), 10, 1 );
	
		/** Initialize the remove item from wishlist action handler */
		$container[ self::WISHLIST_REMOVE ] = function ( Container $container ) {
			return new Wishlist_Actions\Remove_Item( $container[ Api::FACTORY ]->wishlists() );
		};
	
		add_action( 'bigcommerce/wishlist_endpoint/' . Wishlist_Actions\Remove_Item::ACTION, $this->create_callback( 'remove_wishlist_item', function ( $args ) use ( $container ) {
			$container[ self::WISHLIST_REMOVE ]->handle_request( $args );
		} ), 10, 1 );
	
		/** Initialize the add item view to product single page */
		$container[ self::WISHLIST_ADD_ITEM_VIEW ] = function ( Container $container ) {
			return new Add_Item_View( $container[ Api::FACTORY ]->wishlists() );
		};
	
		/** Add item view to product single page template */ 
		$add_item_view_to_product_single = $this->create_callback( 'add_item_view_to_product_single', function ( $data, $template, $options ) use ( $container ) {
			return $container[ self::WISHLIST_ADD_ITEM_VIEW ]->filter_product_single_template( $data, $template, $options );
		} );
		add_action( 'bigcommerce/template=components/products/product-single.php/data', $add_item_view_to_product_single, 10, 3 );
		// Decided not to show on the shortcode single
		//add_action( 'bigcommerce/template=components/products/product-shortcode-single.php/data', $add_item_view_to_product_single, 10, 3 );
		}
		
		/**
		 * Handle passwords container and hooks
		 * @param Container $container
		 */
		private function passwords( Container $container ) {
			/** Initialize password reset action handler */
			$container[ self::PASSWORD_RESET ] = function ( Container $container ) {
				return new Password_Reset( $container[ Api::FACTORY ]->customer() );
			};
		
			add_action( 'after_password_reset', $this->create_callback( 'sync_reset_password', function ( $user, $password ) use ( $container ) {
				$container[ self::PASSWORD_RESET ]->sync_reset_password_with_bigcommerce( $user, $password );
			} ), 10, 2 );
		
			add_action( 'profile_update', $this->create_callback( 'sync_changed_password', function ( $user, $old_user_data ) use ( $container ) {
				$container[ self::PASSWORD_RESET ]->sync_password_change_with_bigcommerce( $user, $old_user_data );
			} ), 10, 2 );
		}
		
		/**
		 * Handle channel settings container and hooks
		 * @param Container $container
		 */
		private function channel_settings( Container $container ) {
			/** Initialize channel settings action handler */
			$container[ self::CHANNEL_SETTINGS ] = function ( Container $container ) {
				return new Channel_Settings( new Connections(), $container[ Api::FACTORY ]->customers() );
			};
		
			add_action( 'bigcommerce/sync_global_logins', $this->create_callback( 'sync_global_logins', function () use ( $container ) {
				$container[ self::CHANNEL_SETTINGS ]->sync_global_logins();
			} ) );
		
			add_action( 'bigcommerce/channel/promote', $this->create_callback( 'schedule_global_logins_sync', function () use ( $container ) {
				$container[ self::CHANNEL_SETTINGS ]->schedule_sync();
			} ) );
		}

	}		
