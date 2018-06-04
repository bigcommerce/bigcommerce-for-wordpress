<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Shortcode_Single_Preview extends Product_Shortcode_Single {

	protected function get_title( Product $product ) {
		$component = new Product_Title( [
			Product_Title::PRODUCT => $product,
			Product_Title::SHOW_CONDITION => false,
			Product_Title::SHOW_INVENTORY => false,
			Product_Title::USE_PERMALINK  => false,
		] );

		return $component->render();
	}

	protected function get_description( Product $product ) {
		$component = new Product_Description( [
			Product_Description::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_price( Product $product ) {
		$component = new Product_Price( [
			Product_Price::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_brand( Product $product ) {
		$component = new Product_Brand( [
			Product_Brand::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_gallery( Product $product ) {
		$component = new Product_Gallery( [
			Product_Gallery::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_form( Product $product ) {
		return '';
	}
}