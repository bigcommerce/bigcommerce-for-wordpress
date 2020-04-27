<?php


namespace BigCommerce\Compatibility\Themes\Flatsome\Templates;

use BigCommerce\Templates\Controller;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;


class Account_Links extends Controller {
	const LINKS = 'links';

	protected $template = 'compatibility/themes/flatsome/myaccount/account-links.php';


	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data                = $this->options;
		$data[ self::LINKS ] = $this->get_links();
		return $data;
	}

	protected function get_links() {

		$links = [
			[
				'title' => __( 'Order History', 'bigcommerce'),
				'url'   => get_permalink( get_option( Orders_Page::NAME, 0 ) ),
			],
			[
				'title' => __( 'Addresses', 'bigcommerce'),
				'url'   => get_permalink( get_option( Address_Page::NAME, 0 ) ),
			],
		];

		if ( get_option( Wishlist_Settings::ENABLED ) ) {
			$links[] = [
				'title' => __( 'Wish Lists', 'bigcommerce'),
				'url'   => get_permalink( get_option( Wishlist_Page::NAME, 0 ) ),
			];
		}

		return $links;
	}

}