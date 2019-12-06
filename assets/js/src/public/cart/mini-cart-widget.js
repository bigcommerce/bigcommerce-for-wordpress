/**
 * @module Mini Cart
 * @description Main mini cart controller for all actions that occur in cart blocks.
 */

import _ from 'lodash';
import Cookies from 'js-cookie';
import { on, trigger } from 'utils/events';
import * as tools from 'utils/tools';
import { CART_ID_COOKIE_NAME, CART_ITEM_COUNT_COOKIE } from 'bcConstants/cookies';
import { wpAPIMiniCartGet } from 'utils/ajax';
import { NLS } from 'publicConfig/i18n';
import cartState from 'publicConfig/cart-state';
import { AJAX_CART_NONCE, CART_API_BASE } from 'publicConfig/wp-settings';
import { cartEmpty } from './cart-templates';
import { updateCartMenuItem } from './cart-menu-item';
import ajaxItems from './ajax-items';

/**
 * @function setEmptyCart
 * @description If the cart is empty, fetch and set the empty cart template.
 * @param miniCartID
 */
const setEmptyCart = (miniCartID = '') => {
	Object.values(cartState.instances.carts).forEach((widget) => {
		widget.innerHTML = cartEmpty;
	});

	cartState.isFetching = false;
	trigger({ event: 'bigcommerce/handle_cart_state', data: { miniCartID }, native: false });
};

/**
 * @function updateCartMenuCount
 * @description if we have a response from the mini cart endpoint and the cart count does not match the cookie, run this.
 * @param count
 */
const updateCartMenuCount = (count = '') => {
	const cookie = Number(Cookies.get(CART_ITEM_COUNT_COOKIE));
	if (count === cookie) {
		return;
	}

	Cookies.set(CART_ITEM_COUNT_COOKIE, count);
	updateCartMenuItem();
};

/**
 * @function loadMiniCarts
 * @description loads the template to display mini-cart widgets
 * @param e
 */
const loadMiniCarts = (e) => {
	if (Object.entries(cartState.instances.carts).length <= 0) {
		return;
	}

	// Check for event detail data from outside events.
	const eventMiniCartID = e ? e.detail.miniCartID : '';
	// Get the current Cart ID
	const cartID = Cookies.get(CART_ID_COOKIE_NAME);
	const cartURL = _.isEmpty(cartID) ? '' : `${CART_API_BASE}/${cartID}${NLS.cart.mini_url_param}`;

	// Start the handle_cart_state event.
	cartState.isFetching = true;
	trigger({ event: 'bigcommerce/handle_cart_state', data: { miniCartID: eventMiniCartID }, native: false });

	// If we don't have a cartID and URL, stop here.
	if (_.isEmpty(cartURL)) {
		setEmptyCart();
		return;
	}

	wpAPIMiniCartGet(cartURL)
		.set('X-WP-Nonce', AJAX_CART_NONCE)
		.end((err, res) => {
			if (err) {
				console.error(err);
				setEmptyCart();
				return;
			}

			// Loop through available mini carts and update cart HTML
			Object.values(cartState.instances.carts).forEach((widget) => {
				// Skip this cart if it is the one that triggered the original event.
				if (widget.dataset.miniCartId === eventMiniCartID) {
					return;
				}

				widget.innerHTML = res.body.rendered;
				ajaxItems();

				// If the count key exists in the response object, proceed with updating the menu item count.
				if (!_.some(res.body.count, _.isEmpty)) {
					updateCartMenuCount(res.body.count);
				}
			});

			// End the handle_cart_state event.
			cartState.isFetching = false;
			trigger({ event: 'bigcommerce/handle_cart_state', data: { miniCartID: eventMiniCartID }, native: false });
		});
};

const cacheElements = () => {
	tools.getNodes('bc-mini-cart', true).forEach((cart) => {
		const miniCartID = _.uniqueId('bc-mini-cart-');
		tools.addClass(cart, 'initialized');
		cart.setAttribute('data-mini-cart-id', miniCartID);
		cartState.instances.carts[miniCartID] = cart;
	});
};

const bindEvents = () => {
	on(document, 'bigcommerce/update_mini_cart', loadMiniCarts);
};

const init = () => {
	cacheElements();
	bindEvents();
	loadMiniCarts();
};

export default init;
