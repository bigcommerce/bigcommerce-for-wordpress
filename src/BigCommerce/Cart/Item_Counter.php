<?php

namespace BigCommerce\Cart;

use BigCommerce\Util\Cart_Item_Iterator;

/**
 * Provides functionality to count the number of items in a BigCommerce cart. 
 * This excludes child items, such as product variations or bundled items.
 */
class Item_Counter {
	/**
	 * Count the total number of items in a BigCommerce cart.
	 *
	 * This method iterates through the items in the provided cart and calculates the total count. 
	 * It excludes child items (e.g., variations or bundled items) from the total count. If the quantity 
	 * of an item is available, it adds the quantity to the count; otherwise, it assumes a quantity of 1.
	 *
	 * @param \BigCommerce\Api\v3\Model\Cart $cart The cart object to count items for.
	 *
	 * @return int The total number of items in the cart.
	 */
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
