<?php
/*
Plugin Name:  BigCommerce for WordPress
Description:  Scale your ecommerce business with WordPress on the front-end and BigCommerce on the back end. Free up server resources from things like catalog management, processing payments, and managing fulfillment logistics.
Author:       BigCommerce
Version:      4.22.0
Author URI:   https://www.bigcommerce.com/wordpress
Requires PHP: 7.4.0
Text Domain:  bigcommerce
License:      GPLv2 or later
*/

define( 'BIGCOMMERCE_PHP_MINIMUM_VERSION', '7.0' );
define( 'BIGCOMMERCE_PHP_OPTIMAL_VERSION', '7.4' );
define( 'BIGCOMMERCE_WP_MINIMUM_VERSION', '4.8' );
define( 'BIGCOMMERCE_WP_OPTIMAL_VERSION', '5.8' );

if ( version_compare( PHP_VERSION, BIGCOMMERCE_PHP_MINIMUM_VERSION, '<' ) || version_compare( get_bloginfo( 'version' ), BIGCOMMERCE_WP_MINIMUM_VERSION, '<' ) ) {
	add_action( 'admin_notices', function() {
		$message = sprintf( esc_html__( 'BigCommerce requires PHP version %s+ and WP version %s+, plugin is currently NOT RUNNING.', 'bigcommerce' ), BIGCOMMERCE_PHP_OPTIMAL_VERSION, BIGCOMMERCE_WP_OPTIMAL_VERSION );
		echo wp_kses_post( sprintf( '<div class="error">%s</div>', wpautop( $message ) ) );
	} );

	return;
} elseif ( version_compare( PHP_VERSION, BIGCOMMERCE_PHP_OPTIMAL_VERSION, '<' ) || version_compare( get_bloginfo( 'version' ), BIGCOMMERCE_WP_OPTIMAL_VERSION, '<' ) ) {
	add_action( 'admin_notices', function() {
		$message = sprintf( esc_html__( 'BigCommerce requires PHP version %s+ and WP version %s+', 'bigcommerce' ), BIGCOMMERCE_PHP_OPTIMAL_VERSION, BIGCOMMERCE_WP_OPTIMAL_VERSION );
		echo wp_kses_post( sprintf( '<div class="notice">%s</div>', wpautop( $message ) ) );
	} );
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

register_activation_hook( __FILE__, [ \BigCommerce\Plugin::class, 'activate' ] );

// Start the plugin
add_action( 'plugins_loaded', 'bigcommerce_init', 1, 0 );

/**
 * @return \BigCommerce\Plugin
 */
function bigcommerce_init() {
	// Don't load on frontend for non-active channel status
	if ( ! defined( 'WP_CLI' )
		&& ! is_admin()
		&& bigcommerce_get_primary_channel_status() !== null
		&& bigcommerce_get_primary_channel_status() !== \BigCommerce\Taxonomies\Channel\BC_Status::STATUS_ACTIVE
	) {
		return;
	}

	$container = new \Pimple\Container( [ 'plugin_file' => __FILE__ ] );
	$plugin    = \BigCommerce\Plugin::instance( $container );
	$plugin->init();

	/**
	 * Fires after the plugin has initialized
	 *
	 * @param \BigCommerce\Plugin $plugin    The global instance of the plugin controller
	 * @param \Pimple\Container   $container The plugin's dependency injection container
	 */
	do_action( 'bigcommerce/init', $plugin, $container );

	return $plugin;
}

function bigcommerce() {
	try {
		return \BigCommerce\Plugin::instance();
	} catch ( \Exception $e ) {
		return bigcommerce_init();
	}
}

/**
 * Look for a value in an environment variable, falling back
 * to a constant. This allows configuration options to be set
 * either in the environment or in wp-config.php.
 *
 * @param string $key The name of an environment variable or constant
 *
 * @return mixed The found value. false if not found.
 */
function bigcommerce_get_env( $key ) {
	$value = getenv( $key, true ) ?: getenv( $key );
	if ( $value === false && defined( $key ) ) {
		$value = constant( $key );
	}

	return $value;
}

/**
 * Get the status of the primary channel
 *
 * @return mixed The found value. null if not found.
 */
function bigcommerce_get_primary_channel_status() {
	$cache_key = 'bigcommerce_primary_channel_status';
	$status    = wp_cache_get( $cache_key );
	if ( $status === false ) {
		global $wpdb;

		$sql = "SELECT tm.meta_value
				FROM {$wpdb->termmeta} tm
				INNER JOIN {$wpdb->termmeta} tm2 ON tm2.term_id=tm.term_id
				WHERE tm.meta_key=%s AND tm2.meta_key=%s AND tm2.meta_value=%s";

		$status = $wpdb->get_var( $wpdb->prepare(
			$sql,
			\BigCommerce\Taxonomies\Channel\BC_Status::STATUS,
			\BigCommerce\Taxonomies\Channel\Channel::STATUS,
			\BigCommerce\Taxonomies\Channel\Channel::STATUS_PRIMARY
		) );

		wp_cache_set( $cache_key, $status );
	}

    return $status;
}
