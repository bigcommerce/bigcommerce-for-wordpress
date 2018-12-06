<?php

namespace BigCommerce\Amp;

/**
 * Class Classic
 *
 * Specific customizations for the classic AMP mode.
 *
 * @package BigCommerce\Amp
 */
class Classic {

	/**
	 * Register the AMP Header menu.
	 */
	public function register_amp_menu() {
		register_nav_menu( 'amp-menu', 'AMP Hamburger Menu' );
	}
}
