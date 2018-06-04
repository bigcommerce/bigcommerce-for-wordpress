<?php


namespace BigCommerce\Templates;


use BigCommerce\Container\Assets;

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
		$url    = bigcommerce()->container()[ Assets::PATH ] . '/img/public/';
		$srcset = [
			esc_url( $url . 'bc-product-placeholder.jpg' ) . ' 2x',
			esc_url( $url . 'bc-product-placeholder--large.jpg' ) . ' 600w',
			esc_url( $url . 'bc-product-placeholder--medium.jpg' ) . ' 370w',
			esc_url( $url . 'bc-product-placeholder--small.jpg' ) . ' 270w',
		];
		$image  = sprintf(
			'<img src="%s" srcset="%s" alt="%s" class="bc-product-placeholder-image" sizes="(max-width: 370px) 85vw, 370px, (max-width: 1200px) 600px"/>',
			esc_url( $url . 'bc-product-placeholder--medium.jpg' ),
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