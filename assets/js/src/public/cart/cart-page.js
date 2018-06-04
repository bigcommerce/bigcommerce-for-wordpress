/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */

import * as tools from '../../utils/tools';
import { NLS } from '../config/i18n';
import queryString from '../../utils/data/query-to-json';

const el = {
	container: tools.getNodes('bc-cart')[0],
};

const handleCartErrorOnPageLoad = () => {
	const queryObj = queryString();
	const APIError = queryObj['api-error'];

	if (!APIError) {
		return;
	}

	if (APIError === '502') {
		el.APIErrorNotification.innerHTML = NLS.cart.add_to_cart_error_502;
		tools.closest(el.APIErrorNotification, '.bc-cart-error').classList.add('message-active');
	}
};

const cacheElements = () => {
	el.APIErrorNotification = tools.getNodes('bc-cart-error-message', false, el.container, false)[0];
};

const bindEvents = () => {
	handleCartErrorOnPageLoad();
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
