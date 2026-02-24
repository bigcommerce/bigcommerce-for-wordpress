<?php


namespace BigCommerce\Assets\Admin;


use BigCommerce\Merchant\Account_Status;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Settings\Sections\Import;
use BigCommerce\Settings\Sections\Troubleshooting_Diagnostics;
use BigCommerce\Taxonomies\Brand\Brand;
use BigCommerce\Taxonomies\Flag\Flag;
use BigCommerce\Taxonomies\Product_Category\Product_Category;


/**
 * Handles the configuration for JavaScript assets in the BigCommerce admin.
 * This includes paths to images and icons, as well as various settings related
 * to product categories, flags, brands, and AJAX actions used in the admin interface.
 *
 * @package BigCommerce\Assets\Admin
 */
class JS_Config {
	private $data;
	private $gutenberg;
	private $directory;

    /**
     * Constructor
     *
     * Initializes the JS_Config object with a specified asset directory.
     *
     * @param string $asset_directory The directory containing the assets.
     */
	public function __construct( $asset_directory ) {
		$this->directory = trailingslashit( $asset_directory );
	}

    /**
     * Get JS configuration data
     *
     * Retrieves the JavaScript configuration data, including paths for images and icons,
     * as well as other relevant settings such as product categories and AJAX actions.
     *
     * @return array The JavaScript configuration data.
     */
	public function get_data() {
		if ( ! isset( $this->data ) ) {
			$this->data = [
				'images_url'                 => $this->directory . 'img/admin/',
				'icons_url'                  => $this->directory . 'img/admin/icons',
				'categories'                 => Product_Category::NAME,
				'flags'                      => Flag::NAME,
				'brands'                     => Brand::NAME,
				'headless'                   => ( ( int ) get_option( Import::HEADLESS_FLAG, 0 ) === 1 ),
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

			/**
			 * Filters admin js config object.
			 *
			 * Allows modification of the JS configuration data before it is returned.
			 *
			 * @param array $data Js config data.
			 */
			$this->data = apply_filters( 'bigcommerce/admin/js_config', $this->data );
		}

		return $this->data;
	}

    /**
     * Get Gutenberg JS configuration data
     *
     * Retrieves the Gutenberg-specific JavaScript configuration data.
     *
     * @return array The Gutenberg JavaScript configuration data.
     */
	public function get_gutenberg_data() {
		if ( ! isset( $this->gutenberg ) ) {
			$this->gutenberg = [];

			/**
			 * Filters gutenberg js config data.
			 *
			 * Allows modification of the Gutenberg-specific JS configuration data.
			 *
			 * @param array $gutenberg Js config data.
			 */
			$this->gutenberg = apply_filters( 'bigcommerce/gutenberg/js_config', $this->gutenberg );
		}

		return $this->gutenberg;
	}
}
