<?php

namespace BigCommerce\Settings;


class Site_URL_Sync {

    protected $routes;
    protected $screen_settings;

    public function __construct( $routes, $screen_settings ) {
        $this->routes          = $routes;
        $this->screen_settings = $screen_settings;
    }

    public function sync() {
        $submission = filter_var_array( $_GET, [
            '_wpnonce' => FILTER_SANITIZE_STRING,
        ] );
        
        if ( empty( $submission['_wpnonce'] ) || ! wp_verify_nonce( $submission['_wpnonce'], Sections\Troubleshooting_Diagnostics::SYNC_SITE_URL ) ) {
            throw new \InvalidArgumentException( __( 'Invalid request. Please try again.', 'bigcommerce' ), 403 );
        }

        $this->routes->update_site_home();

        wp_safe_redirect( esc_url_raw( $this->screen_settings->get_url() ), 303 );
        exit();
    }
}
