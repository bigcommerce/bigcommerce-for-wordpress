<?php


namespace BigCommerce\Assets\Theme;

use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;
use BigCommerce\Settings\Sections\Currency;

class JS_Config {
	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var string
	 */
	private $directory;

	/**
	 * @var Connections
	 */
	private $connections;

	public function __construct( $asset_directory, $connections ) {
		$this->directory   = trailingslashit( $asset_directory );
		$this->connections = $connections;
	}

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
				'currency_code' => get_option( Currency::CURRENCY_CODE, 'USD' ),
			];
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
