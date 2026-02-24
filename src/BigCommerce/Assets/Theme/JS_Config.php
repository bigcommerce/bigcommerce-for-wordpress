<?php


namespace BigCommerce\Assets\Theme;

use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Settings\Sections\Currency;

/**
 * Handles the configuration for JavaScript data, which includes store settings,
 * product messages, channel data, and other theme-specific data required for the frontend.
 *
 * @package BigCommerce\Assets\Theme
 */
class JS_Config {
	/**
	 * @var array $data Configuration data for JavaScript.
	 * 
	 * Contains various data elements that will be used in JavaScript files, such as store domain,
	 * product availability messages, channel data, and currency information.
	 */
	private $data;

	/**
	 * @var string $directory Path to the asset directory for the theme.
	 *
	 * This directory is used to locate various assets like images to be referenced in JavaScript.
	 */
	private $directory;

	/**
	 * @var Connections $connections An instance of the Connections class to fetch the current channel data.
	 *
	 * This is used to retrieve the current active channel and its relevant data for JavaScript configuration.
	 */
	private $connections;

	/**
	 * JS_Config constructor.
	 *
	 * @param string $asset_directory The directory path where theme assets are stored.
	 * @param Connections $connections The Connections object used to get channel-related data.
	 */
	public function __construct( $asset_directory, $connections ) {
		$this->directory   = trailingslashit( $asset_directory );
		$this->connections = $connections;
	}

	/**
	 * Retrieves the configuration data for JavaScript.
	 *
	 * This method fetches the data used in JavaScript files, including store domain, product messages,
	 * channel data, currency information, and the logout URL. If the data has already been generated,
	 * it returns the cached version.
	 *
	 * @return array The configuration data for JavaScript.
	 */
	public function get_data() {
		if ( ! isset( $this->data ) ) {
			$this->data = [
				'store_domain'  => get_option( \BigCommerce\Import\Processors\Store_Settings::DOMAIN ),
				'images_url'    => $this->directory . 'img/admin/',
				'product'       => [
					'messages'  => [
						'not_available' => __( 'The selected product combination is currently unavailable.', 'bigcommerce' ),
					],
				],
				'channel'       => $this->get_current_channel_data(),
				'currency_code' => apply_filters( 'bigcommerce/currency/code', 'USD' ),
				'logout_url'    => esc_url( wp_logout_url( '/' ) ),
			];

			/**
			 * Filters Theme Js config.
			 *
			 * @param array $data Theme Js config.
			 */
			$this->data = apply_filters( 'bigcommerce/js_config', $this->data );
		}

		return $this->data;
	}

	private function get_current_channel_data() {
		try {
			$current_channel = $this->connections->current();
			if ( $current_channel ) {
				return [
					'id'   => get_term_meta( $current_channel->term_id, Channel::CHANNEL_ID, true ),
					'name' => $current_channel->name,
				];
			}
		} catch (\Exception $e) {

		}

		return false;
	}

}
