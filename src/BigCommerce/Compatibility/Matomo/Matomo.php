<?php

namespace BigCommerce\Compatibility\Matomo;


/**
 * This class integrates BigCommerce with Matomo (formerly Piwik), a web analytics platform. It provides functionality for 
 * modifying the JavaScript configuration for tracking user interactions with custom variables in Matomo.
 * The class specifically hooks into the `bigcommerce/js_config` filter to add custom variables to the JavaScript configuration.
 *
 * @package BigCommerce
 * @subpackage Compatibility
 */
class Matomo {

    /**
     * Adds custom variables to the JavaScript configuration for Matomo tracking.
     *
     * This method hooks into the `bigcommerce/js_config` filter and modifies the configuration array to include Matomo's 
     * custom variables. By default, it includes a custom variable for the BigCommerce channel (BC_Channel), which can be extended 
     * for more custom tracking variables.
     *
     * @param array $config The existing JavaScript configuration array that can be modified.
     *
     * @return array The modified configuration array with the added Matomo custom variables.
     *
     * @filter bigcommerce/js_config
     */
	public function js_config( $config ) {
		$config['matomo'] = [
			// Matomo supports 5 custom variables
			'custom_variables' => [
				'var_1' => [
					'id'   => 1,
					'name' => 'BC_Channel',
				],
			],
		];

		return $config;
	}

}
