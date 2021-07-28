/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */

import Cookie from 'js-cookie';
import _ from 'lodash';
import * as tools from 'utils/tools';
import scrollTo from 'utils/dom/scroll-to';
import { trigger } from 'utils/events';
import { CART_ID_COOKIE_NAME, CART_ITEM_COUNT_COOKIE } from 'bcConstants/cookies';
import bigcommerceConfig from 'bigcommerce_config';
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

	const cartMenuCount = tools.getNodes('bc-cart-item-count', true);

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
 * @function handleOrderCompleteEvents
 * @description Clear the cart data and scroll the order into view on the page if the order is successfully completed.
 */
const handleOrderCompleteEvents = () => {
	trigger({ event: 'bigcommerce/order_complete', data: { cart_id: Cookie.get(CART_ID_COOKIE_NAME) }, native: false });
	clearCartData();
	scrollIframe();
};

/**
 * @function handleLogoutEvents
 * @description Log the user out of wordpress if they have successfully logged out of BC via the Embedded Checkout SDK.
 */
const handleLogoutEvents = () => {
	if (!bigcommerceConfig.logout_url) {
		return;
	}

	window.location = bigcommerceConfig.logout_url;
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
	config.onComplete = handleOrderCompleteEvents;

	// Set the onComplete callback to use the clearCartData function.
	config.onSignOut = handleLogoutEvents;

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
