/**
 * @module Global BigCommerce Cart API Methods
 * @description A set of global cart functions that can be called in and script or plugin to get BC data.
 */

import Cookie from 'js-cookie';
import _ from 'lodash';
import * as COOKIES from '../../constants/cookies';
import * as GLOBALS from '../config/wp-settings';

/**
 * @function setCartCookieNames
 * @default Due to an issue on some servers, cookies for WordPress need to be set with the `wp-` prefix.
 * TODO: This should be deprecated entirely in the next major release.
 */
const resetCartCookieNames = () => {
	// Get deprecated cart cookies.
	const deprecatedCartID = Cookie.get(COOKIES.DEPRECATED_CART_ID_COOKIE_NAME);
	const deprecatedCartCount = Cookie.get(COOKIES.DEPRECATED_CART_ITEM_COUNT_COOKIE);
	const currentCartID = Cookie.get(COOKIES.CART_ID_COOKIE_NAME);
	const currentCartCount = Cookie.get(COOKIES.CART_ITEM_COUNT_COOKIE);

	// If there are no old or new cookies set, stop here.
	if ((!deprecatedCartCount && !deprecatedCartID) || (currentCartID && currentCartCount)) {
		return;
	}

	// Reset the cart ID cookie name
	if (!_.isEmpty(deprecatedCartID) && _.isEmpty(currentCartID)) {
		Cookie.set(COOKIES.CART_ID_COOKIE_NAME, deprecatedCartID);
		Cookie.remove(COOKIES.DEPRECATED_CART_ID_COOKIE_NAME);
	}

	// Reset the cart count cookie name
	if (!_.isEmpty(deprecatedCartCount) && _.isEmpty(currentCartCount)) {
		Cookie.set(COOKIES.CART_ITEM_COUNT_COOKIE, deprecatedCartCount);
		Cookie.remove(COOKIES.DEPRECATED_CART_ITEM_COUNT_COOKIE);
	}
};

/**
 * @function addGlobalCartMethods
 * @default clearinghouse for global functions for cart related data.
 */
const addGlobalCartMethods = () => {
	/**
	 * @function getCartID
	 * @default checks for a valid BC cart cookie and returns it's ID value.
	 * @returns {string}
	 */
	GLOBALS.CART.getCartID = () => Cookie.get(COOKIES.CART_ID_COOKIE_NAME);
};

const init = () => {
	resetCartCookieNames();
	addGlobalCartMethods();
};

export default init;
