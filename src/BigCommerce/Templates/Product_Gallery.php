<?php


namespace BigCommerce\Templates;


use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Customizer\Sections\Product_Single as Customizer;
use BigCommerce\Post_Types\Product\Product;

class Product_Gallery extends Controller {
	const PRODUCT        = 'product';
	const IMAGE_IDS      = 'image_ids';
	const YOUTUBE_VIDEOS = 'youtube_videos';
	const FALLBACK       = 'fallback_image';
	const SIZE           = 'image_size';
	const THUMBNAIL      = 'thumbnail_size';
	const ZOOM           = 'zoom';
	const ZOOM_SIZE      = 'zoom_size';

	protected $template        = 'components/products/product-gallery.php';
	protected $wrapper_tag     = 'div';
	protected $wrapper_classes = [ 'bc-product__gallery' ];

	protected function parse_options( array $options ) {
		$defaults = [
			self::PRODUCT   => null,
			self::SIZE      => $this->image_size(),
			self::THUMBNAIL => $this->thumbnail_size(),
		];

		return wp_parse_args( $options, $defaults );
	}

	private function image_size() {
		switch ( get_option( Customizer::GALLERY_SIZE, Customizer::SIZE_DEFAULT ) ) {
			case Customizer::SIZE_LARGE:
				$size = Image_Sizes::BC_EXTRA_MEDIUM;
				break;
			case Customizer::SIZE_DEFAULT:
			default:
				$size = Image_Sizes::BC_MEDIUM;
				break;
		}

		/**
		 * Filter the image size used for product gallery images
		 *
		 * @param string $size The image size to use
		 */
		return apply_filters( 'bigcommerce/template/gallery/image_size', $size );
	}

	private function thumbnail_size() {

		switch ( get_option( Customizer::GALLERY_SIZE, Customizer::SIZE_DEFAULT ) ) {
			case Customizer::SIZE_LARGE:
				$size = Image_Sizes::BC_THUMB_LARGE;
				break;
			case Customizer::SIZE_DEFAULT:
			default:
				$size = Image_Sizes::BC_THUMB;
				break;
		}

		/**
		 * Filter the image size used for product gallery image thumbnails
		 *
		 * @param string $size The image size to use
		 */
		return apply_filters( 'bigcommerce/template/gallery/thumbnail_size', $size );
	}

	protected function zoom_size(  ) {
		/**
		 * Filter the image size used for product gallery image thumbnails
		 *
		 * @param string $size The image size to use
		 */
		return apply_filters( 'bigcommerce/template/gallery/zoom_size', Image_Sizes::BC_LARGE );
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
			self::ZOOM           => $this->enable_zoom(),
			self::ZOOM_SIZE      => $this->zoom_size(),
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

	/**
	 * Add a class with the image size we're using
	 *
	 * @return string[]
	 */
	protected function get_wrapper_classes() {
		$size = $this->options[ self::SIZE ];
		if ( is_array( $size ) ) {
			$size = 'bc-custom';
		}

		return array_merge( parent::get_wrapper_classes(), [
			sanitize_html_class( $size . '-img' ),
		] );
	}

	protected function enable_zoom() {
		return get_option( Customizer::ENABLE_ZOOM, 'no' ) === 'yes';
	}

}
