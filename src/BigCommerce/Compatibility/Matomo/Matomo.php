<?php

namespace BigCommerce\Compatibility\Matomo;


class Matomo {

    /**
	 * @param array $config
	 *
	 * @return array
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
