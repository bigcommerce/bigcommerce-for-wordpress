<?php


namespace BigCommerce\Checkout;

use Bigcommerce\Api\Resources\ShippingZone;
use BigCommerce\Api_Factory;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Screens\Settings_Screen;

/**
 * Class Requirements_Notice
 *
 * Shows a notice if the required configuration for checkout is not complete
 */
class Requirements_Notice {
	const STATUS_CACHE     = 'bigcommerce_checkout_requirements';
	const STATUS_CACHE_TTL = '3600';
	const REFRESH          = 'bigcommerce_checkout_requirements_refresh';

	/**
	 * @var Api_Factory
	 */
	private $factory;

	public function __construct( Api_Factory $factory ) {
		$this->factory = $factory;
	}

	/**
	 * Checks the BigCommerce API to verify that checkout requirements
	 * have been met.
	 *
	 * @return void
	 * @action admin_notices
	 */
	public function check_requirements() {
		$status              = $this->get_current_status();
		$notices             = [];

		if ( empty( $status[ 'shipping_zones' ] ) ) {
			$notices[] = sprintf(
				__( 'Shipping has not been set up. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->get_shipping_configuration_url() ), __( 'Configure Shipping', 'bigcommerce' ) )
			);
		}
		if ( empty( $status[ 'tax_classes' ] ) ) {
			$notices[] = sprintf(
				__( 'Taxes have not been set up. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->get_tax_configuration_url() ), __( 'Configure Taxes', 'bigcommerce' ) )
			);
		}
		if ( empty( $status[ 'payment_methods' ] ) ) {
			$notices[] = sprintf(
				__( 'Payment methods have not been set up. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->get_payment_configuration_url() ), __( 'Configure Payment', 'bigcommerce' ) )
			);
		}
		/* 2018-11-07: We have opted not to show a notification for a missing SSL certificate. It's not _really
		 * required unless embedded checkout is enabled */
		//if ( empty( $status[ 'ssl' ] ) ) {
		//	$notices[] = __( 'An SSL certificate on your domain is required.', 'bigcommerce' );
		//}
		
		if ( empty( $notices ) ) {
			return;
		}

		if ( get_current_screen()->post_type != Product::NAME ) {
			$notice = sprintf(
				__( 'Please complete the outstanding requirements to finish setting up your BigCommerce store. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->get_settings_dashboard_url()), __( 'View more', 'bigcommerce' ) )
			);
			printf(
				'<div class="notice notice-error bigcommerce-notice">%s</div>',
				$notice
			);
		} else {
			$notice_header = _n(
				'Checkout functionality will not work until this has been configured in BigCommerce.',
				'Checkout functionality will not work until these have been configured in BigCommerce.',
				count( $notices ),
				'bigcommerce');
			$list = sprintf( '<ul class="bigcommerce-notice__list">%s</ul>', implode( '', array_map( function ( $message ) {
				return sprintf( '<li class="bigcommerce-notice__list-item">%s</li>', $message );
			}, $notices ) ) );
			printf(
				'<div class="notice notice-error bigcommerce-notice"><p class="bigcommerce-notice__refresh"><a class="bigcommerce-notice__refresh-button" href="%s"><i class="bc-icon icon-bc-sync"></i> %s</a></p><h3 class="bigcommerce-notice__heading">%s</h3>%s</div>',
				esc_url( $this->refresh_url() ),
				__( 'Refresh', 'bigcommerce' ),
				$notice_header,
				$list
			);
		}
	}

	public function get_current_status() {
		$cache = get_transient( self::STATUS_CACHE );
		if ( ! empty( $cache ) ) {
			return $cache;
		}

		$status = [
			'shipping_zones'  => $this->get_shipping_zone_count(),
			'tax_classes'     => $this->get_tax_class_count(),
			'payment_methods' => $this->get_payment_methods_count(),
			'ssl'             => $this->get_ssl_status(),
		];

		set_transient( self::STATUS_CACHE, $status, self::STATUS_CACHE_TTL );

		return $status;
	}

	private function get_shipping_zone_count() {
		$api = $this->factory->shipping();
		try {
			$zones = array_filter( $api->get_zones() ?: [], function ( ShippingZone $zone ) {
				return $zone->enabled;
			} );

			return count( $zones );
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	private function get_tax_class_count() {
		$api = $this->factory->tax_class();
		try {
			$classes = $api->get_tax_classes();
			if ( ! is_array( $classes ) ) {
				return 0;
			}
			return count( $classes );
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	private function get_payment_methods_count() {
		$api = $this->factory->payments();
		try {
			return $api->get_payment_methods_count();
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	private function get_ssl_status() {
		return is_ssl();
	}

	private function get_shipping_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/settings/shipping';
	}

	private function get_settings_dashboard_url(){
		return admin_url( 'edit.php?post_type=' . Product::NAME.'&page=' . Settings_Screen::NAME );
	}

	private function get_tax_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/settings/tax/tax-general';
	}

	private function get_payment_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/settings/payment';
	}

	/**
	 * Admin post handler to refresh the checkout requirements status cache
	 *
	 * @return void
	 * @action admin_post_ . self::REFRESH
	 */
	public function refresh_status() {
		check_admin_referer( self::REFRESH );

		delete_transient( self::STATUS_CACHE );
		$this->get_current_status();

		if ( ! empty( $_REQUEST[ 'redirect_to' ] ) ) {
			wp_safe_redirect( esc_url_raw( $_REQUEST[ 'redirect_to' ] ), 303 );
		} else {
			wp_safe_redirect( esc_url_raw( admin_url() ), 303 );
		}
		exit();
	}

	/**
	 * Get the URL to trigger a status cache refresh
	 *
	 * @param string $redirect Redirect destination after refreshing the status cache
	 *
	 * @return string
	 */
	private function refresh_url( $redirect = '' ) {
		$url = admin_url( 'admin-post.php' );
		$url = add_query_arg( [
			'action' => self::REFRESH,
		], $url );
		if ( empty( $redirect ) ) {
			$redirect = $_SERVER[ 'REQUEST_URI' ];
		}
		$url = add_query_arg( [ 'redirect_to' => urlencode( $redirect ) ], $url );
		$url = wp_nonce_url( $url, self::REFRESH );

		return $url;
	}

	/**
	 * Disable embedded checkout if SSL is not supported
	 *
	 * @param int|bool $option
	 *
	 * @return int|bool
	 * @filter pre_option_ . Cart::OPTION_EMBEDDED_CHECKOUT
	 */
	public function filter_embedded_checkout( $option ) {
		if ( ! $this->can_enable_embedded_checkout() ) {
			return 0; // not `false`, because WP would ignore it
		}

		return $option;
	}

	/**
	 * @return bool
	 * @filter bigcommerce/checkout/can_embed
	 */
	public function can_enable_embedded_checkout() {
		return (bool) $this->get_ssl_status();
	}
}