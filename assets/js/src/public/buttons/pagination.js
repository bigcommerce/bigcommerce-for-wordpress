/**
 * @module Pagination Button
 *
 * @description Load more posts for paginated shortcodes
 */

import _ from 'lodash';
import delegate from 'delegate';
import request from 'superagent';
import { Spinner } from 'spin.js/spin';
import * as tools from '../../utils/tools';
import shortcodeState from '../../admin/config/shortcode-state';
import quickViewDialog from './quick-view-dialog';

const el = {
	container: tools.getNodes('load-items-trigger', true, document, false),
};

const options = {
	delay: 150,
	afterLoadDelay: 250,
};

/**
 * @function createSpinner
 * @description create a new spinner element.
 * @returns {*}
 */
const createSpinLoader = (itemContainer = '') => {
	if (!itemContainer) {
		return;
	}

	const container = tools.closest(itemContainer, '.bc-load-items');
	const spinner = tools.getNodes('.bc-load-items__loader', false, container, true)[0];
	const spinnerOptions = {
		opacity: 0.5,
		scale: 0.5,
		lines: 12,
	};

	new Spinner(spinnerOptions).spin(spinner);
};

/**
 * @function initializeItems
 * @description add a class to signify that item has been rendered in the shortcode container.
 * @param itemContainer
 */
const initializeItems = (itemContainer = '') => {
	if (!itemContainer) {
		return;
	}

	tools.getChildren(itemContainer).forEach((item) => {
		if (!tools.hasClass(item, 'item-initialized') && !tools.hasClass(item, 'bc-load-items__trigger')) {
			tools.addClass(item, 'item-initialized');
		}
	});
};

/**
 * @function loadNextPageItems
 * @description Get and inject the rendered HTML from the WP API response to load the next page of items.
 * @param items
 * @param itemContainer
 */
const loadNextPageItems = (items = {}, itemContainer = '') => {
	const gridWrapper = tools.closest(itemContainer, '.bc-load-items');
	const loader = tools.getNodes('.bc-load-items__loader', false, gridWrapper, true)[0];

	tools.addClass(loader, 'active');

	_.delay(() => {
		tools.removeClass(loader, 'active');
		itemContainer.insertAdjacentHTML('beforeend', items.rendered);
	}, options.delay);

	_.delay(() => {
		if (tools.hasClass(itemContainer, 'bc-product-grid')) {
			quickViewDialog();
		}

		initializeItems(itemContainer);
	}, options.afterLoadDelay);
};

/**
 * @function removePagedButton
 * @description Remove the paged button that triggered the current successful API request.
 * @param target
 * @param container
 */
const removePagedButton = (target = '', container = '') => {
	if (!target && !container) {
		return;
	}

	const loadMoreWrapper = tools.closest(target, '.bc-load-items__trigger');
	container.removeChild(loadMoreWrapper);
};

/**
 * @function handleItemsLoading
 * @description Handler for gracefully loading the next set of paged items into the current shortcode container.
 * @param target
 * @param items
 */
const handleItemsLoading = (target = '', items = {}) => {
	if ((!target && !items) || shortcodeState.isFetching) {
		return;
	}

	const itemContainer = tools.closest(target, '.bc-load-items-container');

	removePagedButton(target, itemContainer);
	loadNextPageItems(items, itemContainer);
};

/**
 * @function getNextPageItems
 * @description Ajax query to get the next set of items in a paged shortcode container.
 * @param e
 */
const getNextPageItems = (e) => {
	e.preventDefault();
	e.delegateTarget.setAttribute('disabled', 'disabled');
	const itemsURL = e.delegateTarget.dataset.href;

	if (!itemsURL) {
		return;
	}

	shortcodeState.isFetching = true;

	request
		.get(itemsURL)
		.end((err, res) => {
			shortcodeState.isFetching = false;

			if (err) {
				console.error(err);
			}

			handleItemsLoading(e.delegateTarget, res.body);
		});
};

/**
 * @function cacheElements
 * @description Load some additional elements into the scope if el.container exists.
 */
const cacheElements = () => {
	el.itemContainer = tools.getNodes('.bc-load-items-container--has-pages', true, document, true);
};

/**
 * @function bindEvents
 * @description Handle events triggered by paged shortcode triggers.
 */
const bindEvents = () => {
	el.itemContainer.forEach((itemContainer) => {
		createSpinLoader(itemContainer);
		initializeItems(itemContainer);
		delegate(itemContainer, '[data-js="load-items-trigger-btn"]', 'click', getNextPageItems);
	});
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
