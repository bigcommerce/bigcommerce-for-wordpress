<?php


namespace BigCommerce\Templates;

use BigCommerce\Customizer\Sections;

class Option_Product_List_With_Images extends Option_Type {

	protected $template = 'components/option-product-list-with-images.php';


	protected function get_options() {
		return array_map( function ( $option ) {
			$option['post_id']       = 0;
			$option['attachment_id'] = 0;

			if ( ! empty( $option['value_data']['product_id'] ) ) {
				$option['product_id'] = (int) $option['value_data']['product_id'];
			} else {
				$option['product_id'] = 0;
			}

			if ( ! empty( $option['product_id'] ) ) {
				$option['post_id'] = $this->get_matching_post_id( $option['product_id'] );
			}

			if ( ! empty( $option['post_id'] ) ) {
				$option['attachment_id'] = $this->get_attachment_id( $option['post_id'] );
				get_post_thumbnail_id( $option['post_id'] );
			}

			return $option;
		}, parent::get_options() );
	}


	protected function get_attachment_id( $post_id ) {
		$featured_image = get_post_thumbnail_id( $post_id );
		if ( ! empty( $featured_image ) ) {
			return absint( $featured_image );
		}
		$default = get_option( Sections\Product_Single::DEFAULT_IMAGE, 0 );
		if ( ! empty( $default ) ) {
			return absint( $default );
		}

		return 0;
	}

	protected function get_matching_post_id( $bc_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$sql = "SELECT post_id FROM {$wpdb->bc_products} WHERE bc_id=%d";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $bc_id ) );
	}
}