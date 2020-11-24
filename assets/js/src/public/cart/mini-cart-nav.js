/**
 * @module Mini Cart Nav
 * @description Mini cart navigation scripts.
 */

import _ from 'lodash';
import Cookies from 'js-cookie';
import delegate from 'delegate';
import * as tools from 'utils/tools';
import { CART_API_BASE, MINI_CART } from 'publicConfig/wp-settings';
import { wpAPIMiniCartGet } from 'utils/ajax';
import cartState from 'publicConfig/cart-state';
import { on, trigger } from 'utils/events';
import { CART_ID_COOKIE_NAME } from 'bcConstants/cookies';
import { AJAX_CART_UPDATE } from 'bcConstants/events';
import { NLS } from 'publicConfig/i18n';
import globalState from 'publicConfig/state';
import ajaxItems from './ajax-items';
import { cartEmpty } from './cart-templates';

const el = {
	cartMenuItems: tools.getNodes('.menu-item-bigcommerce-cart', true, document, true),
};

const miniCartNavState = {
	show: false,
	clickHandler: null,
};

/**
 * @function showHideMiniCart
 * @description Show or hide the mini cart in the menu item.
 * @param miniCartWrapper
 */
const showHideMiniCart = (miniCartWrapper) => {
	if (miniCartNavState.show) {
		tools.addClass(miniCartWrapper, 'bc-show-mini-cart-nav');
		return;
	}

	tools.removeClass(miniCartWrapper, 'bc-show-mini-cart-nav');
};

/**
 * @function setEmptyCart
 * @description If the cart is empty, fetch and set the empty cart template.
 */
const setEmptyCart = (miniCartWrapper) => {
	miniCartWrapper.innerHTML = cartEmpty;
};

/**
 * @function handleClicks
 * @description handle cart menu item clicks.
 * @param e
 */
const handleClicks = (e) => {
	if (!miniCartNavState.clickHandler) {
		return;
	}

	e.preventDefault();
	e.stopPropagation();

	const miniCartWrapper = tools.getNodes('bc-mini-cart', false, e.delegateTarget.parentNode)[0];

	// Toggle visibility of the mini cart nav menu item.
	if (tools.hasClass(miniCartWrapper, 'bc-show-mini-cart-nav')) {
		miniCartNavState.show = false;
		showHideMiniCart(miniCartWrapper);
	} else {
		miniCartNavState.show = true;
		showHideMiniCart(miniCartWrapper);
	}

	// If the mini cart had been created already. We do not need to reload it. Changes will occur when events are fired.
	if (tools.hasClass(miniCartWrapper, 'initialized')) {
		return;
	}

	const cartID = Cookies.get(CART_ID_COOKIE_NAME);
	const cartURL = _.isEmpty(cartID) ? '' : `${CART_API_BASE}/${cartID}${NLS.cart.mini_url_param}`;

	if (_.isEmpty(cartURL)) {
		setEmptyCart();
		return;
	}

	wpAPIMiniCartGet(cartURL)
		.end((err, res) => {
			if (err) {
				console.error(err);
				setEmptyCart(miniCartWrapper);
				return;
			}

			miniCartWrapper.innerHTML = res.body.rendered;
			ajaxItems();
		});
};

/**
 * @function handleOffMenuClicks
 * @description Handle clicks outside of the current live mini cart from a menu item and close it.
 * @param e
 */
const handleOffMenuClicks = (e) => {
	const isMenuLink = tools.hasClass(e.target.parentNode, 'menu-item-bigcommerce-cart');

	if (isMenuLink) {
		return;
	}

	tools.getNodes('.bc-mini-cart--nav-menu', true, document, true).forEach((cart) => {
		if (!cart.contains(e.target)) {
			miniCartNavState.show = false;
			showHideMiniCart(cart);
		}
	});
};

/**
 * @function initCartMenuItems
 * @description Kick off the cart menu items and inject mini cart wrappers.
 */
const initCartMenuItems = () => {
	el.cartMenuItems.forEach((menuItem) => {
		const miniCartID = _.uniqueId('bc-mini-cart-');
		const fragment = document.createElement('div');
		tools.addClass(fragment, 'bc-mini-cart');
		tools.addClass(fragment, 'bc-mini-cart--nav-menu');
		fragment.setAttribute('data-js', 'bc-mini-cart');
		fragment.setAttribute('data-mini-cart-id', miniCartID);
		fragment.textContent = NLS.cart.mini_cart_loading;
		tools.addClass(fragment, 'initialized');
		cartState.instances.carts[miniCartID] = fragment;
		menuItem.appendChild(fragment);
	});

	trigger({ event: AJAX_CART_UPDATE, native: false });
};

/**
 * @function handleViewport
 * @description Enable or destroy the event listener for handling cart menu clicks on mobile and desktop.
 */
const handleViewport = () => {
	if (globalState.is_mobile) {
		// destroy the event handler if it exists.
		if (miniCartNavState.clickHandler) {
			miniCartNavState.clickHandler.destroy();
		}

		miniCartNavState.clickHandler = null;
		return;
	}

	if (globalState.is_desktop && !miniCartNavState.clickHandler) {
		miniCartNavState.clickHandler = delegate(document, '.menu-item-bigcommerce-cart > a', 'click', handleClicks);
	}
};

const bindEvents = () => {
	window.addEventListener('click', handleOffMenuClicks);
	on(document, 'modern_tribe/resize_executed', handleViewport);
};

const init = () => {
	if (!MINI_CART || !el.cartMenuItems) {
		return;
	}

	bindEvents();
	handleViewport();
	initCartMenuItems();
};

export default init;
