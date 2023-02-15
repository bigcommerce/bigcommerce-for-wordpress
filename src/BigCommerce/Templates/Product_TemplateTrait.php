<?php

namespace BigCommerce\Templates;

use BigCommerce\Import\Processors\Store_Settings;
use BigCommerce\Import\Processors\Storefront_Processor;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Flag\Flag;

trait Product_TemplateTrait {

	/**
	 * @param \BigCommerce\Post_Types\Product\Product $product
	 *
	 * @return bool
	 */
	protected function should_hide_prices( Product $product ): bool {
		$should_respect_storefront = Store_Settings::is_msf_on() && ! Channel::is_msf_channel_prop_on( Storefront_Processor::SHOW_PRODUCT_PRICE );

		return has_term( Flag::HIDE_PRICE, Flag::NAME, $product->post_id() ) || $should_respect_storefront;
	}

	/**
	 * @param \BigCommerce\Post_Types\Product\Product $product
	 *
	 * @return string
	 */
	public function get_price( Product $product ): string {
		if ( $this->should_hide_prices( $product ) ) {
			$component = Product_Hidden_Price::factory( [
				Product_Hidden_Price::PRODUCT => $product,
			] );
		} else {
			$component = Product_Price::factory( [
				Product_Price::PRODUCT => $product,
			] );
		}

		return $component->render();
	}

	/**
	 * @param \BigCommerce\Post_Types\Product\Product $product
	 *
	 * @return string
	 */
	protected function get_sku( Product $product ): string {
		if ( ! Channel::is_msf_channel_prop_on( Storefront_Processor::SHOW_PRODUCT_SKU ) ) {
			return '';
		}

		$component = Product_Sku::factory( [
			Product_Sku::PRODUCT => $product,
		] );

		return $component->render();
	}

	/**
	 * @param \BigCommerce\Post_Types\Product\Product $product
	 *
	 * @return string
	 */
	protected function get_brand( Product $product ): string {
		if ( ! Channel::is_msf_channel_prop_on( Storefront_Processor::SHOW_PRODUCT_BRAND ) ) {
			return '';
		}

		$component = Product_Brand::factory( [
				Product_Brand::PRODUCT => $product,
		] );

		return $component->render();
	}

}
