/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */

import Cookie from 'js-cookie';
import _ from 'lodash';
import * as tools from 'utils/tools';
import scrollTo from 'utils/dom/scroll-to';
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

	const cartMenuCount = tools.getNodes('.bigcommerce-cart__item-count', true, document, true);

	_.delay(() => {
		Cookie.remove(CART_ITEM_COUNT_COOKIE);
		Cookie.remove(CART_ID_COOKIE_NAME);

		cartMenuCount.forEach((menuItem) => {
			tools.removeClass(menuItem, 'full');
			menuItem.textContent = '';
		});
	}, 250);
};

/**
 * @function scrollIframe
 * @description After completing the checkout process, ensure the top of the embedded checkout is visible using scrollTo.
 */
const scrollIframe = () => {
	const options = {
		offset: -80,
		duration: 750,
		$target: jQuery(el.container),
	};

	_.delay(() => scrollTo(options), 1000);
};

/**
 * @function loadEmbeddedCheckout
 * @description Create an instance of the BC embedded checkout.
 */
const loadEmbeddedCheckout = async () => {
	const checkoutCDN = await checkoutKitLoader.load('embedded-checkout');
	// Load the config from the data attribute of the checkout container.
	const config = JSON.parse(el.container.dataset.config);

	// Return an empty cart message if there's no checkout URL.
	if (!config.url || config.url < 0) {
		el.container.innerHTML = cartEmpty;
		return;
	}

	// Set the onComplete callback to use the clearCartData function.
	config.onComplete = clearCartData;
	config.onComplete = scrollIframe;

	// Embed the checkout.
	checkoutCDN.embedCheckout(config);
};

const init = () => {
	if (!el.container) {
		return;
	}

	loadEmbeddedCheckout();
};

export default init;
