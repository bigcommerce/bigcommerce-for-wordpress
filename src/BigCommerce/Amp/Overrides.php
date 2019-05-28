<?php

namespace BigCommerce\Amp;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Container\Assets;

/**
 * Class Overrides
 *
 * Overrides template variables.
 *
 * @package BigCommerce\Amp
 */
class Overrides {

	/**
	 * Add AMP Img Src
	 *
	 * Adds 'image_src' to the data passed to the product featured image template.
	 *
	 * @param array  $data     Data to be sent to the template.
	 * @param string $template Current template.
	 *
	 * @return array
	 */
	public function add_amp_img_src( $data, $template ) {
		if ( 'components/products/product-featured-image.php' === $template ) {
			$featured_image = wp_get_attachment_image_src( $data['attachment_id'], Image_Sizes::BC_MEDIUM );
			if ( $featured_image ) {
				$data['image_src']    = wp_get_attachment_image_src( $data['attachment_id'], Image_Sizes::BC_MEDIUM );
				$data['image_srcset'] = [
					$data['image_src'][0] . ' ' . $data['image_src'][1] . 'w',
				];
			} else {
				$url                  = bigcommerce()->container()[ Assets::PATH ] . '/img/public/';
				$data['image_srcset'] = [
					esc_url( $url . 'bc-product-placeholder.jpg' ) . ' 2x',
					esc_url( $url . 'bc-product-placeholder--large.jpg' ) . ' 600w',
					esc_url( $url . 'bc-product-placeholder--medium.jpg' ) . ' 370w',
					esc_url( $url . 'bc-product-placeholder--small.jpg' ) . ' 270w',
				];
				$data['image_src']    = array(
					trailingslashit( $url ) . 'bc-product-placeholder--medium.jpg',
					370,
					370,
				);
			}
		}

		return $data;
	}

	/**
	 * AMP Enable Button
	 *
	 * Enable the AMP submit button when a proper variant has been chosen using array filters.
	 *
	 * @param string $button  Button HTML.
	 * @param int    $post_id Post ID.
	 *
	 * @return string
	 */
	public function amp_enable_button( $button, $post_id ) {
		$insert = '[disabled]="' . esc_attr( 'variants' . $post_id . '.allVariants.filter( a => a.options.filter( b => ( keys( variants' . $post_id . '.currentOptions ).filter( key => variants' . $post_id . '.currentOptions[ key ] == b.id && key == b.option_id ? true : false ) ).length ? true : false ).length == a.options.length ? a : false )[0].disabled != false ? \'disabled\' : false' ) . '"';
		$button = str_replace( 'type="submit"', 'type="submit" ' . $insert, $button );

		return $button;
	}

	/**
	 * AMP Add Redirect Headers
	 *
	 * Identify if we are attempting a redirect from an AMP form and if so, add the proper headers to redirect in AMP.
	 *
	 * @param string $url URL to be redirected to.
	 */
	public function add_amp_redirect_headers( $url ) {
		$amp_source_origin = filter_input( INPUT_GET, '__amp_source_origin', FILTER_SANITIZE_STRING ) ?: false;

		if ( $amp_source_origin ) {
			// Ensure we have an absolute path for the redirect URL.
			$parsed_url = wp_parse_url( $url );

			if ( ! isset( $parsed_url['scheme'] ) && ! isset( $parsed_url['host'] ) ) {
				if ( '/' === substr( $url, 0, 1 ) ) {
					$url = untrailingslashit( get_site_url() ) . $url;
				} else {
					$url = trailingslashit( get_site_url() ) . $url;
				}
			}

			$url = add_query_arg( 'amp', true, $url );

			// Add the AMP headers so the redirect works properly and doesn't throw a console error.
			header( 'AMP-Redirect-To: ' . esc_url_raw( $url ) );
			header( 'AMP-Access-Control-Allow-Source-Origin: ' . esc_url_raw( $amp_source_origin ) );
			header( 'Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin, AMP-Redirect-To' );
			wp_send_json_success();
		}
	}

	/**
	 * Filter Fallback Image
	 *
	 * @return string
	 */
	public function filter_fallback_image() {
		$url    = bigcommerce()->container()[ Assets::PATH ] . '/img/public/';
		$srcset = [
			esc_url( $url . 'bc-product-placeholder--large.jpg' ) . ' 600w',
			esc_url( $url . 'bc-product-placeholder--medium.jpg' ) . ' 370w',
			esc_url( $url . 'bc-product-placeholder--small.jpg' ) . ' 270w',
		];

		return sprintf(
			'<amp-img src="%s" srcset="%s" alt="%s" class="bc-product-placeholder-image" sizes="(max-width: 370px) 85vw, 370px, (max-width: 1200px) 600px" layout="intrinsic" height="370" width="370" />',
			esc_url( $url . 'bc-product-placeholder--medium.jpg' ),
			implode( ', ', $srcset ),
			esc_attr( __( 'product placeholder image', 'bigcommerce' ) )
		);
	}

	/**
	 * AMP Kses Allowed HTML
	 *
	 * @param array  $allowed_tags Array of allowed tags
	 * @param string $context      Context of kses tags.
	 *
	 * @return array
	 */
	public function amp_kses_allowed_html( $allowed_tags, $context ) {
		if ( 'bigcommerce/amp' === $context ) {
			return apply_filters( 'bigcommerce/amp/kses_allowed_html',
				array_merge(
					wp_kses_allowed_html( 'post' ),
					array(
						'input'           => array(
							'type'        => array(),
							'name'        => array(),
							'value'       => array(),
							'placeholder' => array(),
							'class'       => array(),
						),
						'select'          => array(
							'name'  => array(),
							'id'    => array(),
							'on'    => array(),
							'class' => array(),
						),
						'option'          => array(
							'value'    => array(),
							'selected' => array(),
						),
						'img'             => array(
							'src'      => array(),
							'class'    => array(),
							'alt'      => array(),
							'srcset'   => array(),
							'id'       => array(),
							'version'  => array(),
							'decoding' => array(),
						),
						'amp-img'         => array(
							'src'    => array(),
							'width'  => array(),
							'height' => array(),
							'layout' => array(),
							'alt'    => array(),
							'class'  => array(),
							'id'     => array(),
							'srcset' => array(),
						),
						'i-amphtml-sizer' => array(
							'class' => array(),
							'id'    => array(),
						),
						'template'        => array(
							'type' => array(),
							'id'   => array(),
						),
						'amp-list'        => array(
							'id'               => array(),
							'layout'           => array(),
							'height'           => array(),
							'width'            => array(),
							'src'              => array(),
							'single-item'      => array(),
							'items'            => array(),
							'class'            => array(),
							'reset-on-refresh' => array(),
						),
						'amp-state'       => array(
							'id'  => array(),
							'src' => array(),
						),
						'amp-lightbox'    => array(
							'on'         => array(),
							'id'         => array(),
							'scrollable' => array(),
							'layout'     => array(),
						),
						'button'          => array(
							'on'             => array(),
							'type'           => array(),
							'class'          => array(),
							'tabindex'       => array(),
							'aria-label'     => array(),
							'data-productid' => array(),
						),
						'amp-carousel'    => array(
							'id'     => array(),
							'class'  => array(),
							'width'  => array(),
							'height' => array(),
							'layout' => array(),
							'type'   => array(),
						),
						'span'            => array(
							'on'       => array(),
							'class'    => array(),
							'tabindex' => array(),
							'role'     => array(),
						),
						'form'            => array(
							'action-xhr' => array(),
							'method'     => array(),
							'enctype'    => array(),
							'class'      => array(),
							'target'     => array(),
						),
					)
				)
			);
		}

		return $allowed_tags;
	}
}
