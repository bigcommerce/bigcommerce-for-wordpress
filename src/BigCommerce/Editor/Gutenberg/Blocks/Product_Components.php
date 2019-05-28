<?php

namespace BigCommerce\Editor\Gutenberg\Blocks;

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

	protected $icon = 'star-filled';
	protected $shortcode = Shortcodes\Product_Components::NAME;

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
		$keywords = parent::keywords();
		$keywords[] = __( 'components', 'bigcommerce' );
		return $keywords;
	}

	/**
	 * @return array
	 */
	public function js_config() {
		$config = parent::js_config();
		$config[ 'inspector' ] = [
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
}
