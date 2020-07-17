<?php
/*
Plugin Name:  BigCommerce for WordPress
Description:  Scale your ecommerce business with WordPress on the front-end and BigCommerce on the back end. Free up server resources from things like catalog management, processing payments, and managing fulfillment logistics.
Author:       BigCommerce
Version:      3.22.0
Author URI:   https://www.bigcommerce.com/wordpress
Requires PHP: 5.6.24
Text Domain:  bigcommerce
License:      GPLv2 or later
*/

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

