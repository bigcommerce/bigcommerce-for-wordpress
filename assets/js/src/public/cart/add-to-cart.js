/**
 * @module Add to Cart Ajax
 * @description Add to cart ajax script for adding items to the cart instead of a page reload.
 */

import _ from 'lodash';
import delegate from 'delegate';
import Cookie from 'js-cookie';
import * as tools from 'utils/tools';
import { trigger } from 'utils/events';
import { wpAPIAddToCartAjax } from 'utils/ajax';
import { CART_API_BASE, AJAX_CART_ENABLED, AJAX_CART_NONCE } from 'publicConfig/wp-settings';
import { CART_ID_COOKIE_NAME } from 'bcConstants/cookies';
import { AJAX_CART_UPDATE } from 'bcConstants/events';
import { NLS } from 'publicConfig/i18n';
import { cartMenuSet, updateFlatsomeCartMenuQty, updateFlatsomeCartMenuPrice } from './cart-menu-item';

const state = {
	isFetching: false,
	ajax_enabled: AJAX_CART_ENABLED,
	cartItem: {
		product_id: '',
		variant_id: '',
		options: {},
		quantity: 1,
	},
	cartMessage: '',
};

/**
 * @function buildAjaxQueryString
 * @description Build a query string of all options and modifiers for a selected variant.
 * @returns {string}
 */
const buildAjaxQueryString = () => {
	const str = [];

	Object.entries(state.cartItem).forEach(([key, value]) => {
		if (!value || value.length === 0) {
			return;
		}

		if (key === 'options') {
			Object.entries(value).forEach(([objectKey, objectValue], index) => {
				Object.entries(objectValue).forEach(([objKey, objValue]) => {
					const k = encodeURIComponent(objKey);
					const v = encodeURIComponent(objValue);
					str.push(`${key}[${index}][${k}]=${v}`);
				});
			});
		} else {
			const k = encodeURIComponent(key);
			const v = encodeURIComponent(value);
			str.push(`${k}=${v}`);
		}
	});

	return str ? str.join('&') : '';
};

/**
 * @function handleProductModifiers
 * @description Parse the modifiers object for a selected variant and set it to the cartItem state.
 * @param modifiers
 */
const handleProductModifiers = (modifiers = []) => {
	modifiers.forEach((field, index) => {
		// If a checkbox field is not checked, or a text/textarea field is blank, do not submit that data.
		if (!field.value || ((field.type === 'checkbox' || field.type === 'radio') && !field.checked)) {
			return;
		}

		state.cartItem.options[index] = {
			id: parseFloat(field.dataset.optionId),
			value: field.value,
		};
	});
};

/**
 * @function getAjaxQueryString
 * @description Reset the current cartItem object. Repopulate it with the current selected variant. Return the
 * 				query string to be submitted with the ajax call.
 * @param button
 * @returns {string}
 */
const getAjaxQueryString = (button) => {
	state.cartItem.product_id = '';
	state.cartItem.variant = '';
	state.cartItem.options = {};
	state.cartItem.quantity = 1;

	const form = tools.closest(button, '.bc-product-form');
	const hasOptions = tools.getNodes('bc-product-option-field', true, form);
	const qty = tools.getNodes('.bc-product-form__quantity-input', false, form, true)[0];

	// Always need a product_id
	state.cartItem.product_id = button.dataset.js;

	// Set the quantity to be added to the cart
	state.cartItem.quantity = qty ? qty.value : 1;

	// Product Card or product without options.
	if (!hasOptions || !hasOptions.length) {
		return buildAjaxQueryString();
	}

	// Handle Options
	handleProductModifiers(hasOptions);
	return buildAjaxQueryString();
};

/**
 * @function updateCartItemCount
 * @description Upon successfully adding a item to the cart, get the new cart count from the response and update the
 *     			cart menu item.
 * @param data
 */
const updateCartItemCount = (data = {}) => {
	const menuCartCount = tools.getNodes('.bigcommerce-cart__item-count', false, document, true)[0];
	if (!menuCartCount) {
		return;
	}

	let cartCount = 0;
	tools.removeClass(menuCartCount, 'full');

	Object.values(data.items).forEach((item) => {
		if (!item.quantity) {
			return;
		}

		cartCount += parseFloat(item.quantity);
	});

	_.delay(() => tools.addClass(menuCartCount, 'full'), 150);
	menuCartCount.textContent = cartCount.toString();
	cartMenuSet(cartCount);
};

/**
 * @function createAjaxResponseMessage
 * @description Construct a response message to be displayed on the page with the product submitted to the cart API.
 * @param wrapper {string} container for the message to be attached to.
 * @param message {string} Global error and success messages from the plugin's global i18n JS object.
 * @param error {boolean} Whether or not this response is an error.
 */
const createAjaxResponseMessage = (wrapper = '', message = '', error = false) => {
	const messageWrapper = tools.getNodes('bc-ajax-add-to-cart-message', false, wrapper)[0];
	if (!messageWrapper) {
		return;
	}

	const statusClass = error ? 'bc-alert--error' : 'bc-alert--success';
	const messageElement = document.createElement('p');
	tools.addClass(messageElement, 'bc-ajax-add-to-cart__message');
	tools.addClass(messageElement, 'bc-alert');
	tools.addClass(messageElement, statusClass);
	messageElement.innerHTML = message;
	messageWrapper.innerHTML = '';

	messageWrapper.appendChild(messageElement);
};

/**
 * @function handleFetchingState
 * @description While the ajax request is being performed, handle the cart button state and animations.
 * @param button
 */
const handleFetchingState = (button = '') => {
	if (!button) {
		return;
	}

	if (state.isFetching) {
		button.setAttribute('disabled', 'disabled');
		tools.addClass(button, 'bc-ajax-cart-processing');
		return;
	}

	button.removeAttribute('disabled');
	tools.removeClass(button, 'bc-ajax-cart-processing');
};

/**
 * @function handleCartErrors
 * @description Using the error response codes from the API response, set the corresponding message to print on the page.
 * @param response
 */
const handleCartErrors = (response) => {
	state.cartMessage = '';
	const status = response.data.status.toString();

	// If we're missing a status code, just use our default string.
	if (!status) {
		state.cartMessage = NLS.cart.ajax_add_to_cart_error;
	}

	// Map the error code to API's response message.
	switch (true) {
	case (status.charAt(0) === '4'):
		state.cartMessage = response.message;
		break;
	case (status.charAt(0) === '5'):
	default:
		state.cartMessage = NLS.cart.ajax_add_to_cart_error;
	}
};

/**
 * @function handleAjaxAddToCartRequest
 * @description Payload function that handles submitting the product to the cart API.
 * @param e
 */
const handleAjaxAddToCartRequest = (e) => {
	e.preventDefault();
	state.isFetching = true;

	const cartButton = e.delegateTarget;
	const form = tools.closest(cartButton, '.bc-product-form');

	if (!form.checkValidity()) {
		form.reportValidity(); // Check HTML5 form field validity and report on errors.
		return;
	}

	const cartID = Cookie.get(CART_ID_COOKIE_NAME);
	const url = cartID ? `${CART_API_BASE}/${cartID}` : CART_API_BASE;
	const query = getAjaxQueryString(cartButton);

	handleFetchingState(cartButton);
	wpAPIAddToCartAjax(url, query)
		.set('X-WP-Nonce', AJAX_CART_NONCE)
		.end((err, res) => {
			state.isFetching = false;
			handleFetchingState(cartButton);

			if (err) {
				console.error(err);
				handleCartErrors(res.body);
				createAjaxResponseMessage(form, state.cartMessage, true);
				return;
			}

			createAjaxResponseMessage(form, NLS.cart.ajax_add_to_cart_success, false);
			updateCartItemCount(res.body);
			updateFlatsomeCartMenuQty();
			updateFlatsomeCartMenuPrice(res.body);
			trigger({ event: AJAX_CART_UPDATE, native: false });
			trigger({ event: 'bigcommerce/analytics_trigger', data: { cartButton, cartID: res.body.cart_id }, native: false });
		});
};

const bindEvents = () => {
	delegate('.bc-btn--add_to_cart', 'click', handleAjaxAddToCartRequest);
};

const init = () => {
	if (!state.ajax_enabled) {
		return;
	}

	bindEvents();
};

export default init;
