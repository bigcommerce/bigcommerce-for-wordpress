/**
 * @module Coupon Codes
 * @description Scripts to handle cart submission of coupon codes.
 */

import * as tools from 'utils/tools';
import delegate from 'delegate';
import { wpAPICouponCodes } from 'utils/ajax';
import { on, trigger } from 'utils/events';
import { COUPON_CODE_ADD, COUPON_CODE_REMOVE, AJAX_CART_NONCE } from 'publicConfig/wp-settings';
import { AJAX_CART_UPDATE, HANDLE_CART_STATE, HANDLE_COUPON_CODE } from 'bcConstants/events';
import { NLS } from 'publicConfig/i18n';
import cartState from 'publicConfig/cart-state';

const el = {
	container: tools.getNodes('bc-coupon-code')[0],
};

/**
 * @function updateCouponDiscount
 * @description Update discount amount on qty update or removal. Remove coupon section on empty cart.
 * @param e
 */
const updateCouponDiscount = (e) => {
	if (!e.detail.cartData) {
		el.container.parentNode.removeChild(el.container);
		return;
	}

	el.couponDetails.innerText = `${NLS.cart.coupon_discount}: -${e.detail.cartData.coupons[0].discounted_amount.formatted}`;
};

/**
 * @function handleCouponSuccess
 * @description Update cart data when a coupon has been applied.
 * @param cartObject
 */
const handleCouponSuccess = (cartObject = {}) => {
	if (!cartObject) {
		return;
	}

	const couponCode = cartObject.coupons[0].code;

	el.container.classList.add('bc-hide-add-form');
	el.container.classList.remove('bc-hide-remove-form');
	el.addCouponForm.setAttribute('aria-hidden', true);
	el.removeCouponForm.setAttribute('aria-hidden', false);
	el.cartErrorWrapper.classList.remove('message-active');
	el.cartError.innerText = NLS.cart.coupon_success;
	el.removeCouponButton.dataset.couponCode = couponCode;
	el.removeCouponTitle.innerText = couponCode;
	el.couponField.value = '';
	el.couponDetails.innerText = `${NLS.cart.coupon_discount}: -${cartObject.coupons[0].discounted_amount.formatted}`;
};

/**
 * @function handleCouponRemoval
 * @description Update cart data when a coupon has been removed.
 */
const handleCouponRemoval = () => {
	el.container.classList.add('bc-hide-remove-form');
	el.container.classList.remove('bc-hide-add-form');
	el.addCouponForm.setAttribute('aria-hidden', false);
	el.removeCouponForm.setAttribute('aria-hidden', true);
	el.cartErrorWrapper.classList.remove('message-active');
	el.cartError.innerText = NLS.cart.coupon_success;
	el.removeCouponButton.dataset.couponCode = '';
	el.removeCouponTitle.innerText = '';
	el.couponDetails.innerText = '';
};

/**
 * @function handleCouponAddError
 * @description Handle coupon errors when adding.
 */
const handleCouponAddError = () => {
	el.couponField.focus();
	el.container.classList.add('bc-hide-remove-form');
	el.container.classList.remove('bc-hide-add-form');
	el.cartErrorWrapper.classList.add('message-active');
	el.cartError.innerText = NLS.cart.coupon_error;
};

/**
 * @function handleCouponRemoveError
 * @description Handle coupon errors when removing.
 */
const handleCouponRemoveError = () => {
	el.cartErrorWrapper.classList.remove('message-active');
	el.cartError.innerText = NLS.cart.coupon_removal_error;
	el.container.classList.add('bc-hide-remove-form');
	el.container.classList.remove('bc-hide-add-form');
};

/**
 * @function handleCouponCodeAdd
 * @description Main coupon function to apply a coupon to the cart.
 */
const handleCouponCodeAdd = () => {
	if (!COUPON_CODE_ADD) {
		return;
	}

	const queryObject = {
		coupon_code: el.couponField.value,
	};

	cartState.isFetching = true;
	trigger({ event: HANDLE_CART_STATE, native: false });

	wpAPICouponCodes(COUPON_CODE_ADD, queryObject, AJAX_CART_NONCE)
		.end((err, res) => {
			cartState.isFetching = false;
			trigger({ event: HANDLE_CART_STATE, native: false });

			if (err || res.body.error) {
				console.error(err, res.body ? res.body.error : '');
				handleCouponAddError();
				return;
			}

			trigger({ event: HANDLE_COUPON_CODE, data: { data: res.body }, native: false });
			handleCouponSuccess(res.body);
		});
};

/**
 * @function handleCouponCodeRemove
 * @description Main coupon function to remove a coupon from the cart.
 * @param e
 */
const handleCouponCodeRemove = (e) => {
	if (!COUPON_CODE_REMOVE) {
		return;
	}

	const queryObject = {
		coupon_code: e.delegateTarget.dataset.couponCode,
	};

	cartState.isFetching = true;
	trigger({ event: HANDLE_CART_STATE, native: false });

	wpAPICouponCodes(COUPON_CODE_REMOVE, queryObject, AJAX_CART_NONCE)
		.end((err, res) => {
			cartState.isFetching = false;
			trigger({ event: HANDLE_CART_STATE, native: false });

			if (err || res.body.error) {
				console.error(err, res.body ? res.body.error : '');
				handleCouponRemoveError();
				return;
			}

			handleCouponRemoval();
			trigger({ event: HANDLE_COUPON_CODE, data: { data: res.body }, native: false });
		});
};

const cacheElements = () => {
	el.addCouponForm = tools.getNodes('bc-add-coupon-form', false, el.container)[0];
	el.couponField = tools.getNodes('bc-coupon-code-field', false, el.container)[0];
	el.removeCouponForm = tools.getNodes('bc-remove-coupon-form', false, el.container)[0];
	el.removeCouponButton = tools.getNodes('bc-coupon-code-remove', false, el.container)[0];
	el.removeCouponTitle = tools.getNodes('.bc-coupon-name', false, el.removeCouponButton, true)[0];
	el.couponDetails = tools.getNodes('bc-coupon-details', false, el.container)[0];
	el.cartErrorWrapper = tools.getNodes('.bc-cart-error', false, document, true)[0];
	el.cartError = tools.getNodes('bc-cart-error-message', false, el.cartErrorWrapper)[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-coupon-code-submit"]', 'click', handleCouponCodeAdd);
	delegate(el.container, '[data-js="bc-coupon-code-remove"]', 'click', handleCouponCodeRemove);
	on(document, AJAX_CART_UPDATE, updateCouponDiscount);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
