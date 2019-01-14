/**
 * @module Cart Settings
 * @description Cart settings options UI scripts.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';

const el = {
	container: tools.getNodes('.bigcommerce_product_page_bigcommerce', false, document, true)[0],
};

/**
 * @function toggleDependentOptions
 * @description toggle table elements display on settings pages.
 *
 * @param field
 * @param dependents
 */
const toggleDependentOptions = (field, dependents) => {
	const display = field.checked ? 'table-row' : 'none';
	dependents.forEach((dependent) => {
		const row = tools.closest(dependent, 'tr');
		row.style.display = display;
	});
};

/**
 * @function toggleCartOption
 * @description toggle the <tr> elements on and off depending on checkbox state.
 */
const toggleCartOption = () => {
	toggleDependentOptions(el.cartCheckbox, [el.cartAjaxCheckbox, el.cartPageSelect]);
};

/**
 * @function toggleCheckoutOption
 * @description toggle the <tr> elements on and off depending on checkbox state.
 */
const toggleCheckoutOption = () => {
	toggleDependentOptions(el.checkoutCheckbox, [el.checkoutPageSelect]);
};

/**
 * @function toggleGiftCertificateOption
 * @description toggle the <tr> elements on and off depending on checkbox state.
 */
const toggleGiftCertificateOption = () => {
	toggleDependentOptions(el.giftCertificateCheckbox, [el.gitfCertificatePageSelect, el.gittBalancePageSelect]);
};

const cacheElements = () => {
	el.cartCheckbox = tools.getNodes('input[name="bigcommerce_enable_cart"]', false, el.container, true)[0];
	el.cartAjaxCheckbox = tools.getNodes('input[name="bigcommerce_ajax_cart"]', false, el.container, true)[0];
	el.cartPageSelect = tools.getNodes('select[name="bigcommerce_cart_page_id"]', false, el.container, true)[0];
	el.checkoutCheckbox = tools.getNodes('input[name="bigcommerce_enable_embedded_checkout"]', false, el.container, true)[0];
	el.checkoutPageSelect = tools.getNodes('select[name="bigcommerce_checkout_page_id"]', false, el.container, true)[0];
	el.giftCertificateCheckbox = tools.getNodes('input[name="bigcommerce_enable_gift_certificates"]', false, el.container, true)[0];
	el.gitfCertificatePageSelect = tools.getNodes('select[name="bigcommerce_gift_certificate_page_id"]', false, el.container, true)[0];
	el.gittBalancePageSelect = tools.getNodes('select[name="bigcommerce_gift_balance_page_id"]', false, el.container, true)[0];
};

/**
 * @function bindEvents
 * @description bind all event listeners to this function.
 */
const bindEvents = () => {
	delegate(el.container, 'input[name="bigcommerce_enable_cart"]', 'change', toggleCartOption);
	delegate(el.container, 'input[name="bigcommerce_enable_embedded_checkout"]', 'change', toggleCheckoutOption);
	delegate(el.container, 'input[name="bigcommerce_enable_gift_certificates"]', 'change', toggleGiftCertificateOption);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	toggleCartOption();
	toggleCheckoutOption();
	toggleGiftCertificateOption();
	bindEvents();
};

export default init;
