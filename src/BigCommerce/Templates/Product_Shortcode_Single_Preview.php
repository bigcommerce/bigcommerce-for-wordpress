<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Taxonomies\Flag\Flag;

class Product_Shortcode_Single_Preview extends Product_Shortcode_Single {

	use Product_TemplateTrait;

	protected function get_title( Product $product ) {
		$component = Product_Title::factory( [
			Product_Title::PRODUCT => $product,
			Product_Title::SHOW_CONDITION => false,
			Product_Title::SHOW_INVENTORY => false,
			Product_Title::USE_PERMALINK  => false,
			Product_Title::HEADER_LEVEL   => 2,
		] );

		return $component->render();
	}

	protected function get_description( Product $product ) {
		$component = Product_Description::factory( [
			Product_Description::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_gallery( Product $product ) {
		$component = Product_Gallery::factory( [
			Product_Gallery::PRODUCT => $product,
		] );

		return $component->render();
	}

	protected function get_form( Product $product ) {
		$component = Product_Form_Preview::factory( [
			Product_Form::PRODUCT      => $product,
			Product_Form::SHOW_OPTIONS => false,
		] );

		return $component->render();
	}
}
