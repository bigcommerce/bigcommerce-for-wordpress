/**
 * @module Ajax request functions.
 * @description Setup ajax requests via Super Agent and export for modular usage.
 */

import request from 'superagent';
import { PRODUCTS_ENDPOINT, SHORTCODE_ENDPOINT } from '../admin/config/wp-settings';
import { ADMIN_AJAX } from '../admin/config/wp-settings';
import { GUTENBERG_PRODUCTS } from '../admin/gutenberg/config/gutenberg-settings';

export const wpAPIProductLookup = (queryString = '') => request
	.get(PRODUCTS_ENDPOINT)
	.query(queryString);

export const wpAPIShortcodeBuilder = (queryString = '') => request
	.get(SHORTCODE_ENDPOINT)
	.query(queryString);

export const wpAPICartUpdate = (cartURL, querySrting = '') => request
	.put(cartURL)
	.query(querySrting);

export const wpAPICartDelete = cartURL => request
	.del(cartURL);

export const wpAPIProductsPreview = (queryObj = {}) => request
	.get(GUTENBERG_PRODUCTS.preview_url)
	.query(queryObj);

export const wpAdminAjax = (queryObj = {}) => request
	.get(ADMIN_AJAX)
	.query(queryObj)
	.timeout({
		response: 20000,  // Wait 5 seconds for the server to start sending,
		deadline: 60000, // but allow 1 minute for the file to finish loading.
	});
