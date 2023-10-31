/**
 * @module Ajax request functions.
 * @description Setup ajax requests via Super Agent and export for modular usage.
 */

import request from 'superagent';
import { PRODUCTS_ENDPOINT, SHORTCODE_ENDPOINT, ADMIN_AJAX } from '../admin/config/wp-settings';
import { GUTENBERG_PRODUCTS, GUTENBERG_PRODUCT_COMPONENTS } from '../admin/gutenberg/config/gutenberg-settings';

export const wpAPIProductLookup = (queryString = '') => request
	.get(PRODUCTS_ENDPOINT)
	.query(queryString);

export const wpAPIPagedProductLookup = URL => request
	.get(URL);

export const wpAPIShortcodeBuilder = (queryString = '') => request
	.get(SHORTCODE_ENDPOINT)
	.query(queryString);

export const wpAPICartUpdate = (cartURL, queryString = '') => request
	.put(cartURL)
	.query(queryString);

export const wpAPIAddToCartAjax = (cartURL, queryString = '') => request
	.post(cartURL)
	.query(queryString)
	.timeout({
		response: 15000,  // Wait 15 seconds for the server to start sending,
		deadline: 60000, // but allow 1 minute for the file to finish loading.
	});

export const wpAPICartDelete = cartURL => request
	.post(cartURL);

export const wpAPIMiniCartGet = cartURL => request
	.get(cartURL);

export const wpAPIProductsPreview = (queryObj = {}) => request
	.get(GUTENBERG_PRODUCTS.preview_url)
	.query(queryObj);

export const wpAPIProductComponentPreview = (queryObj = {}) => request
	.get(GUTENBERG_PRODUCT_COMPONENTS.preview_url)
	.query(queryObj);

export const wpAdminAjax = (queryObj = {}) => request
	.get(ADMIN_AJAX)
	.query(queryObj)
	.timeout({
		response: 20000,  // Wait 20 seconds for the server to start sending,
		deadline: 60000, // but allow 1 minute for the file to finish loading.
	});

export const wpAPIProductPricing = (pricingURL = '', pricingNonce = '', productsObj = {}) => {
	const priceRequest = request
		.post(pricingURL)
		.set('Content-Type', 'application/json');
	if (pricingNonce) {
		priceRequest.set('X-WP-Nonce', pricingNonce);
	}
	return priceRequest.send(productsObj)
		.timeout({
			response: 15000,  // Wait 15 seconds for the server to start sending,
			deadline: 60000, // but allow 1 minute for the file to finish loading.
		});
};

export const wpAPIGetShippingZones = URL => request
	.get(URL);

export const wpAPIGetShippingMethods = (url, zoneID = '') => request
	.get(`${url}/${zoneID}/methods/html`);

export const wpAPIShippingEndicia = (url, query) => request
	.post(`${url}/methods/endicia`)
	.set('Content-Type', 'application/json')
	.query(query);

export const wpAPICouponCodes = (couponCodeURL = '', queryObj = {}, couponsNonce = '') => request
	.post(couponCodeURL)
	.set('Content-Type', 'application/json')
	.set('X-WP-Nonce', couponsNonce)
	.query(queryObj)
	.timeout({
		response: 15000,  // Wait 15 seconds for the server to start sending,
		deadline: 30000, // but allow 30 seconds for the request to finish processing.
	});
