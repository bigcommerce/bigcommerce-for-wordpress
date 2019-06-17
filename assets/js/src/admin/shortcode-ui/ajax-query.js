/**
 * @module Ajax WP REST API Requests
 * @description Send requests and get responses to the BigCommerce WP REST API endpoint.
 */

import delegate from 'delegate';
import _ from 'lodash';
import { Spinner } from 'spin.js';
import shortcodeState from '../config/shortcode-state';
import * as tools from '../../utils/tools';
import { productTemplate } from './product-template';
import { I18N } from '../config/i18n';
import { wpAPIProductLookup, wpAPIPagedProductLookup } from '../../utils/ajax';
import { ADMIN_ICONS } from '../config/wp-settings';
import { on } from '../../utils/events';

const el = {};
const instances = {
	waypoints: [],
};

/**
 * @function createSpinner
 * @description create a new spinner element.
 * @returns {*}
 */
const createSpinLoader = () => {
	const options = {
		opacity: 0.5,
	};

	return new Spinner(options).spin(el.loader);
};

/**
 * @function handleExistingWaypoints
 * @description Destroy and remove existing waypoints. Clear out the waypoints array.
 */
const handleExistingWaypoints = () => {
	// If we have any waypoints cached, destroy them and clear the instances object.
	if (instances.waypoints.length > 0) {
		instances.waypoints.forEach((way) => {
			// Destroy waypoints before removing the node to avoid errors.
			way.destroy();
		});
		// Clear the waypoints array.
		instances.waypoints = [];
	}

	// If we have any waypoint DOM nodes, remove them.
	const currentWaypoints = tools.getNodes('bc-products-has-next', true, el.productGrid);
	if (currentWaypoints) {
		currentWaypoints.forEach((currentWaypoint) => {
			currentWaypoint.parentNode.removeChild(currentWaypoint);
		});
	}
};

/**
 * @function dimTheHouseLights
 * @description enable or disable the loader and dimmer elements depending on the current state of isFetching
 */
const dimTheHouseLights = () => {
	if (shortcodeState.isFetching) {
		el.dimmer.classList.add('active');
		el.loader.classList.add('active');

		return;
	}

	el.dimmer.classList.remove('active');
	el.loader.classList.remove('active');
};

/**
 * @function renderProductTemplate
 * @param product json product object
 */
const renderProductTemplate = (product = {}) => {
	const selected = shortcodeState.selectedProducts.bc_id.filter(bcid => parseInt(bcid, 10) === product.bigcommerce_id).length > 0;
	const productData = {
		id: product.post_id,
		bcid: product.bigcommerce_id,
		title: product.title,
		image: product.image.sizes['bc-small'].url ? product.image.sizes['bc-small'].url : `${ADMIN_ICONS}/placeholder.svg`,
		price: product.price_range,
		desc: product.content.trimmed,
		classes: product.image.sizes['bc-small'].url ? 'bc-shortcode-ui__product-image--featured' : 'bc-shortcode-ui__product-image--placeholder',
		selected: selected ? ' bc-shortcode-ui__selected-result' : '',
		button_text: selected ? I18N.buttons.remove_product : I18N.buttons.add_product,
	};

	shortcodeState.productHTML += productTemplate(productData);
};

/**
 * @function queryObjectToString
 * @description iterate over the wpAPIQueryObj object and create a query string.
 * @returns {string}
 */
const queryObjectToString = () => {
	const str = [];
	Object.entries(shortcodeState.wpAPIQueryObj).forEach(([key, value]) => {
		if (value.length <= 0) {
			return;
		}
		const k = encodeURIComponent(key);
		const v = encodeURIComponent(value);
		str.push(`${k}=${v}`);
	});

	return str ? str.join(I18N.operations.query_string_separator) : '';
};

/**
 * @function handleAjaxResponse
 * @description run the ajax response through a check and render function.
 * @param res
 */
const handleAjaxResponse = (res) => {
	if (!Array.isArray(res)) {
		return;
	}

	shortcodeState.isFetching = false;
	dimTheHouseLights();

	if (res.length <= 0) {
		const message = document.createElement('h2');
		tools.addClass(message, 'bc-shortcode-ui__no-results');
		message.textContent = I18N.messages.no_results;
		el.productGrid.appendChild(message);
		return;
	}

	res.forEach(product => renderProductTemplate(product));
	el.productGrid.insertAdjacentHTML('beforeend', shortcodeState.productHTML);
	shortcodeState.productHTML = '';
};

/**
 * @function handleAjaxPagination
 * @description Adds AJAX pagination on scroll using Waypoints JS.
 * @param nextPage
 */
const handleAjaxPagination = (nextPage = '') => {
	if (!nextPage) {
		return;
	}

	handleExistingWaypoints();

	const container = document.createElement('div');
	container.setAttribute('data-next', nextPage);
	container.setAttribute('data-js', 'bc-products-has-next');
	el.productGrid.appendChild(container);

	const way = new Waypoint({
		element: document.querySelector('[data-js="bc-products-has-next"]'),
		context: el.productGrid,
		offset: '100%',
		handler: () => {
			// 1. Disable the waypoint while we attempt an endpoint fetch.
			way.disable();
			shortcodeState.isFetching = true;
			dimTheHouseLights();
			// 2. Run the query to get the next page of products and populate the grid.
			wpAPIPagedProductLookup(nextPage)
				.end((err, res) => {
					shortcodeState.isFetching = false;
					dimTheHouseLights();

					if (err) {
						el.productGrid.innerHTML = I18N.messages.ajax_error;
						console.error(err);
						return;
					}

					// 3. Assumes a successful response. It is safe to destroy the waypoint(s).
					handleExistingWaypoints();
					// 4. Get and print the cards markup from the response.
					handleAjaxResponse(res.body);
					// 5. Recursively create the new waypoint if the next page link exists.
					_.delay(() => handleAjaxPagination(res.links.next), 100);
				});
		},
	});

	// Add this waypoint to the instances cache. Used to destroy and clear stale waypoints.
	instances.waypoints.push(way);
};

/**
 * @function wpAPIGetRequest
 * @description Send GET to the WP API endpoint and handle the response.
 */
const wpAPIGetRequest = () => {
	shortcodeState.isFetching = true;
	dimTheHouseLights();
	handleExistingWaypoints();

	el.productGrid.textContent = '';

	const queryString = queryObjectToString();

	wpAPIProductLookup(queryString)
		.end((err, res) => {
			shortcodeState.isFetching = false;
			dimTheHouseLights();

			if (err) {
				el.productGrid.innerHTML = I18N.messages.ajax_error;
				console.error(err);
				return;
			}

			handleAjaxResponse(res.body);
			_.delay(() => handleAjaxPagination(res.links.next), 150);
		});
};

const cacheElements = () => {
	el.dialog = tools.getNodes('bc-shortcode-ui-products', false, document, false)[0];
	el.searchForm = tools.getNodes('bc-shortcode-ui-search', false, el.dialog, false)[0];
	el.productGrid = tools.getNodes('bc-shortcode-ui-query-results', false, el.dialog, false)[0];
	el.dimmer = tools.getNodes('.bc-shortcode-ui__product-query-dimmer', false, el.dialog, true)[0];
	el.loader = tools.getNodes('.bc-shortcode-ui__product-query-loader', false, el.dialog, true)[0];
};

const bindEvents = () => {
	delegate(el.searchForm, '[data-js="bcqb-submit"]', 'click', wpAPIGetRequest);
	on(document, 'bigcommerce/shortcode_ui_state_ready', wpAPIGetRequest);
	on(document, 'bigcommerce/get_channel_products', wpAPIGetRequest);
	on(document, 'bigcommerce/hide_shortcode_ui', handleExistingWaypoints);
};

const init = () => {
	cacheElements();
	createSpinLoader();
	bindEvents();
};

export default init;
