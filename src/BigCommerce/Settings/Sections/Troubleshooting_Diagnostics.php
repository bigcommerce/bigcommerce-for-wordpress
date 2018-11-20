<?php

namespace BigCommerce\Settings\Sections;


use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Pages\Cart_Page;
use BigCommerce\Pages\Gift_Certificate_Page;
use BigCommerce\Pages\Registration_Page;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Screens\Settings_Screen;
use \BigCommerce\Customizer\Sections\Product_Archive;

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

	const AJAX_ACTION = 'bigcommerce_support_data';

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
			self::DIAGNOSTICS_NAME,
			esc_html( __( 'Troubleshooting Diagnostics', 'bigcommerce' ) ),
			[ $this, 'render_field' ],
			Settings_Screen::NAME,
			self::NAME,
			[
				'type'        => 'text',
				'option'      => self::DIAGNOSTICS_NAME,
				'label'       => __( 'Troubleshooting and Diagnostics', 'bigcommerce' ),
				'description' => __( 'The following is information about your WordPress install that may be helpful information to provide to BigCommerce Customer Support in the event that issues arise and you require help troubleshooting.', 'bigcommerce' ),
			]
		);

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
		$diagnostics     = [
			[
				'label' => __( 'WordPress Installation', 'bigcommerce' ),
				'value' => [
					[
						'label' => __( 'Site URL', 'bigcommerce' ),
						'value' => get_site_url(),
					],
					[
						'label' => __( 'WP version', 'bigcommerce' ),
						'value' => get_bloginfo( 'version' ),
					],
					[
						'label' => __( 'Multisite', 'bigcommerce' ),
						'value' => is_multisite() ? 'yes' : 'No',
					],
					[
						'label' => __( 'Permalinks', 'bigcommerce' ),
						'value' => get_option( 'permalink_structure' ),
					],
					[
						'label' => __( 'Plugins Active', 'bigcommerce' ),
						'value' => $this->get_plugin_active_names(),
					],
					[
						'label' => __( 'Network Plugins Active', 'bigcommerce' ),
						'value' => is_multisite() ? $this->get_plugin_network_active_names() : [],
					],
					[
						'label' => __( 'Must Have Plugins', 'bigcommerce' ),
						'value' => $this->get_mu_plugin_names(),
					],
				],
			],
			[
				'label' => __( 'Server Environment', 'bigcommerce' ),
				'value' => [
					[
						'label' => __( 'PHP Version', 'bigcommerce' ),
						'value' => phpversion(),
					],
					[
						'label' => __( 'Max Execution Time', 'bigcommerce' ),
						'value' => ini_get( 'max_execution_time' ),
					],
					[
						'label' => __( 'Memory Limit', 'bigcommerce' ),
						'value' => ini_get( 'memory_limit' ),
					],
					[
						'label' => __( 'Upload Max Filesize', 'bigcommerce' ),
						'value' => ini_get( 'upload_max_filesize' ),
					],
					[
						'label' => __( 'Post Max Size', 'bigcommerce' ),
						'value' => ini_get( 'post_max_size' ),
					],
					[
						'label' => __( 'WP debug', 'bigcommerce' ),
						'value' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No',
					],
					[
						'label' => __( 'WP debug display', 'bigcommerce' ),
						'value' => ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) ? 'Yes' : 'No',
					],
					[
						'label' => __( 'WP debug log', 'bigcommerce' ),
						'value' => ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ? 'Yes' : 'No',
					],
					[
						'label' => __( 'Mysql Version', 'bigcommerce' ),
						'value' => $wpdb->db_version(),
					],
					[
						'label' => __( 'Web Server', 'bigcommerce' ),
						'value' => $_SERVER[ "SERVER_SOFTWARE" ],
					],
				],
			],
			[
				'label' => __( 'BigCommerce Plugin', 'bigcommerce' ),
				'value' => [
					[
						'label' => __( 'Channel ID', 'bigcommerce' ),
						'value' => get_option( Channels::CHANNEL_ID ),
					],
					[
						'label' => __( 'Store ID', 'bigcommerce' ),
						'value' => get_option( Onboarding_Api::STORE_ID, '' ),
					],
					[
						'label' => __( 'Api URL', 'bigcommerce' ),
						'value' => get_option( Api_Credentials::OPTION_STORE_URL ),
					],
					[
						'label' => __( 'Import Frequency', 'bigcommerce' ),
						'value' => get_option( Import::OPTION_FREQUENCY ),
					],
					[
						'label' => __( 'Import Status', 'bigcommerce' ),
						'value' => $this->previous_status()[ 'status' ],
					],
					[
						'label' => __( 'Last Import', 'bigcommerce' ),
						'value' => $this->previous_status()[ 'time_date' ],
					],
					[
						'label' => __( 'Next Import', 'bigcommerce' ),
						'value' => $this->next_status()[ 'time_date' ],
					],
					[
						'label' => __( 'Products Count', 'bigcommerce' ),
						'value' => $products_amount,
					],
					[
						'label' => __( 'Cart Enabled', 'bigcommerce' ),
						'value' => get_option( Cart::OPTION_ENABLE_CART ) ? true : false,
					],
					[
						'label' => __( 'Cart Slug Page', 'bigcommerce' ),
						'value' => get_post_field( 'post_name', get_option( Cart_Page::NAME ) ),
					],
					[
						'label' => __( 'Archive Slug', 'bigcommerce' ),
						'value' => get_option( Product_Archive::ARCHIVE_SLUG ),
					],
					[
						'label' => __( 'Account Page Slug', 'bigcommerce' ),
						'value' => get_post_field( 'post_name', get_option( Registration_Page::NAME ) ),
					],
					[
						'label' => __( 'Gift Certificate Page Slug', 'bigcommerce' ),
						'value' => get_post_field( 'post_name', get_option( Gift_Certificate_Page::NAME ) ),
					],
					[
						'label' => __( 'Template Overrides', 'bigcommerce' ),
						'value' => $this->get_template_overrides(),
					],
				],
			],
		];
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

		return $file_override_list;
	}


	/**
	 * @param string $folder  folder Location
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
}
