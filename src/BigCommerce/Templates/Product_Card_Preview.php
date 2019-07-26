<?php


namespace BigCommerce\Templates;


use BigCommerce\Post_Types\Product\Product;

class Product_Card_Preview extends Product_Card {

	protected function get_title( Product $product ) {
		$component = Product_Title::factory( [
			Product_Title::PRODUCT => $product,
			Product_Title::SHOW_CONDITION => false,
			Product_Title::SHOW_INVENTORY => false,
			Product_Title::USE_PERMALINK  => false,
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