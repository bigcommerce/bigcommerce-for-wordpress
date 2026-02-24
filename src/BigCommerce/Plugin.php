<?php


namespace BigCommerce;

class Plugin {
	const VERSION = '5.1.2';

	protected static $_instance;

	/** @var \Pimple\Container */
	protected $container = null;

	/**
	 * @var Container\Provider[]
	 */
	private $providers = [];

	/**
	 * @param \Pimple\Container $container
	 */
	public function __construct( \Pimple\Container $container ) {
		$this->container = $container;
	}

	public function __get( $property ) {
		if ( array_key_exists( $property, $this->providers ) ) {
			return $this->providers[ $property ];
		}

		return null;
	}

	public function init() {
		$this->load_libraries();
		$this->load_functions();
		$this->load_service_providers();
	}

	private function load_libraries() {
	}

	private function load_functions() {
		// Get relative path to this file
		$reviews_file = __DIR__ . '/Functions/reviews.php';

		require_once $reviews_file;
	}

	private function load_service_providers() {
		$this->providers[ 'logger' ]            = new Container\Log();
		$this->providers[ 'accounts' ]          = new Container\Accounts();
		$this->providers[ 'amp' ]               = new Container\Amp();
		$this->providers[ 'analytics' ]         = new Container\Analytics();
		$this->providers[ 'api' ]               = new Container\Api();
		$this->providers[ 'assets' ]            = new Container\Assets();
		$this->providers[ 'cart' ]              = new Container\Cart();
		$this->providers[ 'channels' ]          = new Container\Merchant();
		$this->providers[ 'checkout' ]          = new Container\Checkout();
		$this->providers[ 'cli' ]               = new Container\Cli();
		$this->providers[ 'compat' ]            = new Container\Compatibility();
		$this->providers[ 'currency' ]          = new Container\Currency();
		$this->providers[ 'customizer' ]        = new Container\Theme_Customizer();
		$this->providers[ 'editor' ]            = new Container\Editor();
		$this->providers[ 'forms' ]             = new Container\Forms();
		$this->providers[ 'gift_certificates' ] = new Container\Gift_Certificates();
		$this->providers[ 'import' ]            = new Container\Import();
		$this->providers[ 'nav' ]               = new Container\Nav_Menu();
		$this->providers[ 'pages' ]             = new Container\Pages();
		$this->providers[ 'post_types' ]        = new Container\Post_Types();
		$this->providers[ 'post_meta' ]         = new Container\Post_Meta();
		$this->providers[ 'proxy' ]             = new Container\Proxy();
		$this->providers[ 'rest' ]              = new Container\Rest();
		$this->providers[ 'graphql' ]           = new Container\GraphQL();
		$this->providers[ 'reviews' ]           = new Container\Reviews();
		$this->providers[ 'rewrites' ]          = new Container\Rewrites();
		$this->providers[ 'schema' ]            = new Container\Schema();
		$this->providers[ 'settings' ]          = new Container\Settings();
		$this->providers[ 'taxonomies' ]        = new Container\Taxonomies();
		$this->providers[ 'shortcodes' ]        = new Container\Shortcodes();
		$this->providers[ 'templates' ]         = new Container\Templates();
		$this->providers[ 'widgets' ]           = new Container\Widgets();
		$this->providers[ 'webhooks' ]          = new Container\Webhooks();
		$this->providers[ 'util' ]              = new Container\Util();
		$this->providers[ 'banners' ]           = new Container\Banners();
		$this->providers[ 'images' ]            = new Container\Image();


		/**
		 * Filter the service providers the power the plugin
		 *
		 * @param Container\Provider[] $providers
		 */
		$this->providers = apply_filters( 'bigcommerce/plugin/providers', $this->providers );

		foreach ( $this->providers as $provider ) {
			$this->container->register( $provider );
		}
	}

	public function container() {
		return $this->container;
	}

	/**
	 * @return string The URL for the plugin's root directory, with a trailing slash
	 */
	public function plugin_dir_url() {
		return plugin_dir_url( $this->container()[ 'plugin_file' ] );
	}

	/**
	 * @return string The file system path for the plugin's root directory, with a trailing slash
	 */
	public function plugin_dir_path() {
		return plugin_dir_path( $this->container()[ 'plugin_file' ] );
	}

	/**
	 * Determines is API credentials have been set, a prerequisite
	 * for much of the plugin functionality.
	 *
	 * @return bool
	 */
	public function credentials_set() {
		/**
		 * Filters the credentials set
		 *
		 * @param boolean $set false
		 */
		return apply_filters( 'bigcommerce/plugin/credentials_set', false );
	}

	/**
	 * @param null|\ArrayAccess $container
	 *
	 * @return self
	 * @throws \Exception
	 */
	public static function instance( $container = null ) {
		if ( ! isset( self::$_instance ) ) {
			if ( empty( $container ) ) {
				throw new \Exception( 'You need to provide a Pimple container' );
			}

			$className       = __CLASS__;
			self::$_instance = new $className( $container );
		}

		return self::$_instance;
	}

	public static function activate() {
		if ( is_network_admin() ) {
			return; // network activated
		}
		if ( ! is_admin() ) {
			return; // not loaded via a normal admin screen
		}

		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		$action = $wp_list_table->current_action();
		if ( $action === 'activate-selected' ) {
			return; // multiple plugins activated
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return; // activated via CLI
		}

		set_transient( 'bigcommerce_activation_redirect', 1, 30 );
	}
}
