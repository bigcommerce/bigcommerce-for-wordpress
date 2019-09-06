/**
 * @module Global BigCommerce Cart API Methods
 * @description A set of global cart functions that can be called in and script or plugin to get BC data.
 */

import Cookie from 'js-cookie';
import * as COOKIES from '../../constants/cookies';
import * as GLOBALS from '../config/wp-settings';

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
	addGlobalCartMethods();
};

export default init;
