<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Rest\Product_Component_Shortcode_Controller;
use BigCommerce\Settings\Sections\Cart;
use BigCommerce\Shortcodes;

/**
 * A block to display a given product's components.
 */
class Product_Components extends Shortcode_Block {
	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	const NAME = 'bigcommerce/product-components';

	/**
	 * The image component key.
	 *
	 * @var string
	 */
	const IMAGE = 'image';

	/**
	 * The SKU component key.
	 *
	 * @var string
	 */
	const SKU = 'sku';

	/**
	 * The title component key.
	 *
	 * @var string
	 */
	const TITLE = 'title';

	/**
	 * The description component key.
	 *
	 * @var string
	 */
	const DESCRIPTION = 'description';

	/**
	 * The add-to-cart component key.
	 *
	 * @var string
	 */
	const ADD_TO_CART = 'add_to_cart';

	/**
	 * The block's icon.
	 *
	 * @var string
	 */
	protected $icon = 'star-filled';

	/**
	 * The shortcode used for this block.
	 *
	 * @var string
	 */
	protected $shortcode = Shortcodes\Product_Components::NAME;

	/** @var Product_Component_Shortcode_Controller */
	private $shortcode_rest_controller;

	/**
	 * Constructor to initialize the block with assets URL and shortcode controller.
	 *
	 * @param string $assets_url
	 * @param Product_Component_Shortcode_Controller $shortcode_controller
	 */
	public function __construct( $assets_url, Product_Component_Shortcode_Controller $shortcode_controller ) {
		parent::__construct( $assets_url );
		$this->shortcode_rest_controller = $shortcode_controller;
	}

	/**
	 * Returns the block's title.
	 *
	 * @return string The block's title.
	 */
	protected function title() {
		return __( 'BigCommerce Product Components', 'bigcommerce' );
	}

	/**
	 * Returns the HTML title for the block.
	 *
	 * @return string The HTML title.
	 */
	protected function html_title() {
		return __( 'Product Components', 'bigcommerce' );
	}

	/**
	 * Returns the URL of the image associated with the block.
	 *
	 * @return string The image URL.
	 */
	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Register-Form.png' );
	}

	/**
	 * Returns an array of keywords related to the block.
	 *
	 * @return array The block's keywords.
	 */
	protected function keywords() {
		$keywords   = parent::keywords();
		$keywords[] = __( 'components', 'bigcommerce' );

		return $keywords;
	}

	/**
	 * Returns the JavaScript configuration for the block.
	 *
	 * @return array The JavaScript configuration.
	 */
	public function js_config() {
		$config              = parent::js_config();

		$config['preview_url'] = $this->get_preview_url();

		$config['inspector'] = [
			'header'                   => __( 'Component Settings', 'bigcommerce' ),
			'product_id_label'         => __( 'Product ID', 'bigcommerce' ),
			'product_id_description'   => __( 'The product ID from BigCommerce', 'bigcommerce' ),
			'component_id_label'       => __( 'Product Component', 'bigcommerce' ),
			'component_id_description' => __( 'The component you would like to display', 'bigcommerce' ),
			'components'               => [
				[
					'key'   => self::SKU,
					'label' => __( 'SKU', 'bigcommerce' ),
				],
				[
					'key'   => self::IMAGE,
					'label' => __( 'Image', 'bigcommerce' ),
				],
				[
					'key'   => self::TITLE,
					'label' => __( 'Title', 'bigcommerce' ),
				],
				[
					'key'   => self::DESCRIPTION,
					'label' => __( 'Description', 'bigcommerce' ),
				],
				[
					'key'   => self::ADD_TO_CART,
					'label' => get_option( Cart::OPTION_ENABLE_CART, true ) ? __( 'Add to Cart', 'bigcommerce' ) : __( 'Buy Now', 'bigcommerce' ),
				],
			],
		];

		return $config;
	}

	/**
	 * Returns the attributes for the block.
	 *
	 * @return array The block's attributes.
	 */
	protected function attributes() {
		return [
			'shortcode'     => [
				'type' => 'string',
			],
			'productId'     => [
				'type' => 'string',
			],
			'componentType' => [
				'type' => 'string',
			],
		];
	}

	/**
	 * Returns the preview URL for the block.
	 *
	 * @return string The preview URL.
	 */
	protected function get_preview_url() {
		return $this->shortcode_rest_controller->get_base_url() . '/preview';
	}
}
