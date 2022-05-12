<?php

namespace BigCommerce\Post_Types\Product;

class WPGraph_Config {

	const STRING_TYPE  = 'String';
	const INTEGER_TYPE = 'Int';
	const FLOAT_TYPE   = 'Float';
	const BOOLEAN_TYPE = 'Boolean';

	/**
	 * Return a list of product WPGQL props and types
	 *
	 * @return array
	 */
	public function get_product_props_description(): array {
		return [
			'name'                      => self::STRING_TYPE,
			'type'                      => self::STRING_TYPE,
			'sku'                       => self::STRING_TYPE,
			'description'               => self::STRING_TYPE,
			'weight'                    => self::INTEGER_TYPE,
			'width'                     => self::INTEGER_TYPE,
			'depth'                     => self::INTEGER_TYPE,
			'height'                    => self::INTEGER_TYPE,
			'price'                     => self::FLOAT_TYPE,
			'cost_price'                => self::FLOAT_TYPE,
			'retail_price'              => self::FLOAT_TYPE,
			'sale_price'                => self::FLOAT_TYPE,
			'tax_class_id'              => self::INTEGER_TYPE,
			'product_tax_code'          => self::STRING_TYPE,
			'inventory_level'           => self::INTEGER_TYPE,
			'inventory_warning_level'   => self::INTEGER_TYPE,
			'inventory_tracking'        => self::STRING_TYPE,
			'fixed_cost_shipping_price' => self::FLOAT_TYPE,
			'is_free_shipping'          => self::BOOLEAN_TYPE,
			'is_visible'                => self::BOOLEAN_TYPE,
			'is_featured'               => self::BOOLEAN_TYPE,
			'warranty'                  => self::STRING_TYPE,
			'bin_picking_number'        => self::STRING_TYPE,
			'upc'                       => self::STRING_TYPE,
			'search_keywords'           => self::STRING_TYPE,
			'availability'              => self::STRING_TYPE,
			'availability_description'  => self::STRING_TYPE,
			'sort_order'                => self::INTEGER_TYPE,
			'condition'                 => self::STRING_TYPE,
			'is_condition_shown'        => self::BOOLEAN_TYPE,
			'order_quantity_minimum'    => self::INTEGER_TYPE,
			'order_quantity_maximum'    => self::INTEGER_TYPE,
			'page_title'                => self::STRING_TYPE,
			'preorder_message'          => self::STRING_TYPE,
			'is_preorder_only'          => self::BOOLEAN_TYPE,
			'is_price_hidden'           => self::BOOLEAN_TYPE,
			'price_hidden_label'        => self::STRING_TYPE,
			'calculated_price'          => self::FLOAT_TYPE,
			'bc_id'                     => self::INTEGER_TYPE,
			'reviews_rating_sum'        => self::FLOAT_TYPE,
			'reviews_count'             => self::INTEGER_TYPE,
			'date_created'              => self::STRING_TYPE,
			'date_modified'             => self::STRING_TYPE,
			'base_variant_id'           => self::INTEGER_TYPE,
		];
	}

	/**
	 * Return a list of variants WPGQL props and types
	 *
	 * @return array
	 */
	public function get_variants_fields_description(): array {
		return [
			'variant_id'       => self::INTEGER_TYPE,
			'inventory'        => self::INTEGER_TYPE,
			'disabled'         => self::BOOLEAN_TYPE,
			'disabled_message' => self::STRING_TYPE,
			'sku'              => self::STRING_TYPE,
			'price'            => self::FLOAT_TYPE,
		];
	}

	/**
	 * Return a list of variant options WPGQL props and types
	 *
	 * @return array
	 */
	public function get_options_fields_description(): array {
		return [
			'label'               => self::STRING_TYPE,
			'option_id'           => self::INTEGER_TYPE,
			'option_display_name' => self::STRING_TYPE,
		];
	}
}
