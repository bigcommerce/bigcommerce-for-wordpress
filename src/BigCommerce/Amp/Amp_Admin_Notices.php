<?php

namespace BigCommerce\Amp;

use WP_Screen;

/**
 * Creates an admin notice if requirements for AMP + BC or not met.
 * 
 * Handles the display of admin notices related to AMP and BigCommerce integration.
 * Specifically, it checks if AMP is active and whether the site is using SSL, 
 * displaying relevant notices in the WordPress admin interface.
 * 
 * @package BigCommerce\Amp
 */
class Amp_Admin_Notices {
    /**
     * ID of the admin screen on which to display the notice.
     *
     * @var string
     */
    private $screen_id;

    /**
     * Whether the AMP for WordPress plugin is active.
     *
     * @var bool
     */
    private $amp_is_enabled;

    /**
     * Whether the site uses SSL.
     *
     * @var bool
     */
    private $is_ssl;

    /**
     * List of notice items.
     *
     * @var array
     */
    private $notices = [];

    /**
     * Constructor
     *
     * Initializes the Amp_Admin_Notices object with necessary information 
     * including the screen ID, AMP plugin status, and SSL status.
     *
     * @param string $screen_id ID of the admin screen where the notice will appear.
     * @param bool   $amp_is_enabled Whether the AMP plugin is active. Default is false.
     * @param bool   $is_ssl Whether the site uses SSL. Default is null, which checks the current SSL status.
     */
    public function __construct( $screen_id, $amp_is_enabled = false, $is_ssl = null ) {
        $this->screen_id      = $screen_id;
        $this->amp_is_enabled = $amp_is_enabled;
        $this->is_ssl         = is_bool( $is_ssl ) ? $is_ssl : is_ssl();
    }

    /**
     * Provides the instance's notices.
     *
     * @return array List of notices to be displayed.
     */
    public function get_notices() {
        return $this->notices;
    }

    /**
     * Adds a notice to the instance's notices list.
     *
     * @param string $notice The notice message to be added.
     */
    public function add_notice( $notice ) {
        if ( ! in_array( $notice, $this->notices, true ) ) {
            $this->notices[] = $notice;
        }
    }

    /**
     * Adds the SSL notice if applicable.
     *
     * This notice is shown if the AMP plugin is active but the site does not use SSL, 
     * informing the user of the importance of using HTTPS for BigCommerce features.
     */
    private function ssl_notice() {
        if ( $this->is_ssl ) {
            return;
        }

        $notice = sprintf(
            // Translators: Placeholder is an external URL about HTTPS.
            __(
                'This site is using <a href="https://wordpress.org/plugins/amp/">AMP</a>, the official AMP plugin for WordPress. HTTPS is required for some features of BigCommerce for WordPress to work correctly with AMP. Read about updating to HTTPS <a href="%s">here</a>.',
                'bigcommerce'
            ),
            /**
             * Filters the URL with information about updating the current site to HTTPS.
             *
             * @param string URL.
             */
            apply_filters(
                'bigcommerce/amp/https_help_url',
                esc_url( 'https://make.wordpress.org/support/user-manual/web-publishing/https-for-wordpress/' )
            )
        );

        /**
         * Filters the AMP SSL notice text.
         *
         * @param string
         */
        $notice = apply_filters( 'bigcommerce/amp/amp_ssl_notice', $notice );

        $this->add_notice( $notice );
    }

    /**
     * Displays an admin notice if AMP is active and SSL is not enabled.
     *
     * This method renders the AMP-related admin notices in the WordPress dashboard.
     * It checks whether AMP is active, whether the current screen matches the provided 
     * screen ID, and whether the SSL notice should be displayed.
     */
    public function render_amp_admin_notices() {
        if ( ! $this->amp_is_enabled ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! is_a( $screen, WP_Screen::class ) || $this->screen_id !== $screen->id ) {
            return;
        }

        $this->ssl_notice();

        /**
         * Filters AMP admin notices.
         *
         * @param array Array of string messages.
         */
        if ( empty( apply_filters( 'bigcommerce/amp/admin_notices', $this->notices ) ) ) {
            return;
        }

        $list = sprintf(
            '<ul class="bigcommerce-notice__list">%s</ul>',
            implode(
                '',
                array_map(
                    function ( $message ) {
                        return sprintf( '<li class="bigcommerce-notice__list-item">%s</li>', $message );
                    },
                    $this->notices
                )
            )
        );

        echo wp_kses_post(
            sprintf(
                '<div class="notice notice-error bigcommerce-notice"><h3 class="bigcommerce-notice__heading">%s</h3>%s</div>',
                __( 'AMP and BigCommerce', 'bigcommerce' ),
                $list
            )
        );
    }
}
