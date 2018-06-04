/**
 * @module Product Results
 * @description handle product results interactions.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { selectedProduct } from './product-template';
import { I18N } from '../config/i18n';
import shortCodestate from '../config/shortcode-state';
import { on } from '../../utils/events';

const el = {};

/**
 * @function renderProductTemplate
 * @param product json product object
 */
const renderProductTemplate = (product = {}) => {
	const productData = {
		id: product.dataset.postid,
		bcid: product.dataset.bcid,
		title: product.dataset.title,
		price: product.dataset.price,
	};

	return selectedProduct(productData);
};

/**
 * @function addProduct
 * @description add a product to the selected sidebar list.
 * @param e
 */
const addProduct = (e) => {
	const product = e.delegateTarget;
	const resultsProduct = tools.getNodes(`[data-product="${e.delegateTarget.dataset.bcid}"]`, false, el.resultsContainer, true)[0];

	el.productList.insertAdjacentHTML('beforeend', renderProductTemplate(product));
	resultsProduct.classList.add('bc-shortcode-ui__selected-result');
	resultsProduct.querySelector('.bc-shortcode-ui__product-anchor-status').innerHTML = I18N.buttons.remove_product;
	shortCodestate.selectedProducts.post_id.push(e.delegateTarget.dataset.postid);
};

/**
 * @function removeProduct
 * @description remove a product from the selected sidebar list.
 * @param e
 */
const removeProduct = (e) => {
	const productID = e.delegateTarget.dataset.postid;
	const productBCID = e.delegateTarget.dataset.bcid;
	const product = tools.getNodes(`[data-product="${productBCID}"]`, false, el.productList, true)[0];
	const resultsProduct = tools.getNodes(`[data-product="${productBCID}"]`, false, el.resultsContainer, true)[0];
	const valIndex = shortCodestate.selectedProducts.post_id.indexOf(productID);

	el.productList.removeChild(product);
	resultsProduct.classList.remove('bc-shortcode-ui__selected-result');
	resultsProduct.querySelector('.bc-shortcode-ui__product-anchor-status').innerHTML = I18N.buttons.add_product;
	shortCodestate.selectedProducts.post_id.splice(valIndex, 1);
};

/**
 * @function addRemoveProduct
 * @description set a product in the results section to selected or not based on current state.
 * @param e
 */
const addRemoveProduct = (e) => {
	const product = tools.closest(e.delegateTarget, `[data-product="${e.delegateTarget.dataset.bcid}"]`);

	product.classList.toggle('bc-shortcode-ui__selected-result');
	if (!product.classList.contains('bc-shortcode-ui__selected-result')) {
		removeProduct(e);
		return;
	}

	addProduct(e);
};

/**
 * @function resetProducts
 * @description clear and reset all product selections in the Shortcode UI.
 */
const resetProducts = () => {
	shortCodestate.selectedProducts.post_id = [];

	tools.getNodes('.bc-shortcode-ui__selected-result', true, el.resultsContainer, true).forEach((product) => {
		tools.removeClass(product, 'bc-shortcode-ui__selected-result');
	});

	el.productList.innerHTML = '';
};

const cacheElements = () => {
	el.resultsContainer = tools.getNodes('bc-shortcode-ui-query-results')[0];
	el.selectedContainer = tools.getNodes('bc-shortcode-ui-selected-products')[0];
	el.productList = tools.getNodes('bc-shortcode-ui-product-list', false, el.selectedContainer)[0];
};

const bindEvents = () => {
	delegate(el.resultsContainer, '[data-js="add-product"]', 'click', addProduct);
	delegate(el.resultsContainer, '[data-js="add-remove-product"]', 'click', addRemoveProduct);
	delegate(el.selectedContainer, '[data-js="remove-product"]', 'click', removeProduct);
	on(document, 'bigcommerce/reset_shortcode_ui', resetProducts);
};

const init = () => {
	cacheElements();
	bindEvents();
};

export default init;
