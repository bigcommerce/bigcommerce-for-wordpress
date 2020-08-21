<?php
/*
Plugin Name:  BigCommerce for WordPress
Description:  Scale your ecommerce business with WordPress on the front-end and BigCommerce on the back end. Free up server resources from things like catalog management, processing payments, and managing fulfillment logistics.
Author:       BigCommerce
Version:      3.23.0
Author URI:   https://www.bigcommerce.com/wordpress
Requires PHP: 5.6.24
Text Domain:  bigcommerce
License:      GPLv2 or later
*/

define( 'BIGCOMMERCE_PHP_OPTIMAL_VERSION', '7.2' );
define( 'BIGCOMMERCE_WP_OPTIMAL_VERSION', '5.2' );

if ( version_compare( PHP_VERSION, BIGCOMMERCE_PHP_OPTIMAL_VERSION, '<' ) || version_compare( get_bloginfo( 'version' ), BIGCOMMERCE_WP_OPTIMAL_VERSION, '<' ) ) {
	add_action( 'admin_notices', function() {
		$message = sprintf( esc_html__( 'The upcoming 4.0 release of BC4WP, will support PHP %s+ and WP %s+. If your infrastructure is not at or above those standards, updating the plugin may result in functionality breakage or errors on your site.', 'bigcommerce' ), BIGCOMMERCE_PHP_OPTIMAL_VERSION, BIGCOMMERCE_WP_OPTIMAL_VERSION );
		echo wp_kses_post( sprintf( '<div class="notice is-dismissible">%s</div>', wpautop( $message ) ) );
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

