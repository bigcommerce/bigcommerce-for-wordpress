<?php

namespace BigCommerce\Amp;

/**
 * Class Classic
 *
 * Provides customizations for the classic AMP mode, including
 * registering specific menus for AMP compatibility.
 *
 * @package BigCommerce\Amp
 */
class Classic {

    /**
     * Registers the AMP Header menu.
     *
     * This menu is used for the AMP hamburger menu in classic AMP mode.
     *
     * @return void
     */
    public function register_amp_menu() {
        register_nav_menu( 'amp-menu', 'AMP Hamburger Menu' );
    }
}
