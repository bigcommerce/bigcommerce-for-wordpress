<?php


namespace BigCommerce\Accounts;

/**
 * Class Countries
 *
 * Handles loading and retrieving country data from a JSON file, and injecting
 * country data into JavaScript configuration arrays.
 */
class Countries {
	private $data_file;
	private $data;

	/**
	 * Countries constructor.
	 *
	 * @param string $data_file The path to the JSON file containing country data.
	 */
	public function __construct( $data_file ) {
		$this->data_file = $data_file;
	}

	/**
	 * Get a list of countries.
	 *
	 * Loads the country data from the JSON file if it hasn't been loaded yet, 
	 * and returns the data as an array.
	 * 
	 * @return array The list of countries loaded from the JSON file.
	 * @filter bigcommerce/countries/data Filter applied to the countries data before returning.
	 */
	public function get_countries() {
		$this->load_data();

		return $this->data;
	}

	/**
	 * Load country data from the JSON file.
	 *
	 * If the country data hasn't already been loaded, this method loads it from the specified
	 * JSON file and decodes it into an array.
	 *
	 * @return void
	 */
	private function load_data() {
		if ( isset( $this->data ) ) {
			return; // Data is already loaded
		}
		$this->data = (array) json_decode( file_get_contents( $this->data_file ) );
	}

	/**
	 * Inject country data into JavaScript configuration.
	 *
	 * Adds the list of countries to the provided JavaScript configuration array under the "countries" key.
	 * 
	 * @param array $config The JavaScript configuration array to modify.
	 * 
	 * @return array The modified JavaScript configuration array.
	 * @filter bigcommerce/js_config Filter applied to the JavaScript configuration before returning.
	 * @filter bigcommerce/admin/js_config Filter applied to the admin JavaScript configuration before returning.
	 */
	public function js_config( $config ) {
		$config[ 'countries' ] = $this->get_countries();

		return $config;
	}
}
