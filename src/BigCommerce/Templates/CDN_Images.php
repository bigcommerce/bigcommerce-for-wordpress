<?php

namespace BigCommerce\Templates;

use BigCommerce\Import\Image_Importer;

trait CDN_Images {

	/**
	 * Get list of cdn url for each image
	 *
	 * @param array $images_ids
	 *
	 * @return array
	 */
	public function get_images_cdn_url( $images_ids = [] ) {
		if ( ! Image_Importer::should_load_from_cdn() || empty( $images_ids ) ) {
			return [];
		}

		$cdn_images = [];

		foreach ( $images_ids as $image_id ) {
			$cdn_images[ $image_id ] = [
					Image_Importer::URL_ZOOM  => get_post_meta( $image_id, Image_Importer::SOURCE_URL, true ),
					Image_Importer::URL_THUMB => get_post_meta( $image_id, Image_Importer::URL_THUMB, true ),
					Image_Importer::URL_STD   => get_post_meta( $image_id, Image_Importer::URL_STD, true ),
			];
		}

		return $cdn_images;
	}

}
