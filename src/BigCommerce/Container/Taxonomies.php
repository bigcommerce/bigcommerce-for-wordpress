<?php


namespace BigCommerce\Container;


use BigCommerce\Customizer\Sections\Product_Archive;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Login_Page;
use BigCommerce\Pages\Shipping_Returns_Page;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;
use BigCommerce\Settings\Screens\Connect_Channel_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Settings\Sections\Api_Credentials;
use BigCommerce\Settings\Sections\Channel_Select;
use BigCommerce\Settings\Sections\Channels as Channel_Settings;
use BigCommerce\Taxonomies\Availability;
use BigCommerce\Taxonomies\Brand;
use BigCommerce\Taxonomies\Channel;
use BigCommerce\Taxonomies\Channel\Channel_Connector;
use BigCommerce\Taxonomies\Condition;
use BigCommerce\Taxonomies\Flag;
use BigCommerce\Taxonomies\Product_Category;
use BigCommerce\Taxonomies\Product_Type;
use Pimple\Container;

/**
 * This class is responsible for managing and registering taxonomies for BigCommerce integration. It defines constants for various taxonomies and handles their configuration and filtering via WordPress hooks.
 * 
 * @package BigCommerce\Container
 */
class Taxonomies extends Provider {

    /**
     * Constant for Product Category taxonomy.
	 * @var string
     */
    const PRODUCT_CATEGORY = 'taxonomy.product_category';

    /**
     * Constant for Product Category configuration.
	 * @var string
     */
    const PRODUCT_CATEGORY_CONFIG = 'taxonomy.product_category.config';

    /**
     * Constant for Product Category group filtered terms.
	 * @var string
     */
    const PRODUCT_CATEGORY_GROUP_FILTERED_TERMS = 'taxonomy.product_category.group_filtered_terms';

    /**
     * Constant for Product Category query filter.
	 * @var string
     */
    const PRODUCT_CATEGORY_QUERY_FILTER = 'taxonomy.product_category.query_filter';

    /**
     * Constant for Brand taxonomy.
	 * @var string
     */
    const BRAND = 'taxonomy.brand';

    /**
     * Constant for Brand configuration.
	 * @var string
     */
    const BRAND_CONFIG = 'taxonomy.brand.config';

    /**
     * Constant for Availability taxonomy.
	 * @var string
     */
    const AVAILABILITY = 'taxonomy.availability';

    /**
     * Constant for Availability configuration.
	 * @var string
     */
    const AVAILABILITY_CONFIG = 'taxonomy.availability.config';

    /**
     * Constant for Condition taxonomy.
	 * @var string
     */
    const CONDITION = 'taxonomy.condition';

    /**
     * Constant for Condition configuration.
	 * @var string
     */
    const CONDITION_CONFIG = 'taxonomy.condition.config';

    /**
     * Constant for Product Type taxonomy.
	 * @var string
     */
    const PRODUCT_TYPE = 'taxonomy.product_type';

    /**
     * Constant for Product Type configuration.
	 * @var string
     */
    const PRODUCT_TYPE_CONFIG = 'taxonomy.product_type.config';

    /**
     * Constant for Flag taxonomy.
	 * @var string
     */
    const FLAG = 'taxonomy.flag';

    /**
     * Constant for Flag configuration.
	 * @var string
     */
    const FLAG_CONFIG = 'taxonomy.flag.config';

    /**
     * Constant for Channel taxonomy.
	 * @var string
     */
    const CHANNEL = 'taxonomy.channel';

    /**
     * Constant for Channel configuration.
	 * @var string
     */
    const CHANNEL_CONFIG = 'taxonomy.channel.config';

    /**
     * Constant for Channel synchronization.
	 * @var string
     */
    const CHANNEL_SYNC = 'taxonomy.channel.sync';

    /**
     * Constant for Channel connector.
	 * @var string
     */
    const CHANNEL_CONNECTOR = 'taxonomy.channel.connector';

    /**
     * Constant for Channel admin filter.
	 * @var string
     */
    const CHANNEL_ADMIN_FILTER = 'taxonomy.channel.admin_products_filter';

    /**
     * Constant for Channel query filter.
	 * @var string
     */
    const CHANNEL_QUERY_FILTER = 'taxonomy.channel.query_filter';

    /**
     * Constant for Channel currency filter.
	 * @var string
     */
    const CHANNEL_CURRENCY_FILTER = 'taxonomy.channel.currency_filter';

    /**
     * Constant for Channel BigCommerce status.
	 * @var string
     */
    const CHANNEL_BC_STATUS = 'taxonomy.channel.bc_status';

    /**
     * Constant for Routes related to Channel.
	 * @var string
     */
    const ROUTES = 'taxonomy.channel.routes';


    /**
     * Registers all taxonomies and related actions and filters.
     *
     * @param Container $container The container object.
     */
    public function register( Container $container ) {
        $this->product_category( $container );
        $this->brand( $container );
        $this->availability( $container );
        $this->condition( $container );
        $this->product_type( $container );
        $this->flag( $container );
        $this->channel( $container );

        add_action( 'init', $this->create_callback( 'register', function () use ( $container ) {
            $container[ self::PRODUCT_CATEGORY_CONFIG ]->register();
            $container[ self::BRAND_CONFIG ]->register();
            $container[ self::AVAILABILITY_CONFIG ]->register();
            $container[ self::CONDITION_CONFIG ]->register();
            $container[ self::PRODUCT_TYPE_CONFIG ]->register();
            $container[ self::FLAG_CONFIG ]->register();
            $container[ self::CHANNEL_CONFIG ]->register();
        } ), 0, 0 );
    }

    private function product_category( Container $container ) {
        $container[ self::PRODUCT_CATEGORY_CONFIG ] = function ( Container $container ) {
            return new Product_Category\Config( Product_Category\Product_Category::NAME, [ Product::NAME ] );
        };

        $container[ self::PRODUCT_CATEGORY_GROUP_FILTERED_TERMS ] = function ( Container $container ) {
            return new Product_Category\Group_Filtered_Terms();
        };

        $container[ self::PRODUCT_CATEGORY_QUERY_FILTER ] = function () use ( $container ) {
            return new Product_Category\Query_Filter( $container[ self::PRODUCT_CATEGORY_GROUP_FILTERED_TERMS ] );
        };

        add_filter( 'get_terms_args', $this->create_callback( 'exclude_product_categories_by_group', function ( $args, $taxonomies ) use ( $container ) {
            if ( ! is_admin() ) {
                $args = $container[ self::PRODUCT_CATEGORY_GROUP_FILTERED_TERMS ]->exclude_hidden_terms( $args, $taxonomies );
            }

            return $args;
        } ), 10, 3 );

        add_action( 'pre_get_posts', $this->create_callback( 'filter_query_by_product_category', function ( $query ) use ( $container ) {
            if ( ! is_admin() ) {
                $container[ self::PRODUCT_CATEGORY_QUERY_FILTER ]->apply( $query );
            }
        } ), 10, 1 );

        add_action( 'parse_tax_query', $this->create_callback( 'hide_children_by_default', function ( $query ) use ( $container ) {
            $container[ self::PRODUCT_CATEGORY_QUERY_FILTER ]->maybe_hide_children( $query );
        } ), 10, 1 );
    }

	private function brand( Container $container ) {
		$container[ self::BRAND_CONFIG ] = function ( Container $container ) {
			return new Brand\Config( Brand\Brand::NAME, [ Product::NAME ] );
		};
	}

	private function availability( Container $container ) {
		$container[ self::AVAILABILITY_CONFIG ] = function ( Container $container ) {
			return new Availability\Config( Availability\Availability::NAME, [ Product::NAME ] );
		};
	}

	private function condition( Container $container ) {
		$container[ self::CONDITION_CONFIG ] = function ( Container $container ) {
			return new Condition\Config( Condition\Condition::NAME, [ Product::NAME ] );
		};
	}

	private function product_type( Container $container ) {
		$container[ self::PRODUCT_TYPE_CONFIG ] = function ( Container $container ) {
			return new Product_Type\Config( Product_Type\Product_Type::NAME, [ Product::NAME ] );
		};
	}

	private function flag( Container $container ) {
		$container[ self::FLAG_CONFIG ] = function ( Container $container ) {
			return new Flag\Config( Flag\Flag::NAME, [ Product::NAME ] );
		};
	}

	private function channel( Container $container ) {
		$this->routes( $container );

		$container[ self::CHANNEL_CONFIG ] = function ( Container $container ) {
			return new Channel\Config( Channel\Channel::NAME, [ Product::NAME, Queue_Task::NAME ] );
		};
		$container[ self::CHANNEL_SYNC ]   = function ( Container $container ) {
			return new Channel\Channel_Synchronizer( $container[ Api::FACTORY ]->channels() );
		};
		$container[ self::CHANNEL_BC_STATUS ] = function ( Container $container ) {
			return new Channel\BC_Status();
		};

		add_action( 'bigcommerce/import/start', function () use ( $container ) {
			$container[ self::CHANNEL_BC_STATUS ]->maybe_cancel_import();
		}, 11, 0 );

		add_action( 'admin_notices', function () use ( $container ) {
			$container[ self::CHANNEL_BC_STATUS ]->admin_notices();
		} );

		// We need a fresh list of channels on Connect Channel screen and on each import
		$channel_sync = $this->create_callback( 'channel_sync', function () use ( $container ) {
			$container[ self::CHANNEL_SYNC ]->sync();
		} );


		add_action( 'bigcommerce/settings/before_form/page=' . Connect_Channel_Screen::NAME, $channel_sync, 10, 0 );
		add_action( 'bigcommerce/import/start', $channel_sync, 10, 0 );
		add_action( 'bigcommerce/settings/before_form/page=' . Settings_Screen::NAME, function () use ( $container ) {
			$container[ self::CHANNEL_SYNC ]->initial_sync();
		}, 10, 0 );

		add_action( 'edited_' . Channel\Channel::NAME, $this->create_callback( 'handle_channel_name_change', function ( $term_id ) use ( $container ) {
			$container[ self::CHANNEL_SYNC ]->handle_name_change( $term_id );
		} ), 10, 1 );


		$container[ self::CHANNEL_CONNECTOR ] = function ( Container $container ) {
			return new Channel_Connector( $container[ Api::FACTORY ]->channels(), $container[ Api::FACTORY ]->store() );
		};

		add_action( 'admin_menu', $this->create_callback( 'create_first_channel', function () use ( $container ) {
			if ( wp_doing_ajax() ) {
				return;
			}
			if ( $container[ Settings::CONFIG_STATUS ] < Settings::STATUS_CHANNEL_CONNECTED
			     && $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_API_CONNECTED
			     && ! empty( get_option( Onboarding_Api::ACCOUNT_ID, '' ) ) ) {
				$container[ self::CHANNEL_CONNECTOR ]->create_first_channel();
			}
		} ), 0, 0 ); // run before menu items are set up

		add_filter( 'sanitize_option_' . Channel_Select::CHANNEL_TERM, $this->create_callback( 'handle_select_channel', function ( $value ) use ( $container ) {
			return $container[ self::CHANNEL_CONNECTOR ]->handle_connect_request( $value );
		} ), 100, 1 );

		add_filter( 'sanitize_option_' . Channel_Settings::NEW_NAME, $this->create_callback( 'handle_create_channel', function ( $value ) use ( $container ) {
			return $container[ self::CHANNEL_CONNECTOR ]->handle_create_request( $value );
		} ), 100, 1 );

		add_filter( 'bigcommerce/settings/api/disabled/field=' . Api_Credentials::OPTION_STORE_URL, $this->create_callback( 'prevent_store_url_changes', function ( $disabled ) use ( $container ) {
			return $container[ self::CHANNEL_CONNECTOR ]->prevent_store_url_changes( $disabled );
		} ), 10, 1 );

		$container[ self::CHANNEL_ADMIN_FILTER ] = function ( Container $container ) {
			return new Channel\Admin_Products_Filter();
		};

		add_action( 'load-edit.php', $this->create_callback( 'init_list_table_hooks', function () use ( $container ) {
			if ( Channel\Channel::multichannel_enabled() ) {
				add_filter( 'restrict_manage_posts', $this->create_callback( 'products_admin_channel_select', function ( $post_type, $which ) use ( $container ) {
					$container[ self::CHANNEL_ADMIN_FILTER ]->display_channel_select( $post_type, $which );
				} ), 10, 2 );

				add_filter( 'parse_request', $this->create_callback( 'parse_products_admin_request', function ( \WP $wp ) use ( $container ) {
					$container[ self::CHANNEL_ADMIN_FILTER ]->filter_list_table_request( $wp );
				} ), 10, 1 );
			}
		} ), 10, 0 );

		$container[ self::CHANNEL_QUERY_FILTER ] = function ( Container $container ) {
			return new Channel\Query_Filter();
		};

		add_action( 'pre_get_posts', $this->create_callback( 'filter_query_by_channel', function ( $query ) use ( $container ) {
			if ( ! is_admin() && Channel\Channel::multichannel_enabled() ) {
				$container[ self::CHANNEL_QUERY_FILTER ]->apply( $query );
			}
		} ), 10, 1 );

		$container[ self::CHANNEL_CURRENCY_FILTER ] = function ( Container $container ) {
			return new Channel\Currency_Filter();
		};

		add_action( 'pre_option_' . \BigCommerce\Settings\Sections\Currency::CURRENCY_CODE, $this->create_callback( 'filter_channel_currency', function ( $currency_code ) use ( $container ) {
			return $container[ self::CHANNEL_CURRENCY_FILTER ]->filter_currency( $currency_code );
		} ), 5, 1 );
	}

	private function routes( Container $container ) {
		$container[ self::ROUTES ] = function ( Container $container ) {
			return new Channel\Routes( $container[ Api::FACTORY ]->sites(), $container[ Api::FACTORY ]->channels() );
		};

		add_action( 'bigcommerce/channel/updated_channel_id', $this->create_callback( 'set_routes_for_channel', function ( $channel_id ) use ( $container ) {
			$container[ self::ROUTES ]->set_routes( $channel_id );
		} ), 10, 1 );

		add_action( 'bigcommerce/routes/cron/update', $this->create_callback( 'update_routes', function () use ( $container ) {
			$container[ self::ROUTES ]->update_routes();
		} ), 10, 0 );

		$route_changed = $this->create_callback( 'route_changed', function () use ( $container ) {
			$container[ self::ROUTES ]->schedule_update_routes();
		} );

		add_action( 'update_option_show_on_front', $route_changed, 10, 0 );
		add_action( 'add_option_show_on_front', $route_changed, 10, 0 );
		add_action( 'update_option_permalink_structure', $route_changed, 10, 0 );
		add_action( 'add_option_permalink_structure', $route_changed, 10, 0 );
		add_action( 'update_option_' . Cart_Page::NAME, $route_changed, 10, 0 );
		add_action( 'add_option_' . Cart_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Login_Page::NAME, $route_changed, 10, 0 );
		add_action( 'add_option_' . Login_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Account_Page::NAME, $route_changed, 10, 0 );
		add_action( 'add_option_' . Account_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Shipping_Returns_Page::NAME, $route_changed, 10, 0 );
		add_action( 'add_option_' . Shipping_Returns_Page::NAME, $route_changed, 10, 0 );
		add_action( 'update_option_' . Product_Archive::ARCHIVE_SLUG, $route_changed, 10, 0 );
		add_action( 'add_option_' . Product_Archive::ARCHIVE_SLUG, $route_changed, 10, 0 );
		add_action( 'update_option_' . Product_Archive::CATEGORY_SLUG, $route_changed, 10, 0 );
		add_action( 'add_option_' . Product_Archive::CATEGORY_SLUG, $route_changed, 10, 0 );
		add_action( 'update_option_' . Product_Archive::BRAND_SLUG, $route_changed, 10, 0 );
		add_action( 'add_option_' . Product_Archive::BRAND_SLUG, $route_changed, 10, 0 );
		add_action( 'bigcommerce/channel/connection_changed', $route_changed );
		add_action( 'update_option_home', $this->create_callback( 'update_site_home', function () use ( $container ) {
			$container[ self::ROUTES ]->update_site_home();
		} ), 10, 0 );

		add_action( 'post_updated', $this->create_callback( 'update_route_permalink', function ( $post_id, $new_post, $old_post ) use ( $container ) {
			$container[ self::ROUTES ]->update_route_permalink( $post_id, $new_post, $old_post );
		} ), 10, 3 );

		add_action( 'bigcommerce/import/fetched_store_settings', $this->create_callback( 'check_and_update_routes_version', function () use ( $container ) {
			$container[ self::ROUTES ]->maybe_update_routes();
		} ), 10, 0 );

		add_filter( 'bigcommerce/diagnostics', $this->create_callback( 'route_diagnostics', function ( $data ) use ( $container ) {
			return $container[ self::ROUTES ]->diagnostic_data( $data );
		} ), 10, 1 );
	}
}
