<?php

namespace BigCommerce\Editor\Gutenberg;

use BigCommerce\Shortcodes;

/**
 * Class Migrate_Blocks
 *
 * When loading a post in the Gutenberg editor, migrate shortcodes
 * from this plugin to blocks
 */
class Migrate_Blocks {

	/**
	 * If Gutenberg will be replacing the editor,
	 * set up additional hooks to filter the post
	 * content.
	 *
	 * @param bool     $passthrough
	 * @param \WP_Post $post
	 *
	 * @return bool
	 * @see    gutenberg_init()
	 * @filter replace_editor 9
	 */
	public function check_if_gutenberg_editor( $passthrough, $post ) {
		// Duplicate the conditions from gutenberg_init
		if ( ! function_exists( 'gutenberg_can_edit_post' ) ) {
			return $passthrough; // gutenberg doesn't exist
		}

		if ( true === $passthrough && current_filter() === 'replace_editor' ) {
			return $passthrough; // something else has replaced the editor
		}

		if ( isset( $_GET[ 'classic-editor' ] ) ) {
			return $passthrough; // classic editor will be used
		}

		if ( ! gutenberg_can_edit_post( $post ) ) {
			return $passthrough;
		}

		// Gutenberg will be editing this post, so set up hooks
		$this->set_gutenberg_editor_hooks( $post );

		return $passthrough;
	}


	/**
	 * If Gutenberg will NOT be replacing the editor,
	 * set up additional hooks to filter the post
	 * content.
	 *
	 * @param bool     $passthrough
	 * @param \WP_Post $post
	 *
	 * @return bool
	 * @see    gutenberg_init()
	 * @filter replace_editor 11
	 */
	public function check_if_classic_editor( $passthrough, $post ) {
		if ( $passthrough ) {
			return $passthrough; // gutenberg is in charge
		}

		// standard editor, set up appropriate hooks
		$this->set_classic_editor_hooks( $post );

		return $passthrough;
	}

	/**
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	private function set_gutenberg_editor_hooks( $post ) {
		add_filter( 'rest_prepare_' . $post->post_type, [ $this, 'filter_post_rest_response' ], 10, 3 );
	}

	private function set_classic_editor_hooks( $post ) {
		add_filter( 'the_editor_content', function ( $content, $default_editor ) use ( $post ) {
			if ( $content != $post->post_content ) {
				/**
				 * Let's assume that this is a different editor, and not be too invasive.
				 * A little brittle, because something might have changed the content earlier,
				 * but we'd rather be too passive here than too aggressive.
				 */
				return $content;
			}

			$content = $this->replace_blocks_with_shortcodes( $content );

			return $content;
		}, 0, 2 );
	}


	/**
	 * Filters the post data for a response.
	 *
	 * The dynamic portion of the hook name, `$this->post_type`, refers to the post type slug.
	 *
	 * @since  4.7.0
	 *
	 * @param \WP_REST_Response $response The response object.
	 * @param \WP_Post          $post     Post object.
	 * @param \WP_REST_Request  $request  Request object.
	 *
	 * @return \WP_REST_Response
	 *
	 * @filter 'rest_prepare_' . $post->post_type
	 */
	public function filter_post_rest_response( $response, $post, $request ) {
		$data = $response->get_data();
		if ( array_key_exists( 'content', $data ) && array_key_exists( 'raw', $data[ 'content' ] ) ) {
			$data[ 'content' ][ 'raw' ] = $this->ensure_blocks( $data[ 'content' ][ 'raw' ] );
			$response->set_data( $data );
		}

		return $response;
	}

	private function ensure_blocks( $content ) {
		if ( strpos( $content, '<!-- wp:bigcommerce/' ) !== false ) {
			return $content; // already has bigcommerce blocks, so leave it as-is
		}
		$product_shortcode = Shortcodes\Products::NAME;

		$search  = '#(?<!\<div class=\"wp-block-bigcommerce-products\"\>)(?<!\")\s*(\[\s*' . $product_shortcode . '(\s[^\]]*)?\])\s*(?!\<\/div\>)#';
		$content = preg_replace_callback( $search, [ $this, 'product_shortcode_to_block' ], $content );

		$shortcode_map = [
			Shortcodes\Account_Profile::NAME          => Blocks\Account_Profile::NAME,
			Shortcodes\Address_List::NAME             => Blocks\Address_List::NAME,
			Shortcodes\Cart::NAME                     => Blocks\Cart::NAME,
			Shortcodes\Checkout::NAME                 => Blocks\Checkout::NAME,
			Shortcodes\Login_Form::NAME               => Blocks\Login_Form::NAME,
			Shortcodes\Order_History::NAME            => Blocks\Order_History::NAME,
			Shortcodes\Registration_Form::NAME        => Blocks\Registration_Form::NAME,
			Shortcodes\Gift_Certificate_Form::NAME    => Blocks\Gift_Certificate_Form::NAME,
			Shortcodes\Gift_Certificate_Balance::NAME => Blocks\Gift_Certificate_Balance::NAME,
			Shortcodes\Product_Reviews::NAME          => Blocks\Product_Reviews::NAME,
		];

		foreach ( $shortcode_map as $shortcode_id => $block_id ) {
			$shortcode = sprintf( '[%s]', $shortcode_id );
			$block_data = [ 'shortcode' => $shortcode ];
			$block     = sprintf( "<!-- wp:%s %s -->\n[%s]\n<!-- /wp:%s -->", $block_id, wp_json_encode( $block_data ), $shortcode_id, $block_id );
			$content   = str_replace( $shortcode, $block, $content );
		}

		return $content;
	}

	private function product_shortcode_to_block( $match ) {
		$shortcode       = $match[ 1 ];
		$shortcode_regex = get_shortcode_regex( [ Shortcodes\Products::NAME ] );
		preg_match( "/$shortcode_regex/", $shortcode, $matches );
		$attributes = shortcode_parse_atts( $matches[ 3 ] );

		$block_data = [
			'shortcode'   => $shortcode,
			'queryParams' => array_merge( $attributes, [ 'preview' => 1, 'paged' => 0 ] ),
		];

		$block_string = sprintf(
			"<!-- wp:%s %s -->\n<div class=\"%s\">%s</div>\n<!-- /wp:%s -->\n",
			Blocks\Products::NAME,
			wp_json_encode( $block_data ),
			'wp-block-bigcommerce-products',
			$shortcode,
			Blocks\Products::NAME
		);

		return $block_string;
	}

	/**
	 * Transform all bigcommerce blocks into their shortcode equivalents
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function replace_blocks_with_shortcodes( $content ) {
		if ( strpos( $content, '<!-- wp:bigcommerce/' ) === false ) {
			return $content; // no blocks to migrate
		}

		$search  = '<!-- wp:bigcommerce/([^\s]+)[^>]*-->\s*(<div class="wp-block-bigcommerce-products">)?(\[[\S\s]*?\])(</div>)?\s*<!-- /wp:bigcommerce/\1 -->';
		$content = preg_replace( "#$search#", '$3', $content );

		return $content;
	}
}