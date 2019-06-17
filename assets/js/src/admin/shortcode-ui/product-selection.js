/**
 * @module Product Results
 * @description handle product results interactions.
 */

import _ from 'lodash';
import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { selectedProduct } from './product-template';
import { I18N } from '../config/i18n';
import shortcodeState from '../config/shortcode-state';
import { on, trigger } from '../../utils/events';
import { wpAPIProductLookup } from '../../utils/ajax';

const el = {};

/**
 * @function addProduct
 * @description add a product to the selected sidebar list.
 * @param e
 */
const addProduct = (e) => {
	const product = e.delegateTarget;
	const resultsProduct = tools.getNodes(`[data-product="${e.delegateTarget.dataset.bcid}"]`, false, el.resultsContainer, true)[0];
	const productData = {
		id: product.dataset.postid,
		bcid: product.dataset.bcid,
		title: product.dataset.title,
		price: product.dataset.price,
	};

	el.productList.insertAdjacentHTML('beforeend', selectedProduct(productData));
	resultsProduct.classList.add('bc-shortcode-ui__selected-result');
	resultsProduct.querySelector('.bc-shortcode-ui__product-anchor-status').textContent = I18N.buttons.remove_product;
	shortcodeState.selectedProducts.bc_id.push(e.delegateTarget.dataset.bcid);
	trigger({ event: 'bigcommerce/shortcode_product_list_event', native: false });
};

/**
 * @function removeProduct
 * @description remove a product from the selected sidebar list.
 * @param e
 */
const removeProduct = (e) => {
	const productBCID = e.delegateTarget.dataset.bcid;
	const product = tools.getNodes(`[data-product="${productBCID}"]`, false, el.productList, true)[0];
	const resultsProduct = tools.getNodes(`[data-product="${productBCID}"]`, false, el.resultsContainer, true)[0];
	const bcIdIndex = shortcodeState.selectedProducts.bc_id.indexOf(productBCID);

	if (resultsProduct) {
		resultsProduct.classList.remove('bc-shortcode-ui__selected-result');
		resultsProduct.querySelector('.bc-shortcode-ui__product-anchor-status').textContent = I18N.buttons.add_product;
	}

	el.productList.removeChild(product);
	shortcodeState.selectedProducts.bc_id.splice(bcIdIndex, 1);
	trigger({ event: 'bigcommerce/shortcode_product_list_event', native: false });
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
 * @function resetProductList
 * @description clear the product list selections and state when triggering the bigcommerce/set_shortcode_ui_state event.
 */
const resetProductsList = () => {
	tools.getNodes('.bc-shortcode-ui__selected-result', true, el.resultsContainer, true).forEach((product) => {
		tools.removeClass(product, 'bc-shortcode-ui__selected-result');
	});

	el.productList.textContent = '';
};

/**
 * @function populateSavedUIProductList
 * @description Add saved UI products to the products list.
 * @param e
 */
const populateSavedUIProductList = (e) => {
	if (!e.detail.params) {
		return;
	}

	const currentBlockBCIDs = e.detail.params.id;

	if (!currentBlockBCIDs || currentBlockBCIDs.length <= 0) {
		return;
	}

	const str = [];
	const bcidk = encodeURIComponent('bcid');
	const bcidv = encodeURIComponent(currentBlockBCIDs);
	str.push(`${bcidk}=${bcidv}`);
	const channelk = encodeURIComponent('bigcommerce_channel');
	const channelv = encodeURIComponent('-1');
	str.push(`${channelk}=${channelv}`);
	const queryString = str ? str.join(I18N.operations.query_string_separator) : '';
	let selectedProductsNodes = '';

	shortcodeState.isFetching = true;

	wpAPIProductLookup(queryString)
		.end((err, res) => {
			shortcodeState.isFetching = false;

			if (err) {
				// TODO: get debug status and only display if true.
				console.error(err);
				return;
			}

			res.body.forEach((product) => {
				const productData = {
					id: product.post_id,
					bcid: product.bigcommerce_id,
					title: product.title,
					price: product.price_range,
				};

				selectedProductsNodes += selectedProduct(productData);
			});

			el.productList.insertAdjacentHTML('beforeend', selectedProductsNodes);

			_.delay(() => trigger({ event: 'bigcommerce/shortcode_product_list_event', native: false }), 100);
		});
};

/**
 * @function handleSavedUIProductList
 * @description When a user opens or reopens the UI, reset the UI and check for a saved state.
 * @param e
 */
const handleSavedUIProductList = (e) => {
	resetProductsList();
	populateSavedUIProductList(e);
};

const cacheElements = () => {
	el.resultsContainer = tools.getNodes('bc-shortcode-ui-query-results')[0];
	el.selectedContainer = tools.getNodes('bc-shortcode-ui-selected-products')[0];
	el.productList = tools.getNodes('bc-shortcode-ui-product-list', false, el.selectedContainer)[0];
};

const bindEvents = () => {
	delegate(el.resultsContainer, '[data-js="add-remove-product"]', 'click', addRemoveProduct);
	delegate(el.selectedContainer, '[data-js="remove-product"]', 'click', removeProduct);
	on(document, 'bigcommerce/set_shortcode_ui_state', handleSavedUIProductList);
};

const init = () => {
	cacheElements();
	bindEvents();
};

export default init;
