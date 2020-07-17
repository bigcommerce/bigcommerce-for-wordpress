<?php

namespace BigCommerce\Settings\Sections;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Gift_Certificate_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Customizer\Sections\Product_Archive;

/**
 * Class Troubleshooting_Diagnostics
 *
 * @package BigCommerce\Settings\Sections
 */
class Troubleshooting_Diagnostics extends Settings_Section {

	const NAME             = 'bigcommerce_diagnostics';
	const DIAGNOSTICS_ID   = 'bigcommerce_diagnostics_id';
	const DIAGNOSTICS_NAME = 'bigcommerce_diagnostics_name';
	const TEXTBOX_NAME     = 'bigcommerce_diagnostics_output';
	const LOG_ERRORS       = 'bigcommerce_diagnostics_log_import_errors';
	const SYNC_SITE_URL    = 'bigcommerce_diagnostics_sync_site_url';

	const AJAX_ACTION               = 'bigcommerce_support_data';
	const AJAX_ACTION_IMPORT_ERRORS = 'bigcommerce_import_errors_log';

	/**
	 * Plugin path
	 *
	 * @var string
	 */
	protected $plugin_path;

	public function __construct( $plugin_path ) {
		$this->plugin_path = $plugin_path;
	}

	/*
	 * Add settings section self::NAME
	 * Register it into Settings_Screen::NAME
	 */
	public function register_settings_section() {

		add_settings_section(
			self::NAME,
			__( 'Diagnostics', 'bigcommerce' ),
			'__return_false',
			Settings_Screen::NAME
		);

		add_settings_field(
			self::SYNC_SITE_URL,
			esc_html( __( 'Sync Site URL', 'bigcommerce' ) ),
			[ $this, 'render_sync_site_url', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'        => 'checkbox',
				'option'      => self::SYNC_SITE_URL,
				'label'       => __( 'Sync Site URL', 'bigcommerce' ),
				'description' => __( 'Manually sync site URL with BigCommerce.', 'bigcommerce' ),
			]
		);

		register_setting(
			Settings_Screen::NAME,
			self::LOG_ERRORS
		);
		
		add_settings_field(
			self::LOG_ERRORS,
			esc_html( __( 'Log import errors', 'bigcommerce' ) ),
			[ $this, 'render_enable_import_errors', ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'        => 'checkbox',
				'option'      => self::LOG_ERRORS,
				'label'       => esc_html( __( 'Enable Error Logs', 'bigcommerce' ) ),
				'description' => esc_html( __( 'If enabled, will log import error messages to /wp-content/uploads/logs/bigcommerce/debug.log. If you want to use a different path please define BIGCOMMERCE_DEBUG_LOG in your wp-config.php with the desired writeable path.', 'bigcommerce' ) ),
			]
		);

		add_settings_field(
			self::DIAGNOSTICS_NAME,
			esc_html( __( 'Troubleshooting Diagnostics', 'bigcommerce' ) ),
			[ $this, 'render_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'        => 'text',
				'option'      => self::DIAGNOSTICS_NAME,
				'label'       => esc_html( __( 'Troubleshooting and Diagnostics', 'bigcommerce' ) ),
				'description' => esc_html( __( 'The following is information about your WordPress install that may be helpful information to provide to BigCommerce Customer Support in the event that issues arise and you require help troubleshooting.', 'bigcommerce' ) ),
			]
		);


	}

	public function render_sync_site_url( $args ) {
		$url  = add_query_arg( [ 'action' => self::SYNC_SITE_URL, '_wpnonce' => wp_create_nonce( self::SYNC_SITE_URL ) ], admin_url( 'admin-post.php' ) );
		$link = sprintf( '<a href="%s" class="bc-admin-btn">%s</a>', esc_url( $url ), esc_attr( $args[ 'label' ] ) );
		printf( '%s<p class="description">%s</p>', $link, esc_html( $args[ 'description' ] ) );
	}

	/**
	 * @param array $args
	 */
	public function render_field( $args ) {
		if ( ! empty( $args[ 'description' ] ) ) {
			printf( '<p>%s</p>', esc_html( $args[ 'description' ] ) );
		}

		printf( '<div class="bc-diagnostics-data">
			<button type="button" class="bc-admin-btn" data-js="bc-admin-get-diagnostics">%s</button>
			<p class="bc-admin-diagnostics-loader">%s <span class="spinner is-active bc-admin-spinner"></span></p>
			</div>',
			'Get Diagnostics',
			'Gathering information about your store...'
		);
	}

	public function render_enable_import_errors( $args ) {
		$value    = (bool) get_option( $args[ 'option' ], false );
		$checkbox = sprintf( '<label for="%2$s"><input type="%1$s" value="1" class="regular-text code" name="%2$s" id="%2$s" %3$s/> %4$s</label>', esc_attr( $args[ 'type' ] ), esc_attr( $args[ 'option' ] ), checked( true, $value, false ), esc_attr( $args[ 'label' ] ) );
		printf( '%s<p class="description">%s</p>', $checkbox, esc_html( $args[ 'description' ] ) );
	}

	/**
	 * Sets a wp_send_json answer for the ajax call that holds
	 * information about the plugin and the hosting system
	 *
	 * @action wp_ajax_ . self::AJAX_ACTION
	 */
	public function get_diagnostics_data() {
		// Validate request nonce
		$this->validate_ajax_nonce( $_REQUEST );

		global $wpdb;

		$products_total  = wp_count_posts( Product::NAME );
		$products_amount = $products_total->publish + $products_total->draft;
		$server_software = filter_input( INPUT_SERVER, 'SERVER_SOFTWARE', FILTER_SANITIZE_STRING );
		$diagnostics     = [
			[
				'label' => __( 'WordPress Installation', 'bigcommerce' ),
				'key'   => 'wordpress',
				'value' => [
					[
						'label' => __( 'Site URL', 'bigcommerce' ),
						'key'   => 'siteurl',
						'value' => get_site_url(),
					],
					[
						'label' => __( 'WP version', 'bigcommerce' ),
						'key'   => 'wpversion',
						'value' => get_bloginfo( 'version' ),
					],
					[
						'label' => __( 'Multisite', 'bigcommerce' ),
						'key'   => 'multisite',
						'value' => is_multisite() ? 'yes' : 'No',
					],
					[
						'label' => __( 'Permalinks', 'bigcommerce' ),
						'key'   => 'permalinks',
						'value' => get_option( 'permalink_structure' ),
					],
					[
						'label' => __( 'Plugins Active', 'bigcommerce' ),
						'key'   => 'plugins',
						'value' => $this->get_plugin_active_names(),
					],
					[
						'label' => __( 'Network Plugins Active', 'bigcommerce' ),
						'key'   => 'networkplugins',
						'value' => is_multisite() ? $this->get_plugin_network_active_names() : [],
					],
					[
						'label' => __( 'Must Have Plugins', 'bigcommerce' ),
						'key'   => 'muplugins',
						'value' => $this->get_mu_plugin_names(),
					],
				],
			],
			[
				'label' => __( 'Server Environment', 'bigcommerce' ),
				'key'   => 'server',
				'value' => [
					[
						'label' => __( 'PHP Version', 'bigcommerce' ),
						'key'   => 'phpversion',
						'value' => phpversion(),
					],
					[
						'label' => __( 'Max Execution Time', 'bigcommerce' ),
						'key'   => 'max_execution_time',
						'value' => ini_get( 'max_execution_time' ),
					],
					[
						'label' => __( 'Memory Limit', 'bigcommerce' ),
						'key'   => 'memory_limit',
						'value' => ini_get( 'memory_limit' ),
					],
					[
						'label' => __( 'Upload Max Filesize', 'bigcommerce' ),
						'key'   => 'upload_max_filesize',
						'value' => ini_get( 'upload_max_filesize' ),
					],
					[
						'label' => __( 'Post Max Size', 'bigcommerce' ),
						'key'   => 'post_max_size',
						'value' => ini_get( 'post_max_size' ),
					],
					[
						'label' => __( 'WP debug', 'bigcommerce' ),
						'key'   => 'wp_debug',
						'value' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No',
					],
					[
						'label' => __( 'WP debug display', 'bigcommerce' ),
						'key'   => 'wp_debug_display',
						'value' => ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) ? 'Yes' : 'No',
					],
					[
						'label' => __( 'WP debug log', 'bigcommerce' ),
						'key'   => 'wp_debug_log',
						'value' => ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ? 'Yes' : 'No',
					],
					[
						'label' => __( 'Mysql Version', 'bigcommerce' ),
						'key'   => 'mysqlversion',
						'value' => $wpdb->db_version(),
					],
					[
						'label' => __( 'Web Server', 'bigcommerce' ),
						'key'   => 'webserver',
						'value' => $server_software,
					],
				],
			],
			[
				'label' => __( 'BigCommerce Plugin', 'bigcommerce' ),
				'key'   => 'bigcommerce',
				'value' => [
					[
						'label' => __( 'Channel ID', 'bigcommerce' ),
						'key'   => 'channelid',
						'value' => get_option( Channels::CHANNEL_ID ),
					],
					[
						'label' => __( 'Store ID', 'bigcommerce' ),
						'key'   => 'storeid',
						'value' => get_option( Onboarding_Api::STORE_ID, '' ),
					],
					[
						'label' => __( 'Api URL', 'bigcommerce' ),
						'key'   => 'apiurl',
						'value' => get_option( Api_Credentials::OPTION_STORE_URL ),
					],
					[
						'label' => __( 'Import Frequency', 'bigcommerce' ),
						'key'   => 'importfrequency',
						'value' => get_option( Import::OPTION_FREQUENCY ),
					],
					[
						'label' => __( 'Import Status', 'bigcommerce' ),
						'key'   => 'importstatus',
						'value' => $this->previous_status()[ 'status' ],
					],
					[
						'label' => __( 'Last Import', 'bigcommerce' ),
						'key'   => 'lastimport',
						'value' => $this->previous_status()[ 'time_date' ],
					],
					[
						'label' => __( 'Next Import', 'bigcommerce' ),
						'key'   => 'nextimport',
						'value' => $this->next_status()[ 'time_date' ],
					],
					[
						'label' => __( 'Products Count', 'bigcommerce' ),
						'key'   => 'productcount',
						'value' => $products_amount,
					],
					[
						'label' => __( 'Cart Enabled', 'bigcommerce' ),
						'key'   => 'cartenabled',
						'value' => get_option( Cart::OPTION_ENABLE_CART, true ) ? true : false,
					],
					[
						'label' => __( 'Cart Page Slug', 'bigcommerce' ),
						'key'   => 'cartpage',
						'value' => get_post_field( 'post_name', get_option( Cart_Page::NAME ) ),
					],
					[
						'label' => __( 'Archive Slug', 'bigcommerce' ),
						'key'   => 'archiveslug',
						'value' => get_option( Product_Archive::ARCHIVE_SLUG ),
					],
					[
						'label' => __( 'Category Slug', 'bigcommerce' ),
						'key'   => 'categoryslug',
						'value' => get_option( Product_Archive::CATEGORY_SLUG ),
					],
					[
						'label' => __( 'Brand Slug', 'bigcommerce' ),
						'key'   => 'brandslug',
						'value' => get_option( Product_Archive::BRAND_SLUG ),
					],
					[
						'label' => __( 'Account Page Slug', 'bigcommerce' ),
						'key'   => 'accountslug',
						'value' => get_post_field( 'post_name', get_option( Registration_Page::NAME ) ),
					],
					[
						'label' => __( 'Gift Certificate Page Slug', 'bigcommerce' ),
						'key'   => 'giftcertificateslug',
						'value' => get_post_field( 'post_name', get_option( Gift_Certificate_Page::NAME ) ),
					],
					[
						'label' => __( 'Template Overrides', 'bigcommerce' ),
						'key'   => 'templateoverrides',
						'value' => $this->get_template_overrides(),
					],
				],
			],
		];

		/**
		 * Filter the list of diagnostic data
		 *
		 * @param array $diagnostics
		 */
		$diagnostics = apply_filters( 'bigcommerce/diagnostics', $diagnostics );

		// Send response
		wp_send_json( $diagnostics );

		// Just in case
		wp_die( '', '', [ 'response' => null ] );
	}


	/**
	 * @return array List of active plugins
	 */
	private function get_plugin_network_active_names() {
		$active_plugins = (array) get_site_option( 'active_sitewide_plugins', [] );
		if ( empty( $active_plugins ) ) {
			return [];
		}

		$plugins        = get_plugins();
		$plugin_names   = [];
		$active_plugins = array_keys( $active_plugins );
		sort( $active_plugins );

		foreach ( $active_plugins as $plugin ) {
			if ( ! validate_file( $plugin ) // $plugin must validate as file
			     && '.php' == substr( $plugin, - 4 ) // $plugin must end with '.php'
			     && file_exists( WP_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist
			) {
				if ( isset( $plugins[ WP_PLUGIN_DIR . '/' . $plugin ] ) ) {
					$plugin_names[] = [
						'label' => $plugins[ $plugin ][ 'Name' ],
						'value' => $plugins[ $plugin ][ 'Version' ],
					];
				}
			}
		}

		return $plugin_names;
	}

	/**
	 * @return array List of network wide active plugins
	 */
	private function get_plugin_active_names() {
		$activated_plugins = get_option( 'active_plugins' );
		$plugins           = get_plugins();
		$plugin_names      = [];

		foreach ( $activated_plugins as $plugin ) {
			if ( isset( $plugins[ $plugin ] ) ) {
				$plugin_names[] = [
					'label' => $plugins[ $plugin ][ 'Name' ],
					'value' => $plugins[ $plugin ][ 'Version' ],
				];
			}

		}

		return $plugin_names;
	}

	/**
	 * @return array List of active Must Have plugins
	 */
	private function get_mu_plugin_names() {
		$activated_mu_plugins = get_mu_plugins();
		if ( empty( $activated_mu_plugins ) ) {
			return [];
		}

		$plugin_names = [];

		foreach ( $activated_mu_plugins as $plugin ) {
			$plugin_names[] = [
				'label' => $plugin[ 'Name' ],
				'value' => $plugin[ 'Version' ],
			];
		}

		return $plugin_names;
	}


	/**
	 * @return array A list of template overrides in the active theme
	 */
	private function get_template_overrides() {
		$theme_override_dir = apply_filters( 'bigcommerce/template/directory/theme', '', '' );

		$theme_parent_dir_path = trailingslashit( TEMPLATEPATH ) . $theme_override_dir;
		$theme_child_dir_path  = trailingslashit( STYLESHEETPATH ) . $theme_override_dir;

		// If none exists bail out sooner
		if ( ( ! file_exists( $theme_parent_dir_path ) ) && ( ! file_exists( $theme_child_dir_path ) ) ) {
			return [];
		}

		$file_override_list = [];

		if ( $theme_parent_dir_path == $theme_child_dir_path ) {
			if ( file_exists( $theme_parent_dir_path ) ) {
				$file_override_list = $this->scan_folder( $theme_parent_dir_path );
			}
		} else {
			if ( file_exists( $theme_parent_dir_path ) ) {
				$file_override_list = $this->scan_folder( $theme_parent_dir_path );
			}
			if ( file_exists( $theme_child_dir_path ) ) {
				$file_override_list = array_merge( $file_override_list, $this->scan_folder( $theme_child_dir_path ) );
			}
		}

		return $this->check_template_overrides( $file_override_list );
	}

	/**
	 * Check template override versions
	 *
	 * @param array $overrides
	 * @return array A list of template overrides in the active theme with notes
	 */
	private function check_template_overrides( $overrides ) {
		
		$messages = [];

		// Get original template for each override and compare versions.
		foreach ( $overrides as $override ) {
			$original_template_path = $this->get_original_template_path_from_override( $override );

			$override_version = $this->get_template_version( WP_CONTENT_DIR .'/'. $override );
			$original_version = $this->get_template_version( $original_template_path );
			// Cast to int to get only the major version.
			$override_version_major = (int) $override_version;
			$original_version_major = (int) $original_version;

			if ( $original_version_major > $override_version_major ) {
				$override = "{$override} - <strong>Version out of date:</strong> {$override_version} - [Core version: {$original_version}]";
			}

			$messages[] = $override;
		}

		return $messages;
	}

	/**
	 * Read template file version
	 *
	 * @param string $template_path
	 * @return string
	 */
	private function get_template_version( $template_path ) {

		if ( ! file_exists( $template_path ) ) {
			return '';
		}

		$re = '/@version\s+(\S+)/';
		$contents = file_get_contents( $template_path );
		preg_match( $re, $contents, $match );

		if ( isset( $match[1] ) ) {
			return $match[1];
		}

		return '1.0.0';
	}

	/**
	 * Get original plugin template path
	 *
	 * @param string $override
	 * @return string
	 */
	private function get_original_template_path_from_override( $override ) {
		$theme_override_dir = apply_filters( 'bigcommerce/template/directory/theme', '', '' );

		// Get everything after $theme_override_dir in the string.
		$original_template = substr(
			$override,
			strpos( $override, $theme_override_dir ) + strlen( $theme_override_dir ) + 1
		); 

		return "{$this->plugin_path}templates/public/{$original_template}";
	}


	/**
	 * @param string $folder folder Location
	 *
	 * @return array   File list
	 */
	private function scan_folder( $folder ) {

		$template_overrides = [];
		$files              = scandir( $folder );

		foreach ( $files as $key => $value ) {

			$path = $folder . DIRECTORY_SEPARATOR . $value;

			if ( ! is_dir( $path ) ) {
				if ( '.php' == substr( $path, - 4 ) ) {
					$template_overrides[] = ltrim( str_replace( WP_CONTENT_DIR, '', $path ), '/' );
				}
			} else if ( $value != "." && $value != ".." ) {
				$template_overrides = array_merge( $template_overrides, $this->scan_folder( $path ) );
			}
		}

		return $template_overrides;
	}

	/**
	 * @return array The message describing the previous import and the status string
	 */
	private function previous_status() {
		$status   = new Status();
		$previous = $status->previous_status();

		if ( ( int ) $previous[ 'timestamp' ] != 0 ) {
			$datetime = get_date_from_gmt( date( 'Y-m-d H:i:s', (int) $previous[ 'timestamp' ] ) );
		} else {
			$datetime = __( 'This task has not yet run', 'bigcommerce' );
		}

		return [
			'time_date' => $datetime,
			'status'    => $previous[ 'status' ],
		];
	}

	/**
	 * @return array The message describing the next import and the status string
	 */
	private function next_status() {
		$next = wp_next_scheduled( Cron_Runner::START_CRON );
		if ( $next ) {
			$datetime = get_date_from_gmt( date( 'Y-m-d H:i:s', (int) $next ) );
		} else {
			$status  = new Status();
			$current = $status->current_status();
			if ( $current[ 'status' ] === Status::NOT_STARTED ) {
				$datetime = __( 'There is no cron schedule for this task.', 'bigcommerce' );
			} else {
				$datetime = __( 'The import is currently running.', 'bigcommerce' );
			}
		}

		return [ 'time_date' => $datetime ];
	}

	/**
	 * Validate the nonce for an ajax status request
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	private function validate_ajax_nonce( $request ) {
		if ( empty( $request[ '_wpnonce' ] ) || ! wp_verify_nonce( $request[ '_wpnonce' ], self::AJAX_ACTION ) ) {
			wp_send_json_error( [
				'code'    => 'invalid_nonce',
				'message' => __( 'Invalid request.', 'bigcommerce' ),
			] );
			exit();
		}
	}

	/**
	 * Responds a formatted log content
	 *
	 * @param Error_Log $log
	 */
	public function get_import_errors( Error_Log $log ) {
		// Validate request nonce
		$this->validate_ajax_nonce( $_REQUEST );

		// Send response
		wp_send_json( $log->get_log_data() );

		// Just in case
		wp_die( '', '', [ 'response' => null ] );
	}
}
