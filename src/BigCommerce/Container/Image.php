<?php declare(strict_types=1);

namespace BigCommerce\Container;

use BigCommerce\Import\Image_Importer;
use Pimple\Container;

class Image extends Provider {

	public function register( Container $container ) {

		add_filter( 'bigcommerce/import/product/import_images', $this->create_callback( 'images_import_full_disabled', function ( ) {
			return Image_Importer::is_image_import_allowed();
		} ), 10, 0 );

		add_filter( 'wp_get_attachment_image', $this->create_callback( 'handle_attachment_via_cdn', function ( $html, $thumb_id ) {
			$bigcommerce_id = get_post_meta( $thumb_id, 'bigcommerce_id', true );

			if ( empty( $bigcommerce_id ) ) {
				return $html;
			}

			if ( ! Image_Importer::should_load_from_cdn() ) {
				return $html;
			}

			$src = get_post_meta( $thumb_id, Image_Importer::URL_THUMB, true );

			if ( empty( $src ) ) {
				return $html;
			}

			$html = preg_replace( '/src="[^"]*"/', 'src="' . $src . '"', $html );
			$html = preg_replace( '/srcset="[^"]*"/', '', $html );

			return $html;
		} ), 10, 2 );
	}

}
