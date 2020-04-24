<?php


namespace BigCommerce\Templates;


use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Container\Assets;
use BigCommerce\Customizer\Sections\Product_Single;

class Fallback_Image extends Controller {
	const IMAGE  = 'image';

	protected $template = 'components/fallback-image.php';


	protected function parse_options( array $options ) {
		$defaults = [];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		return [
			self::IMAGE   => $this->get_image(),
		];
	}

	protected function get_image() {
		$url        = bigcommerce()->container()[ Assets::PATH ] . '/img/public/';
		$image_size = get_option( Product_Single::GALLERY_SIZE, Product_Single::SIZE_DEFAULT );
		$image_file = $image_size === Product_Single::SIZE_LARGE ? 'bc-product-placeholder.jpg' : 'bc-product-placeholder--medium.jpg';

		$srcset = [
			esc_url( $url . 'bc-product-placeholder.jpg' ) . ' 1000w',
			esc_url( $url . 'bc-product-placeholder--large.jpg' ) . ' 600w',
			esc_url( $url . 'bc-product-placeholder--medium.jpg' ) . ' 370w',
			esc_url( $url . 'bc-product-placeholder--small.jpg' ) . ' 270w',
		];
		$image  = sprintf(
			'<img src="%s" srcset="%s" alt="%s" class="bc-product-placeholder-image" sizes="(max-width: 370px) 370px, (max-width: 1200px) 600px, (max-width: 1440px) 1000px, 85vw"/>',
			esc_url( $url . $image_file ),
			implode( ', ', $srcset ),
			esc_attr( __( 'product placeholder image', 'bigcommerce' ) )
		);

		/**
		 * Filter the fallback image for products without a featured image
		 *
		 * @param string $image The fallback image HTML
		 */
		return apply_filters( 'bigcommerce/template/image/fallback', $image );
	}


}
