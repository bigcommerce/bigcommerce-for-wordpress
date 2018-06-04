/**
 * @module Cart Menu Item
 * @description Handle cart item count for WordPress menu item.
 */

import Cookies from 'js-cookie';
import _ from 'lodash';
import * as tools from '../../utils/tools';
import * as ls from '../../utils/storage/local';
import { CART_ITEM_COUNT_KEY, CART_ITEM_COUNT_COOKIE } from '../../constants/cookies';

const cartMenuSet = itemCount => ls.put(CART_ITEM_COUNT_KEY, itemCount);

const cartMenuGet = ls.get(CART_ITEM_COUNT_KEY);

const updateCartMenuItem = () => {
	const currentCount = ls.get(CART_ITEM_COUNT_KEY);
	tools.getNodes('.bigcommerce-cart__item-count', true, document, true).forEach((item) => {
		item.classList.remove('full');

		if (currentCount <= 0) {
			item.innerHTML = '';
			return;
		}

		_.delay(() => {
			item.classList.add('full');
		}, 150);

		item.innerHTML = currentCount;
	});
};

const updateMenuQtyTotal = (data = {}) => {
	const totalQty = [];

	Object.values(data.items).forEach((value) => {
		const itemQty = value.quantity;
		totalQty.push(itemQty);
	});

	cartMenuSet(totalQty.reduce((prev, next) => prev + next, 0));

	updateCartMenuItem();
};

const updateMenuQtyOnPageLoad = () => {
	const cookie = Cookies.get(CART_ITEM_COUNT_COOKIE);

	if (!cookie) {
		updateCartMenuItem();
		return;
	}

	if (cookie !== cartMenuGet) {
		cartMenuSet(cookie);
		updateCartMenuItem();
		_.delay(() => {
			Cookies.remove(CART_ITEM_COUNT_COOKIE);
		}, 100);
	}
};

export { cartMenuSet, updateMenuQtyTotal, updateMenuQtyOnPageLoad, updateCartMenuItem };
