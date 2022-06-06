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

	public function get_headless_images( $product ) {
		$images = [];

		$source         = $product->get_source_data();
		$default_images = [];
		$main_image     = [];
		if ( ! empty( $source->images ) ) {
			foreach ( $source->images as $image ) {
				if ( $image->is_thumbnail ) {
					$main_image = [
						'url' => $image->url_standard,
						'alt' => $image->description,
					];
					continue;
				}
				$default_images[] = [
						'url' => $image->url_zoom,
						'alt' => $image->description,
				];
			}
		}

		foreach ( $source->variants as $variant ) {
			$image = $variant->image_url;

			if ( empty( $image ) ) {
				continue;
			}

			if ( !empty( $main_image ) && $main_image['url'] === $image) {
				continue;
			}

			$images[] = [
					'url' => $image,
					'alt' => '',
			];
		}

		if ( empty( $main_image ) ) {
			return array_merge( $default_images, $images );
		}

		return array_merge( [ $main_image ], $default_images, $images );
	}

}
