<?php


namespace BigCommerce\Assets\Admin;


use BigCommerce\Merchant\Account_Status;
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
				'images_url'          => $this->directory . 'img/admin/',
				'icons_url'           => $this->directory . 'img/admin/icons',
				'categories'          => Product_Category::NAME,
				'flags'               => Flag::NAME,
				'brands'              => Brand::NAME,
				'recent'              => __( 'recent', 'bigcommerce' ),
				'search'              => __( 'search', 'bigcommerce' ),
				'sort_order'          => __( 'order', 'bigcommerce' ),
				'account_rest_nonce'  => wp_create_nonce( Account_Status::STATUS_AJAX ),
				'account_rest_action' => Account_Status::STATUS_AJAX,
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
