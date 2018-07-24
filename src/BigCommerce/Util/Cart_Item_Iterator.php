<?php


namespace BigCommerce\Util;


use BigCommerce\Api\v3\Model\Cart;

class Cart_Item_Iterator {
	/**
	 * @param Cart $cart
	 *
	 * @return \Generator
	 */
	public static function factory( Cart $cart ) {
		foreach ( $cart->getLineItems()->getPhysicalItems() as $item ) {
			yield $item->getId() => $item;
		}
		foreach ( $cart->getLineItems()->getDigitalItems() as $item ) {
			yield $item->getId() => $item;
		}
		foreach ( $cart->getLineItems()->getGiftCertificates() as $item ) {
			yield $item->getId() => $item;
		}
	}
}