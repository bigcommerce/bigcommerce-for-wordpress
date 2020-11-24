/**
 * @module Shipping Calculator
 * @description Shipping estimate calculator
 */

import * as tools from 'utils/tools';
import delegate from 'delegate';
import cartState from 'publicConfig/cart-state';
import { Spinner } from 'spin.js/spin';
import { AJAX_CART_UPDATE, HANDLE_CART_STATE } from 'bcConstants/events';
import { SHIPPING_API_ZONES, SHIPPING_API_METHODS } from 'publicConfig/wp-settings';
import { NLS } from 'publicConfig/i18n';
import { wpAPIGetShippingZones, wpAPIGetShippingMethods } from 'utils/ajax';
import { on, trigger } from 'utils/events';

const el = {
	calculator: tools.getNodes('bc-shipping-calculator', false, document)[0],
};

/**
 * @description Module State object.
 * @type {{shippingItemCount: number, fetching: boolean, spinner: string, isActive: boolean, hasError: boolean, subtotal: string}}
 */
const state = {
	fetching: false,
	isActive: false,
	hasError: false,
	spinner: '',
	shippingItemCount: 0,
	subtotal: '',
};

/**
 * @function handleShippingError
 * @description Handle errors and error messaging for shipping calculator requests.
 */
const handleShippingError = () => {
	const errorMessage = tools.getNodes('.bc-shipping-error', false, el.calculator, true)[0];

	if (state.hasError && !errorMessage) {
		const errorWrapper = document.createElement('div');
		tools.addClass(errorWrapper, 'bc-shipping-error');
		errorWrapper.innerText = NLS.cart.shipping_calc_error;

		el.calculator.appendChild(errorWrapper);
		return;
	}

	if (!state.hasError && errorMessage) {
		errorMessage.parentNode.removeChild(errorMessage);
	}
};

/**
 * @function setValidCartCount
 * @description if we have a 'peritem' shipping option, count and store the number of valid items to ship.
 * @param data
 */
const setValidCartCount = (data) => {
	// Count Items
	let count = 0;
	Object.values(data.items).forEach((item) => {
		if (item.bigcommerce_product_type[0].slug === 'digital') {
			return;
		}

		count += item.quantity;
	});

	state.shippingItemCount = count;
};

/**
 * @function resetShippingCalculator
 * @description reset the shipping calculator if the cart is updated via ajax.
 */
const resetShippingCalculator = (e) => {
	state.isActive = false;
	state.hasError = false;
	handleShippingError();

	// On a cart ajax refresh event
	if (e.detail.cartData) {
		// First set the subtotal to the cart state.
		state.subtotal = e.detail.cartData.subtotal.formatted;
		// If the only remaining items in the card are digital goods, remove the shipping calculator all together.
		setValidCartCount(e.detail.cartData);
		if (state.shippingItemCount === 0) {
			el.calculator.parentNode.removeChild(el.calculator);
			return;
		}
	}

	// Update the subtotal field with the current subtotal in state.
	el.currentSubtotal.innerText = state.subtotal;

	// If we have any shipping fields, remove them!
	const shippingFields = tools.getNodes('.bc-shipping-calculator-fields', false, el.calculator, true)[0];
	if (!shippingFields) {
		return;
	}

	shippingFields.parentNode.removeChild(shippingFields);
};

/**
 * @function createSpinner
 * @description create a spinner instance to be used if the shipping calculator is requested.
 */
const createSpinner = () => {
	const spinnerOptions = {
		opacity: 0.5,
		scale: 0.5,
		lines: 12,
	};

	new Spinner(spinnerOptions).spin(el.spinner);
};

/**
 * @function handleSpinnerState
 * @description handle the state of the cart, the spinner and the fields during a fetch request.
 */
const handleSpinnerState = () => {
	if (cartState.isFetching) {
		trigger({ event: HANDLE_CART_STATE, native: false });
		tools.addClass(el.spinner, 'show-spinner');
		return;
	}

	tools.removeClass(el.spinner, 'show-spinner');
	trigger({ event: HANDLE_CART_STATE, native: false });
};

/**
 * @function getZones
 * @description Get the shipping zones associated with this store.
 */
const getZones = () => {
	if (state.isActive) {
		return;
	}

	state.isActive = true;
	cartState.isFetching = true;
	handleSpinnerState();

	wpAPIGetShippingZones(SHIPPING_API_ZONES)
		.end((err, res) => {
			cartState.isFetching = false;
			handleSpinnerState();

			if (err) {
				state.hasError = true;
				state.isActive = false;
				handleShippingError();
				console.error(err);
				return;
			}

			const html = res.body.rendered;
			const fieldsWrapper = document.createElement('div');
			// Remove any error messages on success.
			state.hasError = false;
			handleShippingError();
			tools.addClass(fieldsWrapper, 'bc-shipping-calculator-fields');
			fieldsWrapper.innerHTML = html;
			el.calculator.appendChild(fieldsWrapper);
			el.currentSubtotal.innerText = state.subtotal;
		});
};

/**
 * @function getMethods
 * @description get the shipping methods associated with the selected shipping zone.
 * @param e
 */
const getMethods = (e) => {
	cartState.isFetching = true;
	handleSpinnerState();

	wpAPIGetShippingMethods(SHIPPING_API_METHODS, e.delegateTarget.value)
		.end((err, res) => {
			cartState.isFetching = false;
			handleSpinnerState();

			if (err) {
				state.hasError = true;
				handleShippingError();
				console.error(err);
				return;
			}

			const html = res.body.rendered;
			const fieldsWrapper = tools.getNodes('.bc-shipping-calculator-fields', false, el.calculator, true)[0];
			const methods = tools.getNodes('bc-shipping-methods', false, el.calculator)[0];

			if (!fieldsWrapper) {
				return;
			}

			// Remove any error messages on success.
			state.hasError = false;
			handleShippingError();

			if (methods) {
				methods.parentNode.removeChild(methods);
			}

			fieldsWrapper.insertAdjacentHTML('beforeend', html);
			el.currentSubtotal.innerText = state.subtotal;
		});
};

/**
 * @function updateShippingCosts
 * @description find the cart subtotal node and update it with the new price.
 * @param shippingOption
 */
const updateShippingCosts = (shippingOption) => {
	const subtotal = shippingOption.dataset.cartSubtotal;
	const subtotalContainer = tools.getNodes('.bc-cart-subtotal__amount', false, document, true)[0];

	subtotalContainer.innerText = subtotal;
};

/**
 * @function updateCartPrice
 * @description payload function. checking to see we have valid state and elements first, then update the subtotal.
 */
const updateCartPrice = () => {
	const shippingOption = tools.getNodes('input[name="shipping-method"]:checked', false, el.calculator, true)[0];

	if (!shippingOption) {
		state.hasError = true;
		handleShippingError();
		return;
	}

	// Clear and reset the module state
	state.shippingItemCount = 0;

	// Remove old error message on new request.
	state.hasError = false;
	handleShippingError();

	updateShippingCosts(shippingOption);
};

const cacheElements = () => {
	el.spinner = tools.getNodes('bc-loader', false, el.calculator)[0];
	el.currentSubtotal = tools.getNodes('[data-subtotal]', false, document, true)[0];
	if (el.currentSubtotal) {
		state.subtotal = el.currentSubtotal.dataset.subtotal;
	}
};

const bindEvents = () => {
	delegate(el.calculator, '[data-js="shipping-calculator-toggle"]', 'click', getZones);
	delegate(el.calculator, '[data-js="bc-shipping-zones"]', 'change', getMethods);
	delegate(el.calculator, '[data-js="shipping-calculator-update"]', 'click', updateCartPrice);
	on(document, AJAX_CART_UPDATE, resetShippingCalculator);
};

const init = () => {
	if (!el.calculator) {
		return;
	}

	bindEvents();
	cacheElements();
	createSpinner();
};

export default init;
