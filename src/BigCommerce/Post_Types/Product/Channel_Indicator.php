<?php


namespace BigCommerce\Post_Types\Product;

use BigCommerce\Taxonomies\Channel\Channel;

/**
 * Class Channel_Indicator
 *
 * Responsible for showing an indicator in the Product admin
 * for which channel the current post is in
 */
class Channel_Indicator {


	/**
	 * Add a message to the "Publish" meta box
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 * @action post_submitbox_misc_actions
	 */
	public function add_submitbox_message( $post ) {
		if ( get_post_type( $post ) !== Product::NAME ) {
			return;
		}

		$name = $this->get_channel_name( $post->ID );
		if ( ! $name ) {
			return;
		}

		echo '<div class="misc-pub-section misc-pub-bigcommerce">';
		printf( esc_html( __( 'Channel: %s', 'bigcommerce' ) ), esc_html( $name ) );
		echo '</div>';
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @filter bigcommerce/gutenberg/js_config
	 */
	public function add_message_to_gutenberg_config( $data ) {
		$data['channel_indicator'] = [
			'label' => '',
			'value' => '',
		];

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen ) {
			return $data;
		}
		if ( $screen->id !== Product::NAME ) {
			return $data;
		}

		$post = get_post();
		$name = $this->get_channel_name( $post->ID );

		if ( ! $name ) {
			return $data;
		}
		$data['channel_indicator'] = [
			'label' => __( 'Channel', 'bigcommerce' ),
			'value' => $name,
		];


		return $data;
	}

	private function get_channel_name( $post_id ) {
		$terms = get_the_terms( $post_id, Channel::NAME );
		if ( empty( $terms ) ) {
			return '';
		}

		return reset( $terms )->name;
	}
}
