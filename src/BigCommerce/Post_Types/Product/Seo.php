<?php


namespace BigCommerce\Post_Types\Product;

/**
 * Class Seo
 *
 * Responsible for rendering SEO content to the page header
 */
class Seo {

	/**
	 * @param string[] $title_parts
	 *
	 * @return string[]
	 * @filter wp_title_parts
	 */
	public function filter_wp_title( $title_parts ) {
		return $this->filter_title_parts( $title_parts, 0 );
	}

	/**
	 * @param string[] $title_parts
	 *
	 * @return string[]
	 * @filter document_title_parts
	 */
	public function filter_document_title( $title_parts ) {
		return $this->filter_title_parts( $title_parts, 'title' );
	}

	private function filter_title_parts( $title_parts, $key ) {
		if ( ! is_singular( Product::NAME ) || empty( $title_parts[ $key ]) ) {
			return $title_parts;
		}

		$seo_title = $this->get_seo_title( get_queried_object_id() );
		if ( empty( $seo_title ) ) {
			return $title_parts;
		}

		$post_title = single_post_title( '', false ); // the title as it would be rendered

		if ( $title_parts[ $key ] !== $post_title ) {
			return $title_parts; // something else has already modified it, don't interfere
		}

		$title_parts[ $key ] = $seo_title;

		return $title_parts;
	}

	private function get_seo_title( $post_id ) {
		$product = new Product( $post_id );

		return $product->get_property( 'page_title' ); // the title from SEO settings
	}

	/**
	 * @return void
	 * @action wp_head
	 */
	public function print_meta_description() {
		if ( ! is_singular( Product::NAME ) ) {
			return;
		}

		$description = $this->get_meta_description( get_queried_object_id() );

		if ( empty( $description ) ) {
			return;
		}

		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
	}

	private function get_meta_description( $post_id ) {
		$product = new Product( $post_id );

		return $product->get_property( 'meta_description' ); // the description from SEO settings
	}
}
