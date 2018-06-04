/**
 * @module Ajax request functions.
 * @description Setup ajax requests via Super Agent and export for modular usage.
 */

import request from 'superagent';
import { PRODUCTS_ENDPOINT, SHORTCODE_ENDPOINT } from '../admin/config/wp-settings';
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
