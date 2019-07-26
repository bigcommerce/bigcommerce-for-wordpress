<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

use BigCommerce\Rest\Product_Component_Shortcode_Controller;
use BigCommerce\Settings\Sections\Cart;
use BigCommerce\Shortcodes;

/**
 * Class Product_Components
 *
 * A block to display a given products components
 */
class Product_Components extends Shortcode_Block {
	const NAME        = 'bigcommerce/product-components';
	const IMAGE       = 'image';
	const SKU         = 'sku';
	const TITLE       = 'title';
	const DESCRIPTION = 'description';
	const ADD_TO_CART = 'add_to_cart';

	protected $icon      = 'star-filled';
	protected $shortcode = Shortcodes\Product_Components::NAME;

	/** @var Product_Component_Shortcode_Controller */
	private $shortcode_rest_controller;

	public function __construct( $assets_url, Product_Component_Shortcode_Controller $shortcode_controller ) {
		parent::__construct( $assets_url );
		$this->shortcode_rest_controller = $shortcode_controller;
	}

	protected function title() {
		return __( 'BigCommerce Product Components', 'bigcommerce' );
	}

	protected function html_title() {
		return __( 'Product Components', 'bigcommerce' );
	}

	protected function html_image() {
		return $this->image_url( 'Gutenberg-Block_Register-Form.png' );
	}

	protected function keywords() {
		$keywords   = parent::keywords();
		$keywords[] = __( 'components', 'bigcommerce' );

		return $keywords;
	}

	/**
	 * @return array
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

	protected function get_preview_url() {
		return $this->shortcode_rest_controller->get_base_url() . '/preview';
	}
}
