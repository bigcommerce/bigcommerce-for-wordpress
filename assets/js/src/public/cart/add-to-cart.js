/**
 * @module Add to Cart Ajax
 * @description Add to cart ajax script for adding items to the cart instead of a page reload.
 */

import _ from 'lodash';
import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { getCookie } from '../../utils/storage/cookie';
import { CART_API_BASE, AJAX_CART_ENABLED, AJAX_CART_NONCE } from '../config/wp-settings';
import { wpAPIAddToCartAjax } from '../../utils/ajax';
import { NLS } from '../config/i18n';

const state = {
	isFetching: false,
	ajax_enabled: AJAX_CART_ENABLED,
	cartItem: {
		product_id: '',
		variant_id: '',
		options: {},
		modifiers: {},
		quantity: 1,
	},
};

const el = {
	buttons: tools.getNodes('.bc-btn--add_to_cart', true, document, true),
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

		if (key === 'options' || key === 'modifiers') {
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
 * @function handleProductOptions
 * @description Parse the options object for a selected variant and set it to the cartItem state.
 * @param form
 * @param variants
 */
const handleProductOptions = (form, variants) => {
	const variantID = tools.getNodes('variant_id', false, form)[0].value;

	Object.values(variants).forEach((variantObject) => {
		if (variantObject.variant_id.toString() !== variantID.toString()) {
			return;
		}

		Object.values(variantObject.options).forEach((optionValue, index) => {
			state.cartItem.options[index] = {
				id: optionValue.option_id,
				value: optionValue.id,
			};
		});
	});
};

/**
 * @function handleProductModifiers
 * @description Parse the modifiers object for a selected variant and set it to the cartItem state.
 * @param modifiers
 */
const handleProductModifiers = (modifiers = {}) => {
	modifiers.forEach((field, index) => {
		if (!field.value) {
			return;
		}

		state.cartItem.modifiers[index] = {
			id: field.dataset.modifierId,
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
	state.cartItem.modifiers = {};
	state.cartItem.quantity = 1;

	const form = tools.closest(button, '.bc-product-form');
	const hasOptions = tools.getNodes('product-variants-object', false, form)[0];
	const hasModifiers = tools.getNodes('bc-product-modifier-field', true, form);
	const qty = tools.getNodes('.bc-product-form__quantity-input', false, form, true)[0];

	// Always need a product_id
	state.cartItem.product_id = button.dataset.js;

	// Set the quantity to be added to the cart
	state.cartItem.quantity = qty ? qty.value : 1;

	// Product Card or product without options.
	if (!hasOptions && !hasModifiers) {
		return buildAjaxQueryString();
	}

	// Handle Options
	if (hasOptions) {
		const variants = JSON.parse(hasOptions.dataset.variants);
		if (variants.length > 1) {
			handleProductOptions(form, variants);
		}
	}

	// Handle Modifiers
	if (hasModifiers) {
		handleProductModifiers(hasModifiers);
	}

	return buildAjaxQueryString();
};

/**
 * @function updateCartItemCount
 * @description Upon successfully adding a item to the cart, get the new cart count from the response and update the cart menu item.
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
	messageElement.textContent = message;
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

	const cartID = getCookie('bigcommerce_cart_id');
	const url = cartID ? `${CART_API_BASE}/${cartID}` : CART_API_BASE;
	const query = getAjaxQueryString(cartButton);

	handleFetchingState(cartButton);
	wpAPIAddToCartAjax(url, query)
		.set('X-WP-Nonce', AJAX_CART_NONCE)
		.end((err, res) => {
			state.isFetching = false;
			handleFetchingState(cartButton);

			// TODO: If/when we ever get more detailed error responses with error codes, we'll need to address them here.
			if (err) {
				console.error(err);
				createAjaxResponseMessage(form, NLS.cart.ajax_add_to_cart_error, true);
				return;
			}

			createAjaxResponseMessage(form, NLS.cart.ajax_add_to_cart_success, false);
			updateCartItemCount(res.body);
		});
};

const bindEvents = () => {
	delegate('.bc-btn--add_to_cart', 'click', handleAjaxAddToCartRequest);
};

const init = () => {
	if (!state.ajax_enabled || el.buttons.length === 0) {
		return;
	}

	bindEvents();
};

export default init;
