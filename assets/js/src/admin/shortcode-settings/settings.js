/**
 * @module Cart Settings
 * @description Cart settings options UI scripts.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';

const el = {};

/**
 * @function toggleCartOption
 * @description toggle the <tr> elements on and off depending on checkbox state.
 */
const toggleCartOption = () => {
	const tableRow = tools.closest(el.checkBox, 'tr');
	const tableRowSibling = tableRow.nextElementSibling;

	if (!el.checkBox.checked) {
		tableRowSibling.style.display = 'none';
		return;
	}

	tableRowSibling.style.display = 'table-row';
};

const cacheElements = () => {
	el.checkBox = tools.getNodes('input[name="bigcommerce_enable_cart"]', false, el.container, true)[0];
};

/**
 * @function bindEvents
 * @description bind all event listeners to this function.
 */
const bindEvents = () => {
	delegate(el.container, 'input[name="bigcommerce_enable_cart"]', 'change', toggleCartOption);
};

const init = (container) => {
	if (!container) {
		return;
	}

	el.container = container;
	cacheElements();
	toggleCartOption();
	bindEvents();
};

export default init;
