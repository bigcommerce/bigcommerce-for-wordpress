<?php


namespace BigCommerce\Assets\Theme;

class JS_Config {
	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var string
	 */
	private $directory;

	public function __construct( $asset_directory ) {
		$this->directory       = trailingslashit( $asset_directory );
	}

	public function get_data() {
		if ( ! isset( $this->data ) ) {
			$this->data = [
				'store_domain' => get_option( \BigCommerce\Import\Processors\Store_Settings::DOMAIN ),
				'images_url'   => $this->directory . 'img/admin/',
				'product'      => [
					'messages' => [
						'not_available' => __( 'The selected product combination is currently unavailable.', 'bigcommerce' ),
					],
				],
			];
			$this->data = apply_filters( 'bigcommerce/js_config', $this->data );
		}

		return $this->data;
	}
}
