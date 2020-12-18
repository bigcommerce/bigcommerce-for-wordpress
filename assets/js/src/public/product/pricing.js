/**
 * @module Pricing API
 * @description Scripts for handling calls and responses to and from the BC Pricing API
 */

import _ from 'lodash';
import delegate from 'delegate';
import * as tools from 'utils/tools';
import { on } from 'utils/events';
import { wpAPIProductPricing } from 'utils/ajax';
import { PRICING_API_URL, PRICING_API_NONCE } from '../config/wp-settings';

const el = {
	container: tools.getNodes('bc-api-product-pricing', true),
	priceWrapper: tools.getNodes('bc-product-pricing', true),
};

const state = {
	isFetching: false,
	optionTrigger: '',
	isQuickView: false,
	delay: 250,
	req: null,
	products: {
		items: [],
	},
};

/**
 * @function maybePriceIsLoading
 * @description based on state.isFetching, show of hide the spinner element.
 * @param pricingContainer
 */
const maybePriceIsLoading = (pricingContainer = '') => {
	if (state.isFetching) {
		tools.addClass(pricingContainer, 'bc-price-is-loading');
		return;
	}

	tools.removeClass(pricingContainer, 'bc-price-is-loading');
};

/**
 * @function getSelectedOptions
 * @description On load or on selection change, build an options array to submit to the pricing API.
 * @param optionsContainer
 */
const getSelectedOptions = (optionsContainer) => {
	let selection = '';
	const options = [];
	tools.getNodes('product-form-option', true, optionsContainer).forEach((field) => {
		const fieldType = field.dataset.field;

		switch (fieldType) {
		case 'product-form-option-radio':
		case 'product-form-option-checkbox':
			selection = tools.getNodes('input:checked', false, field, true)[0];
			break;
		case 'product-form-option-select':
			selection = tools.getNodes('select', false, field, true)[0];
			break;
		default:
			selection =	'';
		}

		if (!selection) {
			return;
		}

		const option = {
			option_id: parseInt(selection.dataset.optionId, 10),
			value_id: parseInt(selection.value, 10),
		};

		options.push(option);
	});

	return options;
};

/**
 * @function getInitialPricing
 * @description There are no options for this item and it is OK to just submit the product ID.
 * @param productID
 */
const getInitialPricing = (productID) => {
	const item = { product_id: productID };
	state.products.items.push(item);
};

/**
 * @function getOptionsPricing
 * @description If the product has visible options on the page, get those and add them to this item's options array.
 * @param e
 * @param productID
 * @param priceContainer
 * @param productOptions
 */
const getOptionsPricing = (e, productID, priceContainer, productOptions) => {
	const item = {
		product_id: productID,
		options: getSelectedOptions(productOptions),
	};

	state.products.items.push(item);
};

/**
 * @function buildPricingObject
 * @description Determine what type of data we're submitting to the API and build the state.products.items array.
 * @param priceContainer
 */
const buildPricingObject = (priceContainer) => {
	const bcid = parseInt(priceContainer.dataset.pricingApiProductId, 10);
	const dataWrapper = tools.closest(priceContainer, '[data-js="bc-product-data-wrapper"]');
	// CASE: This is not a card and we should get all possible selected options to submit.
	if (dataWrapper) {
		const optionsContainer = tools.getNodes('product-options', false, dataWrapper)[0];
		getOptionsPricing(null, bcid, priceContainer, optionsContainer);
	} else { // CASE: This is a card or single product with no options and it is safe to just get the basic pricing.
		getInitialPricing(bcid, priceContainer);
	}
};

/**
 * @function showCachedPricing
 * @description Show the cached pricing nodes if the API is down, errors out, or the API pricing nodes are overwritten
 *     or missing.
 * @param products
 */
const showCachedPricing = (products = []) => {
	// The API is down or responded with an error but and we still have API Pricing Nodes on the page.
	if (products.length) {
		products.items.forEach((product) => {
			// Only update the requested products to show the cached pricing data.
			const priceWrapper = tools.getNodes(`[data-product-price-id="${product.product_id}"]`, false, document, true)[0];
			const cachedPricingNode = tools.getNodes('bc-cached-product-pricing', true, priceWrapper)[0];
			tools.addClass(cachedPricingNode, 'bc-product__pricing--visible');
			maybePriceIsLoading(priceWrapper);
		});
		return;
	}

	// CASE: There are no Pricing API nodes to update, just show all the cached pricing ASAP.
	const cachedPricingNodes = tools.getNodes('bc-cached-product-pricing', true, document);
	if (!cachedPricingNodes || !cachedPricingNodes.length) {
		return;
	}

	cachedPricingNodes.forEach(element => tools.addClass(element, 'bc-product__pricing--visible'));
};

/**
 * @function filterAPIPricingData
 * @description Depending on the display_type of the item data, update the applicable price nodes with the new pricing
 *     data.
 * @param type
 * @param APIPricingNode
 * @param data
 */
const filterAPIPricingData = (type = '', APIPricingNode = '', data = {}) => {
	const pricingNodes = [];
	const pricingContainer = tools.closest(APIPricingNode, '[data-js="bc-product-pricing"]');

	if (!pricingContainer) {
		return;
	}

	// This will hide the spinner because state.isFetching is false.
	maybePriceIsLoading(pricingContainer);

	// If the cached pricing is visible, hide it now that we have data.
	const cachedPricingNode = tools.getNodes('bc-cached-product-pricing', false, pricingContainer)[0];
	if (cachedPricingNode) {
		tools.removeClass(cachedPricingNode, 'bc-product__pricing--visible');
	}

	// Create an array of potential nodes to update relative to the container they belong to.
	tools.addClass(APIPricingNode, 'bc-product__pricing--visible');
	pricingNodes['price-node'] = APIPricingNode.querySelector('.bc-product__price--base');
	pricingNodes['sale-node'] = APIPricingNode.querySelector('.bc-product__price--sale');
	pricingNodes['original-price-node'] = APIPricingNode.querySelector('.bc-product__original-price');
	pricingNodes['retail-price-node'] = APIPricingNode.querySelector('.bc-product__retail-price');
	pricingNodes['retail-value-node'] = APIPricingNode.querySelector('.bc-product__retail-price-value');
	pricingNodes.forEach(node => tools.removeClass(node, 'bc-show-current-price'));

	if (data.retail_price.formatted.length === 0) {
		tools.addClass(pricingNodes['retail-price-node'], 'bc-no-retail-price');
	} else {
		pricingNodes['retail-value-node'].textContent = data.retail_price.formatted;
	}

	// CASE: The display_type is 'sale'.
	if (type === 'sale') {
		pricingNodes['original-price-node'].textContent = data.original_price.formatted;
		pricingNodes['sale-node'].textContent = data.calculated_price.formatted;
		pricingNodes['price-node'].textContent = '';
		tools.addClass(pricingNodes['original-price-node'], 'bc-show-current-price');
		tools.addClass(pricingNodes['sale-node'], 'bc-show-current-price');
		return;
	}

	// CASE: The display_type is either 'price_range' or 'simple' and we can update the same node with  either data.
	const basePrice = type === 'price_range' ? `${data.price_range.min.formatted} - ${data.price_range.max.formatted}` : data.calculated_price.formatted;
	pricingNodes['original-price-node'].textContent = '';
	pricingNodes['sale-node'].textContent = '';
	pricingNodes['price-node'].textContent = basePrice;
	tools.addClass(pricingNodes['price-node'], 'bc-show-current-price');
};

/**
 * @function handleAPIItemData
 * @description For each item in the array, determine if the response belongs to a single product or a set of products.
 * @param type
 * @param productID
 * @param data
 */
const handleAPIItemData = (type = '', productID = '', data = {}) => {
	let parentNode = document;
	// CASE: This call was triggered by an option field change and belongs to a single product. Only update this instance.
	if (state.optionTrigger || state.isQuickView) {
		if (state.optionTrigger) {
			parentNode = tools.closest(state.optionTrigger, '[data-js="bc-product-data-wrapper"]');
		} else if (state.isQuickView) {
			const wrapper = tools.getNodes('.bc-product-quick-view__wrapper[aria-hidden=false]', false, document, true)[0];
			parentNode = tools.getNodes('bc-product-data-wrapper', false, wrapper)[0];
		}
		const priceNode = tools.getNodes(`[data-pricing-api-product-id="${productID}"]`, false, parentNode, true)[0];
		filterAPIPricingData(type, priceNode, data);
		// When the fields are re-enabled, it is safe to reset this value to blank to prepare it for the next call.
		// Prevents memory leak.
		state.optionTrigger = ''; //reset
		state.isQuickView = false;
		return;
	}

	// CASE: This data belongs to a group of items on the page and in case there are duplicates of this product, update all of them.
	tools.getNodes(`[data-pricing-api-product-id="${productID}"]`, true, parentNode, true).forEach((APIPricingNode) => {
		filterAPIPricingData(type, APIPricingNode, data);
	});
};

/**
 * @function handleAPIPricingData
 * @description Using the response from the API payload, parse through the items array and handle the display_type.
 * @param data
 */
const handleAPIPricingData = (data = {}) => {
	const APIPricingNodes = tools.getNodes('bc-api-product-pricing', true, document);
	if (!APIPricingNodes || !APIPricingNodes.length) {
		return;
	}

	Object.values(data.items).forEach((item) => {
		const productID = parseInt(item.product_id, 10);

		handleAPIItemData(item.display_type, productID, item);
	});
};

/**
 * @function submitAPIRequest
 * @description Submit the items saved in the state object to the API endpoint for current pricing data.
 */
const submitAPIRequest = (instance = {}) => {
	if (state.products.items.length < 1) {
		return;
	}

	if (instance.req) {
		instance.req.abort();
		instance.req = null;
	}

	state.isFetching = true;

	instance.req = wpAPIProductPricing(PRICING_API_URL, PRICING_API_NONCE, JSON.stringify(state.products))
		.end((err, res) => {
			state.isFetching = false;
			state.req = null;

			if (err) {
				console.error(err);
				showCachedPricing(state.products);
				return;
			}

			_.delay(() => handleAPIPricingData(res.body), state.delay);
		});
};

/**
 * @function handleOptionChanges
 * @description When an option change is triggered, create a new API request.
 * @param e
 */
const handleOptionChanges = (e) => {
	// We have to use e.target here due to lodash debounce losing the delegateTarget property.
	const wrapper = tools.closest(e.target, '[data-js="bc-product-data-wrapper"]');
	const priceWrapper = tools.getNodes('bc-product-pricing', false, wrapper)[0];
	const priceContainer = tools.getNodes('bc-api-product-pricing', false, wrapper)[0];
	// We're resetting the array here because option changes should only trigger a single item call to the API.
	state.products.items = [];
	state.isFetching = true;
	state.optionTrigger = e.target;
	buildPricingObject(priceContainer);
	maybePriceIsLoading(priceWrapper);
	submitAPIRequest(state);
};

/**
 * @function initOptionClicks
 * @description Click/change event listener for form fields on each product. Runs the handleOptionChanges function on
 *     the event.
 * @param pricingContainer - the current .initialized pricing node.
 */
const initOptionClicks = (pricingContainer = '') => {
	const wrapper = tools.closest(pricingContainer, '[data-js="bc-product-data-wrapper"]');
	if (!wrapper) {
		return;
	}

	const options = tools.getNodes('.bc-product-form__options.initialized', false, wrapper, true)[0];
	const radios = tools.getNodes('[data-js="product-form-option"] input[type=radio]', true, options, true);
	const selects = tools.getNodes('[data-js="product-form-option"] select', true, options, true);
	const checkboxes = tools.getNodes('[data-js="product-form-option"] input[type=checkbox]', true, options, true);

	if (radios.length > 0) {
		delegate(options, '[data-js="product-form-option"] input[type=radio]', 'click', handleOptionChanges);
	}

	if (selects.length > 0) {
		delegate(options, '[data-js="product-form-option"] select', 'change', handleOptionChanges);
	}

	if (checkboxes.length > 0) {
		delegate(options, '[data-js="product-form-option"] input[type=checkbox]', 'click', handleOptionChanges);
	}
};

/**
 * @function isPreinitialized
 * @description determines if a pricing node is eligible for preinitialization
 * @param pricingContainer
 */
const isPreinitialized = (pricingContainer) => {
	if (!tools.hasClass(pricingContainer, 'preinitialized')) {
		return false; // not preinitialized
	}

	const dataWrapper = tools.closest(pricingContainer, '[data-js="bc-product-data-wrapper"]');
	if (!dataWrapper) {
		return true; // no product options that might affect preinitialized pricing
	}

	const optionsContainer = tools.getNodes('product-options', false, dataWrapper)[0];
	const options = getSelectedOptions(optionsContainer);

	return options.length < 1; // no options selected = OK to use preinitialized value
};

/**
 * @function initPricing
 * @description prepare all dynamic pricing elements for receiving Pricing API data.
 * @param e
 */
const initPricing = (e) => {
	state.products.items = []; // Reset the items array to be submitted to the API endpoint.
	// Get all nodes that are not initialized and prepare them for the type of data they need to receive.
	state.isFetching = true;

	tools.getNodes('[data-js="bc-product-pricing"]:not(.initialized)', true, document, true).forEach((pricingContainer) => {
		const pricingAPINode = tools.getNodes('bc-api-product-pricing', false, pricingContainer)[0];

		tools.addClass(pricingContainer, 'initialized');
		// If this node is not preinitialized, it is safe to push to the state.items array and apply the price loading class.
		if (!isPreinitialized(pricingContainer)) {
			buildPricingObject(pricingAPINode);
			maybePriceIsLoading(pricingContainer);
		}
		initOptionClicks(pricingContainer);
	});

	state.isQuickView = e ? e.detail.quickView : false;

	// After looping through all the available nodes, run an API request.
	submitAPIRequest();
};

const bindEvents = () => {
	on(document, 'bigcommerce/get_pricing', initPricing);
};

const init = () => {
	if (!el.priceWrapper || !el.priceWrapper.length) {
		// There are no pricing wrappers on the page at all. i.e Cart page, checkout page, etc.
		return;
	}

	if (!el.container || !el.container.length) {
		// If there are no API Pricing nodes present, show cached pricing.
		showCachedPricing();
		return;
	}

	// Setup and submit a Pricing API request on page load.
	initPricing();
	bindEvents();
};

export default init;
