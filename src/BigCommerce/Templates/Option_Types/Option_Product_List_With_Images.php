<?php


namespace BigCommerce\Templates\Option_Types;

use BigCommerce\Customizer\Sections;

class Option_Product_List_With_Images extends Option_Product_List {

	protected $template = 'components/option-types/option-product-list-with-images.php';

	protected function get_options() {
		return array_map( function ( $option ) {
			if ( ! empty( $option[ 'post_id' ] ) ) {
				$option[ 'attachment_id' ] = $this->get_attachment_id( $option[ 'post_id' ] );
				get_post_thumbnail_id( $option[ 'post_id' ] );
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

}
