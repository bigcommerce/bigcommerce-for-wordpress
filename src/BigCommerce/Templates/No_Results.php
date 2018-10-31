<?php


namespace BigCommerce\Templates;

use BigCommerce\Post_Types\Product\Product;

Class No_Results extends Controller {
	const NO_RESULTS_MESSAGE        = 'no_results_message';
	const RESET_BUTTON_LABEL        = 'reset_button_label';
	const PRODUCT_ARCHIVE_PERMALINK = 'product_archive_permalink';

	protected $template = 'components/catalog/no-results.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::NO_RESULTS_MESSAGE        => '',
			self::RESET_BUTTON_LABEL        => '',
			self::PRODUCT_ARCHIVE_PERMALINK => '',
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {

		return [
			self::NO_RESULTS_MESSAGE        => $this->get_no_results_message(),
			self::RESET_BUTTON_LABEL        => $this->get_reset_button_label(),
			self::PRODUCT_ARCHIVE_PERMALINK => get_post_type_archive_link( Product::NAME ),
		];
	}

	protected function get_no_results_message() {
		return __( 'Sorry, no products match your search. Please modify your search or reset your query.', 'bigcommerce' );
	}

	protected function get_reset_button_label() {
		return __( 'Reset Filters', 'bigcommerce' );
	}
}
