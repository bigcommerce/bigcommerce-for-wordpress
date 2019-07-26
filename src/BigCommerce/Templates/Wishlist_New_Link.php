<?php


namespace BigCommerce\Templates;

/**
 * Class Wishlist_New_Link
 *
 * Renders the "New Wish List" button as a link
 */
class Wishlist_New_Link extends Wishlist_New_Button {
	protected $template = 'components/wishlist/new-link.php';

	protected function required_attributes() {
		$attributes = parent::required_attributes();
		unset( $attributes['type'] );
		$attributes['href'] = '#';

		return $attributes;
	}
}
