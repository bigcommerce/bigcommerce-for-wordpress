<?php

namespace BigCommerce\Merchant;

use Bigcommerce\Api\Resources\ShippingZone;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api_Factory;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Settings\Sections\Cart;

/**
 * Class Setup_Status
 *
 * Gets information about the current setup state of the BigCommerce store
 */
class Setup_Status {

	const STATUS_CACHE     = 'bigcommerce_store_setup_status';
	const STATUS_CACHE_TTL = '600';

	/**
	 * @var Api_Factory
	 */
	private $factory;

	public function __construct( Api_Factory $factory ) {
		$this->factory = $factory;
	}

	/**
	 * Get the current status of the store. Results
	 * are cached in a transient for up to 10 minutes.
	 *
	 * @return array
	 */
	public function get_current_status() {
		$cache = get_transient( self::STATUS_CACHE );

		if ( ! empty( $cache ) && is_array( $cache ) ) {
			return $cache;
		}

		$ssl_status = $this->get_ssl_status();

		$status = [
			'shipping_zones'   => $this->get_shipping_zone_count(),
			'shipping_methods' => $this->get_shipping_method_count(),
			'tax_classes'      => $this->get_tax_class_count(),
			'payment_methods'  => $this->get_payment_methods_count(),
			'ssl'              => $ssl_status,
			'product_count'    => $this->get_product_count(),
			'domain'           => $this->get_domain(),
		];

		set_transient( self::STATUS_CACHE, $status, self::STATUS_CACHE_TTL );

		return $status;
	}

	/**
	 * Refresh the status cache with new data
	 *
	 * @return array
	 */
	public function refresh_status() {
		delete_transient( self::STATUS_CACHE );

		return $this->get_current_status();
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

	private function get_shipping_method_count() {
		$api = $this->factory->shipping();

		return $api->count_shipping_methods();
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

	/**
	 * Indicates whether the WordPress site is using SSL
	 * and sitewide https is enabled in the store.
	 *
	 * @return bool
	 */
	public function get_ssl_status() {
		return is_ssl() && $this->get_store_sitewidehttps_enabled();
	}

	private function get_store_sitewidehttps_enabled() {
		return $this->factory->store()->get_sitewidehttps_enabled();
	}
	
	private function get_domain() {
		return $this->factory->store()->get_domain();
	}

	private function get_product_count() {
		$api = $this->factory->catalog();
		try {
			$response = $api->getProducts( [ 'limit' => 1, 'include_fields' => 'id' ] );

			return (int) $response->getMeta()->getPagination()->getTotal();
		} catch ( ApiException $e ) {
			return 0;
		}
	}

	public function get_product_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/products';
	}

	public function get_shipping_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/settings/shipping';
	}

	public function get_tax_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/settings/tax/tax-general';
	}

	public function get_payment_configuration_url() {
		return 'https://login.bigcommerce.com/deep-links/manage/settings/payment';
	}
	
	public function get_checkout_setup_documentation_url() {
		return 'https://support.bigcommerce.com/s/blog-article/aAn4O000000CdEcSAK/thirdparty-cookies-and-bigcommerce-for-wordpress';
	}

	public function get_required_steps() {
		$status = $this->get_current_status();
		$steps  = [];

		if ( empty( $status['product_count'] ) ) {
			$steps['import'] = [
				'heading' => __( 'Begin Creating and Importing Products in BigCommerce', 'bigcommerce' ),
				'url'     => $this->get_product_configuration_url(),
				'label'   => __( 'Open BigCommerce', 'bigcommerce' ),
				'icon'    => 'tag',
			];
		}

		if ( empty( $status['payment_methods'] ) ) {
			$steps['payment'] = [
				'heading' => __( 'Configure Your Payment Methods', 'bigcommerce' ),
				'url'     => $this->get_payment_configuration_url(),
				'label'   => __( 'Open BigCommerce', 'bigcommerce' ),
				'icon'    => 'currency',
			];
		}

		if ( empty( $status['shipping_zones'] ) ) {
			$steps['shipping'] = [
				'heading' => __( 'Configure Your Shipping Zones', 'bigcommerce' ),
				'url'     => $this->get_shipping_configuration_url(),
				'label'   => __( 'Open BigCommerce', 'bigcommerce' ),
				'icon'    => 'shipping_returns',
			];
		}

		if ( empty( $status['tax_classes'] ) ) {
			$steps['taxes'] = [
				'heading' => __( 'Configure Your Taxes', 'bigcommerce' ),
				'url'     => $this->get_tax_configuration_url(),
				'label'   => __( 'Open BigCommerce', 'bigcommerce' ),
				'icon'    => 'dollar-sign',
			];
		}

		$is_subdomain = isset( $status['domain'] ) && strpos( $status['domain'], parse_url( get_home_url(), PHP_URL_HOST ) ) !== false;
		
		if ( ! $is_subdomain ) {
			$steps['checkout_url'] = [
				'heading' => __( 'Checkout URL', 'bigcommerce' ),
				'url'     => $this->get_checkout_setup_documentation_url(),
				'label'   => __( 'Learn More', 'bigcommerce' ),
				'icon'    => 'store-front',
			];
		}
			
		/**
		 * Filter the array of next steps required for setting up the
		 * BigCommerce store.
		 *
		 * @var array $steps The required steps. Each item should be an associative array with:
		 *                    - heading - The heading text to display for the step
		 *                    - url     - The URL the step's call to action will link to
		 *                    - label   - The text for the call to action
		 */
		return apply_filters( 'bigcommerce/settings/next-steps/required', $steps );
	}

	public function get_optional_steps() {
		$status = $this->get_current_status();
		$steps  = [];

		if ( ! $status['ssl'] ) {
			$steps['ssl'] = [
				'heading' => __( 'Add SSL Certificate and enable sitewide HTTPS in BigCommerce store for Embedded Checkout', 'bigcommerce' ),
				'icon'    => 'cart',
			];
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->id !== 'bigcommerce_product_page_' . Settings_Screen::NAME ) {
				$steps['settings'] = [
					'heading' => __( 'Customize Your Store Functionality', 'bigcommerce' ),
					'url'     => admin_url( 'edit.php?post_type=' . Product::NAME . '&page=' . Settings_Screen::NAME ),
					'label'   => __( 'Configure', 'bigcommerce' ),
					'icon'    => 'store-front',
				];
			}
		}

		if ( current_user_can( 'customize' ) ) {
			$url = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
			$steps['customizer'] = [
				'heading' => __( 'Customize the Look and Feel of Your Store', 'bigcommerce' ),
				'url'     => add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $url ) ) ), admin_url( 'customize.php' ) ),
				'label'   => __( 'Customize', 'bigcommerce' ),
				'icon'    => 'customize',
			];
		}

		/**
		 * Filter the array of optional next steps for setting up the
		 * BigCommerce store.
		 *
		 * @var array $steps The optional steps. Each item should be an associative array with:
		 *                    - heading - The heading text to display for the step
		 *                    - url     - The URL the step's call to action will link to
		 *                    - label   - The text for the call to action
		 */
		return apply_filters( 'bigcommerce/settings/next-steps/optional', $steps );
	}

	public function new_account() {
		$account_id = get_option( Onboarding_Api::ACCOUNT_ID, '' );

		return ! empty( $account_id );
	}
}
