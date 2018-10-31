<?php


namespace BigCommerce\Accounts;


class Countries {
	private $data_file;
	private $data;

	public function __construct( $data_file ) {
		$this->data_file = $data_file;
	}

	/**
	 * @return array
	 * @filter bigcommerce/countries/data
	 */
	public function get_countries() {
		$this->load_data();

		return $this->data;
	}

	private function load_data() {
		if ( isset( $data ) ) {
			return; // already loaded
		}
		$this->data = (array) json_decode( file_get_contents( $this->data_file ) );
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 * @filter bigcommerce/js_config
	 * @filter bigcommerce/admin/js_config
	 */
	public function js_config( $config ) {
		$config[ 'countries' ] = $this->get_countries();

		return $config;
	}
}