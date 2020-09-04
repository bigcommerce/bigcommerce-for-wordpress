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
		$message = sprintf( esc_html__( 'By end of September, BC4WP 4.2.0 will require PHP %s+ and WP %s+. Unfortunately, your site does not meet these requirements and may not function is correctly if you upgrade. While we will gradually increase the minimum required PHP version each BC4WP release, until we get to PHP %s+, please do not attempt to upgrade to BC4WP 4.0+ unless you also upgrade your PHP and WP versions.', 'bigcommerce' ), BIGCOMMERCE_PHP_OPTIMAL_VERSION, BIGCOMMERCE_WP_OPTIMAL_VERSION, BIGCOMMERCE_PHP_OPTIMAL_VERSION );
		$link = " <a href=\"https://support.bigcommerce.com/s/blog-article/aAn4O000000CdFBSA0/important-changes-for-bigcommerce-for-wordpress-40\" target=\"_blank\">Learn more</a>";
		$script = 
		<<<EOL
			<script>
				function getCookie(name) {
					var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
					return v ? v[2] : null;
				}
				
				function setCookie(name, value, days) {
					var d = new Date;
					d.setTime(d.getTime() + 24*60*60*1000*days);
					document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
				}

				setTimeout(function(){
					document.querySelector('.bc4wp-server-notice .notice-dismiss').addEventListener('click', function(){
					  setCookie('bc4wp-server-notice', '1', 365);
					});
				}, 3000);
				
				if (getCookie('bc4wp-server-notice')) {
					document.getElementsByClassName('bc4wp-server-notice')[0].style.display='none';
				}
			</script>
		EOL;
		echo wp_kses_post( sprintf( '<div class="notice is-dismissible bc4wp-server-notice">%s</div>', wpautop( $message . $link ) ) ) . $script;
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

