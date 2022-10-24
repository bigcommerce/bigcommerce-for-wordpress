<?php

namespace BigCommerce\Templates;

use BigCommerce\Import\Image_Importer;
use BigCommerce\Post_Types\Product\Product;
use \BigCommerce\Customizer\Sections\Product_Single;

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

	/**
	 * @param \BigCommerce\Post_Types\Product\Product $product
	 *
	 * @return array|array[]
	 */
	public function get_headless_images( Product $product ) {
		$images = [];

		$source          = $product->get_source_data();
		$default_images  = [];
		$main_image      = [];
		$is_standard_img = get_option( Product_Single::HEADLESS_IMAGE_SIZE, Product_Single::SIZE_CDN_STD ) === Product_Single::SIZE_CDN_STD;
		if ( ! empty( $source->images ) ) {
			foreach ( $source->images as $image ) {
				if ( $image->is_thumbnail ) {
					$main_image = [
						Image_Importer::URL_STD   => $image->url_standard,
						Image_Importer::URL_ZOOM  => $image->url_zoom ?? null,
						Image_Importer::URL_THUMB => $is_standard_img ? $image->url_standard : $image->url_thumbnail ?? null,
						Image_Importer::IMAGE_ALT => $image->description,
					];
					continue;
				}
				$default_images[] = [
					Image_Importer::URL_STD   => $image->url_standard,
					Image_Importer::URL_ZOOM  => $image->url_zoom ?? null,
					Image_Importer::URL_THUMB => $is_standard_img ? $image->url_standard : $image->url_thumbnail ?? null,
					Image_Importer::IMAGE_ALT => $image->description,
				];
			}
		}

		foreach ( $source->variants as $variant ) {
			$image = $variant->image_url;

			if ( empty( $image ) ) {
				continue;
			}

			if ( ! empty( $main_image ) && $main_image[ Image_Importer::URL_STD ] === $image ) {
				continue;
			}

			$images[] = [
				Image_Importer::URL_STD   => $image,
				Image_Importer::URL_ZOOM  => $image,
				Image_Importer::URL_THUMB => $image,
				Image_Importer::IMAGE_ALT => '',
			];
		}

		if ( empty( $main_image ) ) {
			return array_merge( $default_images, $images );
		}

		return array_merge( [ $main_image ], $default_images, $images );
	}

}
