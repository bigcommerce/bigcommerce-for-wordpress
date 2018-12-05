<?php


namespace BigCommerce\Assets\Admin;


use BigCommerce\Merchant\Account_Status;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;

class JS_Config {
	private $data;
	private $gutenberg;
	private $directory;

	public function __construct( $asset_directory ) {
		$this->directory = trailingslashit( $asset_directory );
	}

	public function get_data() {
		if ( ! isset( $this->data ) ) {
			$this->data = [
				'images_url'                 => $this->directory . 'img/admin/',
				'icons_url'                  => $this->directory . 'img/admin/icons',
				'categories'                 => Product_Category::NAME,
				'flags'                      => Flag::NAME,
				'brands'                     => Brand::NAME,
				'recent'                     => __( 'recent', 'bigcommerce' ),
				'search'                     => __( 'search', 'bigcommerce' ),
				'sort_order'                 => __( 'order', 'bigcommerce' ),
				'admin_ajax'                 => admin_url( 'admin-ajax.php' ),
				'account_rest_nonce'         => wp_create_nonce( Account_Status::STATUS_AJAX ),
				'account_rest_action'        => Account_Status::STATUS_AJAX,
				'diagnostics_ajax_nonce'     => wp_create_nonce( Troubleshooting_Diagnostics::AJAX_ACTION ),
				'diagnostics_ajax_action'    => Troubleshooting_Diagnostics::AJAX_ACTION,
				'diagnostics_section'        => Troubleshooting_Diagnostics::NAME,
				'product_import_ajax_nonce'  => wp_create_nonce( Import_Status::AJAX_ACTION_IMPORT_STATUS ),
				'product_import_ajax_action' => Import_Status::AJAX_ACTION_IMPORT_STATUS,
			];
			$this->data = apply_filters( 'bigcommerce/admin/js_config', $this->data );
		}

		return $this->data;
	}

	public function get_gutenberg_data() {
		if ( ! isset( $this->gutenberg ) ) {
			$this->gutenberg = [];
			$this->gutenberg = apply_filters( 'bigcommerce/gutenberg/js_config', $this->gutenberg );
		}

		return $this->gutenberg;
	}
}
