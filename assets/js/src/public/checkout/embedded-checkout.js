/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */

import Cookie from 'js-cookie';
import { embedCheckout } from '@bigcommerce/checkout-sdk';
import { cartEmpty } from '../cart/cart-templates';
import * as tools from '../../utils/tools';
import { get, remove } from '../../utils/storage/local';

const el = {
	container: tools.getNodes('bc-embedded-checkout')[0],
};

/**
 * @function clearCartData
 * @description Clears out the localstorage and cart count in the nav menu item.
 */
const clearCartData = () => {
	const cartCount = get('bigcommerce_current_cart_item_count');
	if (!cartCount) {
		return;
	}

	const cartMenuCount = tools.getNodes('.bigcommerce-cart__item-count', false, document, true)[0];
	remove('bigcommerce_current_cart_item_count');
	tools.removeClass(cartMenuCount, 'full');
	cartMenuCount.textContent = '';
	Cookie.remove('bigcommerce_cart_id');
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
