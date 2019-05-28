<?php


namespace BigCommerce\Templates;

class Linked_Product_Featured_Image extends Product_Featured_Image {
	protected $wrapper_tag = 'a';
	protected $wrapper_classes = [ 'bc-product-card-image-anchor' ];

	protected function get_wrapper_attributes() {
		$attributes          = $this->wrapper_attributes;
		$attributes['href']  = esc_url( get_permalink( $this->options[ self::PRODUCT ]->post_id() ) );
		$attributes['title'] = esc_html( get_the_title( $this->options[ self::PRODUCT ]->post_id() ) );

		return $attributes;
	}
}