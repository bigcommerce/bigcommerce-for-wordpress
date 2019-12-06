<?php

namespace BigCommerce\Cart;

use BigCommerce\Util\Cart_Item_Iterator;

/**
 * Class Item_Counter
 *
 * Counts the number of items in a cart
 */
class Item_Counter {
	public static function count_bigcommerce_cart( \BigCommerce\Api\v3\Model\Cart $cart ) {
		return array_reduce(
			iterator_to_array( Cart_Item_Iterator::factory( $cart ) ),
			function ( $count, $item ) {
				if ( method_exists( $item, 'getParentId' ) && $item->getParentId() ) {
					return $count; // it's a child item, so don't count it
				}
				if ( method_exists( $item, 'getQuantity' ) ) {
					$count += $item->getQuantity();
				} else {
					$count += 1;
				}

				return $count;
			},
			0
		);
	}
}
