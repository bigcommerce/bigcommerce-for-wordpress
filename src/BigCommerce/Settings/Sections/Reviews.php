<?php


namespace BigCommerce\Settings\Sections;

use BigCommerce\Settings\Screens\Settings_Screen;

class Reviews extends Settings_Section {

	const NAME = 'product_reviews';

	/**
	 * @return void
	 * @action bigcommerce/settings/register/screen= . Settings_Screen::NAME
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
		return 'https://login.bigcommerce.com/deep-links/manage/products/product-reviews';
	}

}