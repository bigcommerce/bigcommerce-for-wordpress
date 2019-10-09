/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */

import Cookie from 'js-cookie';
import { embedCheckout } from '@bigcommerce/checkout-sdk';
import * as tools from 'utils/tools';
import { CART_ID_COOKIE_NAME, CART_ITEM_COUNT_COOKIE } from 'bcConstants/cookies';
import { cartEmpty } from '../cart/cart-templates';

const el = {
	container: tools.getNodes('bc-embedded-checkout')[0],
};

/**
 * @function clearCartData
 * @description Clears out the localstorage and cart count in the nav menu item.
 */
const clearCartData = () => {
	const cartCount = Cookie.get(CART_ITEM_COUNT_COOKIE);
	if (!cartCount) {
		return;
	}

	const cartMenuCount = tools.getNodes('.bigcommerce-cart__item-count', false, document, true)[0];
	Cookie.remove(CART_ITEM_COUNT_COOKIE);
	tools.removeClass(cartMenuCount, 'full');
	cartMenuCount.textContent = '';
	Cookie.remove(CART_ID_COOKIE_NAME);
};

/**
 * @function loadEmbeddedCheckout
 * @description Create an instance of the BC embedded checkout.
 */
const loadEmbeddedCheckout = () => {
	// Load the config from the data attribute of the checkout container.
	const config = JSON.parse(el.container.dataset.config);

	// Return an empty cart message if there's no checkout URL.
	if (!config.url || config.url < 0) {
		el.container.innerHTML = cartEmpty;
		return;
	}

	// Set the onComplete callback to use the clearCartData function.
	config.onComplete = clearCartData;

	// Embed the checkout.
	embedCheckout(config);
};

const init = () => {
	if (!el.container) {
		return;
	}

	loadEmbeddedCheckout();
};

export default init;
