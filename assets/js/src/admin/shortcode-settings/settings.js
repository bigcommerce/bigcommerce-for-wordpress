/**
 * @module Cart Settings
 * @description Cart settings options UI scripts.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';

const el = {};

const toggleSiblingOptions = (field) => {
	const tableRow = tools.closest(field, 'tr');
	const table = tools.closest(field, 'table');
	const siblings = tools.getNodes('tr', true, table, true);
	const display = field.checked ? 'table-row' : 'none';

	siblings.forEach((row) => {
		if (row !== tableRow) {
			row.style.display = display;
		}
	});
};

/**
 * @function toggleCartOption
 * @description toggle the <tr> elements on and off depending on checkbox state.
 */
const toggleCartOption = () => {
	toggleSiblingOptions(el.cartCheckbox);
};

/**
 * @function toggleGiftCertificateOption
 * @description toggle the <tr> elements on and off depending on checkbox state.
 */
const toggleGiftCertificateOption = () => {
	toggleSiblingOptions(el.giftCertificateCheckbox);
};

const cacheElements = () => {
	el.cartCheckbox = tools.getNodes('input[name="bigcommerce_enable_cart"]', false, el.container, true)[0];
	el.giftCertificateCheckbox = tools.getNodes('input[name="bigcommerce_enable_gift_certificates"]', false, el.container, true)[0];
};

/**
 * @function bindEvents
 * @description bind all event listeners to this function.
 */
const bindEvents = () => {
	delegate(el.container, 'input[name="bigcommerce_enable_cart"]', 'change', toggleCartOption);
	delegate(el.container, 'input[name="bigcommerce_enable_gift_certificates"]', 'change', toggleGiftCertificateOption);
};

const init = (container) => {
	if (!container) {
		return;
	}

	el.container = container;
	cacheElements();
	toggleCartOption();
	toggleGiftCertificateOption();
	bindEvents();
};

export default init;
