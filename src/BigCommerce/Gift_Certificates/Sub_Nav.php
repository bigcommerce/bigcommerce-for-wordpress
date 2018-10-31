<?php


namespace BigCommerce\Gift_Certificates;


use BigCommerce\Pages;
use BigCommerce\Templates\Sub_Nav_Links;

class Sub_Nav {
	/**
	 * @param string $content
	 *
	 * @return string
	 * @filter the_content
	 */
	public function add_subnav_above_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}
		$post_id = get_queried_object_id();
		if ( $post_id !== get_the_ID() ) {
			return $content; // don't filter if we're not on the main post
		}
		/**
		 * Filter whether to display the gift certificate subnav before the post content
		 * on gift certificate pages.
		 *
		 * @param bool $display True to display the subnav, false to skip it
		 * @param int  $post_id The ID of the current page
		 */
		if ( ! apply_filters( 'bigcommerce/gift_certificates/do_subnav', true, $post_id ) ) {
			return $content;
		}
		switch ( $post_id ) {
			case get_option( Pages\Gift_Certificate_Page::NAME, 0 ):
			case get_option( Pages\Check_Balance_Page::NAME, 0 ):
				return $this->get_subnav() . $content;
			default:
				return $content;
		}
	}

	private function get_subnav() {
		$component = Sub_Nav_Links::factory( [
			Sub_Nav_Links::LINKS => $this->get_links(),
		] );

		return $component->render();
	}

	private function get_links() {
		$links = [];
		foreach ( [ Pages\Gift_Certificate_Page::NAME, Pages\Check_Balance_Page::NAME ] as $option ) {
			$post_id = get_option( $option, 0 );
			if ( $post_id ) {
				$links[] = [
					'url'     => get_permalink( $post_id ),
					'label'   => get_the_title( $post_id ),
					'current' => ( $post_id == get_queried_object_id() ),
				];
			}
		}

		/**
		 * Filter the links that show in the account subnav.
		 *
		 * @param array[] $links Each link will have the properties:
		 *                       `url` - The URL of the link
		 *                       `label` - The label of the link
		 *                       `current` - Whether the link is to the current page
		 */
		return apply_filters( 'bigcommerce/gift_certificates/subnav/links', $links );
	}
}