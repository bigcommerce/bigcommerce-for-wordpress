<?php


namespace BigCommerce\Checkout;

use BigCommerce\Merchant\Setup_Status;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Screens\Onboarding_Complete_Screen;
use BigCommerce\Settings\Screens\Settings_Screen;

/**
 * Shows a notice if the required configuration for checkout is not complete
 */
class Requirements_Notice {
    /**
     * Action for refreshing the checkout requirements status cache.
	 * @var string
     */
    const REFRESH = 'bigcommerce_checkout_requirements_refresh';

    /**
     * @var Setup_Status Handles the setup status for the BigCommerce store.
     */
    private $status;

    /**
     * Constructor to initialize the setup status.
     *
     * @param Setup_Status $status Instance of Setup_Status to manage the store's setup status.
     */
    public function __construct( Setup_Status $status ) {
        $this->status = $status;
    }

    /**
     * Checks the BigCommerce API to verify that checkout requirements
     * have been met. Displays admin notices if requirements are not satisfied.
     *
     * @return void
     * @action admin_notices
     */
	public function check_requirements() {
		$status  = $this->status->get_current_status();
		$notices = [];

		if ( empty( $status['shipping_zones'] ) ) {
			$notices[] = sprintf(
				__( 'Shipping Zones have not been set up. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->status->get_shipping_configuration_url() ), __( 'Configure Shipping', 'bigcommerce' ) )
			);
		}
		if ( empty( $status['tax_classes'] ) ) {
			$notices[] = sprintf(
				__( 'Taxes have not been set up. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->status->get_tax_configuration_url() ), __( 'Configure Taxes', 'bigcommerce' ) )
			);
		}
		if ( empty( $status['payment_methods'] ) ) {
			$notices[] = sprintf(
				__( 'Payment methods have not been set up. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->status->get_payment_configuration_url() ), __( 'Configure Payment', 'bigcommerce' ) )
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

		$screen = get_current_screen();

		if ( $screen && $screen->id === 'bigcommerce_product_page_' . Onboarding_Complete_Screen::NAME ) {
			// no notice
		} elseif ( $screen && $screen->post_type != Product::NAME ) {
			$notice = sprintf(
				__( 'Please complete the outstanding requirements to finish setting up your BigCommerce store. %s', 'bigcommerce' ),
				sprintf( '<a href="%s">%s</a>', esc_url( $this->get_settings_dashboard_url() ), __( 'View more', 'bigcommerce' ) )
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
				'bigcommerce'
			);
			$list          = sprintf( '<ul class="bigcommerce-notice__list">%s</ul>', implode( '', array_map( function ( $message ) {
				return sprintf( '<li class="bigcommerce-notice__list-item">%s</li>', $message );
			}, $notices ) ) );
			printf(
				'<div class="notice notice-error bigcommerce-notice"><p class="bigcommerce-notice__refresh"><a class="bigcommerce-notice__refresh-button" href="%s"><i class="bc-icon icon-bc-sync"></i> %s</a></p><h3 class="bigcommerce-notice__heading">%s</h3>%s</div>',
				esc_url( $this->refresh_url() ),
				esc_html( __( 'Refresh', 'bigcommerce' ) ),
				$notice_header,
				$list
			);
		}
	}

	private function get_settings_dashboard_url() {
		return admin_url( 'edit.php?post_type=' . Product::NAME . '&page=' . Settings_Screen::NAME );
	}

    /**
     * Admin post handler to refresh the checkout requirements status cache.
     *
     * @return void
     * @action admin_post_ . self::REFRESH
     */
	public function refresh_status() {
		check_admin_referer( self::REFRESH );

		$this->status->refresh_status();

		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			wp_safe_redirect( esc_url_raw( $_REQUEST['redirect_to'] ), 303 );
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
			$redirect = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
		}
		$url = add_query_arg( [ 'redirect_to' => urlencode( $redirect ) ], $url );
		$url = wp_nonce_url( $url, self::REFRESH );

		return $url;
	}

    /**
     * Disables embedded checkout if SSL is not supported.
     *
     * @param int|bool $option Current option value.
     *
     * @return int|bool Modified option value.
     * @filter pre_option_ . Cart::OPTION_EMBEDDED_CHECKOUT
     */
    public function filter_embedded_checkout( $option ) {
        if ( ! $this->can_enable_embedded_checkout() ) {
            return 0; // not `false`, because WP would ignore it
        }

        return $option;
    }

    /**
     * Determines if embedded checkout can be enabled.
     *
     * @return bool True if SSL is supported, false otherwise.
     * @filter bigcommerce/checkout/can_embed
     */
    public function can_enable_embedded_checkout() {
        return (bool) $this->status->is_ssl();
    }
}
