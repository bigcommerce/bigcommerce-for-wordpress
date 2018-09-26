/**
 * @module Cart Items Ajax
 * @description Ajax handling for cart items.
 */

import delegate from 'delegate';
import _ from 'lodash';
import Cookies from 'js-cookie';
import * as tools from '../../utils/tools';
import { wpAPICartUpdate, wpAPICartDelete } from '../../utils/ajax';
import { CART_API_BASE } from '../config/wp-settings';
import { NLS } from '../config/i18n';
import cartState from '../config/cart-state';
import { cartEmpty } from './cart-templates';
import { CART_ID_COOKIE_NAME } from '../../constants/cookies';
import { cartMenuSet, updateMenuQtyTotal, updateCartMenuItem } from './cart-menu-item';

const el = {
	container: tools.getNodes('bc-cart')[0],
};

const timeoutOptions = {
	delay: 500,
};

let timeout = null;

/**
 * @function getCartAPIURL
 * @description build the cart API endpoint url.
 * @param e
 * @returns {string}
 */
const getCartAPIURL = (e) => {
	const cartID = el.container.dataset.cart_id;
	const cartItem = e.delegateTarget.dataset.cart_item_id;

	return _.isEmpty(cartID && cartItem) ? '' : `${CART_API_BASE}/${cartID}${NLS.cart.items_url_param}${cartItem}`;
};

/**
 * @function getItemUpdateQueryString
 * @description build the query string for the current item being updated.
 * @param input
 * @returns {string}
 */
const getItemUpdateQueryString = (input = '') => {
	const quantity = input.delegateTarget.value;
	const k = encodeURIComponent(NLS.cart.quantity_param);
	const v = encodeURIComponent(quantity);

	return _.isEmpty(quantity) ? '' : `${k}=${v}`;
};

/**
 * @function handleCartState
 * @description check the cart state and disable/enable actions on current status.
 */
const handleCartState = () => {
	if (cartState.isFetching) {
		el.itemInputs.forEach((item) => {
			item.setAttribute('disabled', 'disabled');
		});
		el.itemRemoveButtons.forEach((item) => {
			item.setAttribute('disabled', 'disabled');
		});
		el.checkoutButton.setAttribute('disabled', 'disabled');
		el.container.classList.add('bc-updating-cart');

		return;
	}

	el.container.classList.remove('bc-updating-cart');
	el.itemInputs.forEach((item) => {
		item.removeAttribute('disabled');
	});
	el.itemRemoveButtons.forEach((item) => {
		item.removeAttribute('disabled');
	});
	el.checkoutButton.removeAttribute('disabled', 'disabled');
};

/**
 * @function updateCartItems
 * @description update the cart item total value.
 * @param data {object}
 */
const updateCartItems = (data = {}) => {
	Object.entries(data.items).forEach(([key, value]) => {
		const id = key;
		const totalSalePrice = value.total_sale_price.formatted;
		const itemRow = tools.getNodes(id, false, el.container, false)[0];
		const totalPrice = tools.getNodes('.bc-cart-item-total-price', false, itemRow, true)[0];

		totalPrice.innerHTML = totalSalePrice;
	});
};

/**
 * @function updatedCartTotals
 * @description update the cart subtotal amount.
 * @param data
 */
const updatedCartTotals = (data = {}) => {
	const baseAmount = data.subtotal.formatted;
	const subTotal = tools.getNodes('.bc-cart-subtotal__amount', false, el.container, true)[0];
	const taxAmount = data.tax_amount.formatted;
	const taxTotal = tools.getNodes('.bc-cart-tax__amount', false, el.container, true)[0];

	subTotal.textContent = baseAmount;
	taxTotal.textContent = taxAmount;
};

/**
 * @function cartItemQtyUpdated
 * @description handle the API response when a cart item is updated.
 * @param data
 */
const cartItemQtyUpdated = (data = {}) => {
	if (!data) {
		return;
	}

	updateCartItems(data);
	updatedCartTotals(data);
	updateMenuQtyTotal(data);
};

const bcAPICodeResponseHandler = (data = {}) => {
	if (!el.APIErrorNotification) {
		return;
	}

	if (data.statusCode === 502) {
		el.APIErrorNotification.innerHTML = NLS.cart.cart_error_502;
		tools.closest(el.APIErrorNotification, '.bc-cart-error').classList.add('message-active');

		return;
	}

	el.APIErrorNotification.innerHTML = '';
	tools.closest(el.APIErrorNotification, '.bc-cart-error').classList.remove('message-active');
};

/**
 * @function handleQtyUpdate
 * @description after an item qty has been updated, run ajax to update the cart.
 * @param input
 */
const handleQtyUpdate = (input) => {
	if (input.delegateTarget.value.length <= 0) {
		return;
	}

	const cartURL = getCartAPIURL(input);
	const queryString = getItemUpdateQueryString(input);
	window.clearTimeout(timeout);

	timeout = _.delay(() => {
		cartState.isFetching = true;
		handleCartState();

		wpAPICartUpdate(cartURL, queryString)
			.end((err, res) => {
				cartState.isFetching = false;
				handleCartState();
				bcAPICodeResponseHandler(res);

				if (err) {
					console.error(err);
					return;
				}

				cartItemQtyUpdated(res.body);
			});
	}, timeoutOptions.delay);
};

/**
 * @function removeCartItem
 * @description remove a cart item row from the cart view DOM.
 * @param itemRow string
 * @param data {object}
 */
const removeCartItem = (itemRow = '', data = {}) => {
	itemRow.parentNode.removeChild(itemRow);

	if (data.statusCode === 204) {
		el.cartBody.insertAdjacentHTML('afterbegin', cartEmpty);
		el.cartFooter.parentNode.removeChild(el.cartFooter);
		Cookies.remove(CART_ID_COOKIE_NAME);
		cartMenuSet(0);
		updateCartMenuItem();
		return;
	}

	updatedCartTotals(data.body);
	updateMenuQtyTotal(data.body);
};

/**
 * @function handleCartItemRemoval
 * @description send and handle the API response for removal of a cart item.
 * @param e
 */
const handleCartItemRemoval = (e) => {
	const cartURL = getCartAPIURL(e);

	cartState.isFetching = true;
	handleCartState();

	wpAPICartDelete(cartURL)
		.end((err, res) => {
			const itemRow = tools.closest(e.delegateTarget, `[data-js="${e.delegateTarget.dataset.cart_item_id}"]`);

			cartState.isFetching = false;
			handleCartState();
			bcAPICodeResponseHandler(res);

			if (err) {
				console.error(err);
				return;
			}

			removeCartItem(itemRow, res);
		});
};

const cacheElements = () => {
	el.itemInputs = tools.getNodes('bc-cart-item__quantity', true, el.container, false);
	el.cartBody = tools.getNodes('.bc-cart-body', false, el.container, true)[0];
	el.cartFooter = tools.getNodes('.bc-cart-footer', false, el.container, true)[0];
	el.itemRemoveButtons = tools.getNodes('.bc-cart-item__remove-button', true, el.container, true);
	el.checkoutButton = tools.getNodes('proceed-to-checkout', false, el.container, false)[0];
	el.APIErrorNotification = tools.getNodes('bc-cart-error-message', false, el.container, false)[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-cart-item__quantity"]', 'input', handleQtyUpdate);
	delegate(el.container, '[data-js="remove-cart-item"]', 'click', handleCartItemRemoval);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
