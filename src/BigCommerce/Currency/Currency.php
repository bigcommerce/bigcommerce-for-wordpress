<?php


namespace BigCommerce\Currency;

use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Settings\Sections\Currency as Currency_Settings;
use BigCommerce\Taxonomies\Channel\Connections;

/**
 * Handles currency operations, including fetching and setting currency codes and managing channel-aware currencies.
 */
class Currency {

    /**
     * The name of the cookie used to store the currency code.
     */
    const CURRENCY_CODE_COOKIE = 'wp-bigcommerce-currency-code';

    /**
     * Retrieves the current currency code.
     *
     * Determines the currency code based on user preferences, cookies, or channel-specific defaults.
     *
     * @return string The current currency code.
     */
    public function get_currency_code() {
        $default_currency_code = get_option( Currency_Settings::CURRENCY_CODE, 'USD' );
        $channels              = new Connections();
        $current               = $channels->current();
        $channel_default_code  = get_term_meta( $current->term_id, Currency_Settings::CHANNEL_CURRENCY_CODE, true );
        if ( ! get_option( Currency_Settings::ENABLE_CURRENCY_SWITCHER, false ) && empty( $channel_default_code ) ) {
            return $default_currency_code;
        } elseif ( ! get_option( Currency_Settings::ENABLE_CURRENCY_SWITCHER, false ) && ! empty( $channel_default_code ) ) {
            return $channel_default_code;
        }

        $currency_code = '';
        if ( is_user_logged_in() ) {
            $user_currency_code = get_user_meta( get_current_user_id(), Currency_Settings::CURRENCY_CODE, true );
            if ( $user_currency_code ) {
                $currency_code = $user_currency_code;
            }
        }

        if ( empty( $currency_code ) ) {
            $cookie = filter_input( INPUT_COOKIE, self::CURRENCY_CODE_COOKIE, FILTER_SANITIZE_STRING );
            if ( $cookie ) {
                $currency_code = $cookie;
            }
        }

        $enabled = $this->enabled_currencies();
        if ( isset( $enabled[ $currency_code ] ) ) {
            return $currency_code;
        }

        return $channel_default_code;
    }

    /**
     * Sets the currency code for the current user and stores it in a cookie.
     *
     * @param string $currency_code The currency code to set.
     *
     * @return bool True if the currency code was successfully set; otherwise, false.
     */
    public function set_currency_code( $currency_code ) {
        $enabled = $this->enabled_currencies();
        if ( ! isset( $enabled[ $currency_code ] ) ) {
            return false;
        }

        $user_id = get_current_user_id();

        if ( $user_id ) {
            update_user_meta( $user_id, Currency_Settings::CURRENCY_CODE, $currency_code );
        }

        $this->set_currency_code_cookie( $currency_code );

        return true;
    }

    /**
     * Sets a cookie to store the currency code.
     *
     * @param string $currency_code The currency code to store in the cookie.
     *
     * @return void
     */
    public function set_currency_code_cookie( $currency_code ) {
        /**
         * Filters the lifetime of the currency code cookie.
         *
         * @param int $lifetime The cookie lifespan in seconds.
         */
        $cookie_life = apply_filters( 'bigcommerce/currency/cookie_lifetime', 30 * DAY_IN_SECONDS );
        $secure      = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
        setcookie( self::CURRENCY_CODE_COOKIE, $currency_code, time() + $cookie_life, COOKIEPATH, COOKIE_DOMAIN, $secure );
        $_COOKIE[ self::CURRENCY_CODE_COOKIE ] = $currency_code;
    }

    private function enabled_currencies() {
        return get_option( Currency_Settings::ENABLED_CURRENCIES, [] );
    }

    /**
     * Retrieves channel-aware currencies for the current store or channel.
     *
     * Filters the enabled currencies based on the current channel's allowed currencies.
     *
     * @return array The list of channel-aware currencies.
     */
    public function get_channel_aware_currencies(): array {
        $store_currencies = $this->enabled_currencies();
        if ( ! Store_Settings::is_msf_on() ) {
            return $store_currencies;
        }

        $channels                   = new Connections();
        $current                    = $channels->current();
        $channel_currencies_allowed = get_term_meta( $current->term_id, Currency_Settings::CHANNEL_ALLOWED_CURRENCY, true );

        if ( empty( $channel_currencies_allowed ) ) {
            return $store_currencies;
        }

        $currencies = array_filter( $store_currencies, function( $currency ) use ( $channel_currencies_allowed ) {
            return in_array( $currency['currency_code'], $channel_currencies_allowed );
        } );

        if ( empty( $currencies ) ) {
            return $store_currencies;
        }

        return $currencies;
    }
}
