<?php


namespace BigCommerce\Templates;


use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Post_Types\Product\Product;

class Product_Gallery extends Controller {
	const PRODUCT        = 'product';
	const IMAGE_IDS      = 'image_ids';
	const YOUTUBE_VIDEOS = 'youtube_videos';
	const FALLBACK       = 'fallback_image';
	const SIZE           = 'image_size';
	const THUMBNAIL      = 'thumbnail_size';

	protected $template = 'components/products/product-gallery.php';

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT   => null,
			/**
			 * Filter the image size used for product gallery images
			 *
			 * @param string $size The image size to use
			 */
			self::SIZE      => apply_filters( 'bigcommerce/template/gallery/image_size', Image_Sizes::BC_MEDIUM ),
			/**
			 * Filter the image size used for product gallery image thumbnails
			 *
			 * @param string $size The image size to use
			 */
			self::THUMBNAIL => apply_filters( 'bigcommerce/template/gallery/thumbnail_size', Image_Sizes::BC_THUMB ),
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		/** @var Product $product */
		$product = $this->options[ self::PRODUCT ];

		return [
			self::PRODUCT        => $product,
			self::IMAGE_IDS      => $product->get_gallery_ids(),
			self::YOUTUBE_VIDEOS => $this->get_videos( $product ),
			self::FALLBACK       => $this->get_fallback(),
			self::SIZE           => $this->options[ self::SIZE ],
			self::THUMBNAIL      => $this->options[ self::THUMBNAIL ],
		];
	}

	protected function get_fallback() {
		$component = Fallback_Image::factory( [] );

		return $component->render();
	}

	protected function get_videos( Product $product ) {
		/** @var \WP_Embed $embed */
		$embed = $GLOBALS['wp_embed'];

		return array_map( function ( $video ) use ( $embed ) {
			$video['embed_html'] = $embed->shortcode( [], $video['url'] );

			return $video;
		}, $product->youtube_videos() );
	}

}
