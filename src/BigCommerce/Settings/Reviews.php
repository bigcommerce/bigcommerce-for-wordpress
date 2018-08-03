<?php


namespace BigCommerce\Settings;


use BigCommerce\Api_Factory;

class Reviews extends Settings_Section {

	const NAME = 'product_reviews';

	/**
	 * @var Api_Factory
	 */
	private $api_factory;

	public function __construct( Api_Factory $api_factory ) {
		$this->api_factory = $api_factory;
	}

	/**
	 * @return void
	 * @action bigcommerce/settings/register
	 */
	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			__( 'Product Reviews', 'bigcommerce' ),
			[ $this, 'render_settings_section' ],
			Settings_Screen::NAME
		);
	}

	public function render_settings_section( $section ) {
		$message = __( 'Moderation and management of product reviews within WordPress is scoped for future development.', 'bigcommerce' );
		$link    = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $this->reviews_url() ), __( 'Manage reviews in BigCommerce.', 'bigcommerce' ) );
		printf( '<p>%s %s</p>', $message, $link );
		do_action( 'bigcommerce/settings/render/product_reviews', $section );
	}

	private function reviews_url() {
		$store_url = $this->get_store_url();
		if ( empty( $store_url ) ) {
			return '';
		}
		$url = trailingslashit( $store_url ) . 'manage/products/product-reviews';

		return $url;
	}


	/**
	 * Get the base URL for the BigCommerce store admin
	 *
	 * @return string
	 */
	private function get_store_url() {
		$url = get_transient( 'bigcommerce_store_url' );
		if ( ! empty( $url ) ) {
			return $url;
		}
		try {
			$api   = $this->api_factory->store();
			$store = $api->getStore();
			if ( empty( $store->secure_url ) ) {
				return '';
			}
			set_transient( 'bigcommerce_store_url', $store->secure_url, DAY_IN_SECONDS );

			return $store->secure_url;
		} catch ( \Exception $e ) {
			return '';
		}
	}

}