/**
 * @module Pagination Button
 *
 * @description Load more posts for paginated shortcodes
 */

import _ from 'lodash';
import delegate from 'delegate';
import request from 'superagent';
import { Spinner } from 'spin.js/spin';
import { trigger } from 'utils/events';
import * as tools from 'utils/tools';
import shortcodeState from '../../admin/config/shortcode-state';
import quickViewDialog from './quick-view-dialog';
import { NLS } from '../config/i18n';
import { paginationError } from '../templates/errors';

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
	_.delay(() => {
		itemContainer.insertAdjacentHTML('beforeend', items.rendered);
	}, options.delay);

	_.delay(() => {
		if (tools.hasClass(itemContainer, 'bc-product-grid')) {
			quickViewDialog();
		}

		initializeItems(itemContainer);
		trigger({ event: 'bigcommerce/get_pricing', native: false });
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
 * @function handleSpinnerState
 * @description Show or hid the display of the spinner when fetching data.
 * @param target
 */
const handleSpinnerState = (target = '') => {
	const gridWrapper = tools.closest(target, '.bc-load-items');
	const loader = tools.getNodes('.bc-load-items__loader', false, gridWrapper, true)[0];

	if (shortcodeState.isFetching) {
		tools.addClass(loader, 'active');
		return;
	}

	tools.removeClass(loader, 'active');
};

/**
 * @function handleRequestError
 * @description if there is a pagination request error, display the message inline.
 * @param err
 * @param target
 */
const handleRequestError = (err = {}, target = '') => {
	if (!target && !err) {
		return;
	}

	const message = err.timeout ? NLS.errors.pagination_timeout_error : NLS.errors.pagination_error;
	const loadMoreWrapper = tools.closest(target, '.bc-load-items__trigger');
	const currentErrorMessage = tools.getNodes('.bc-pagination__error-message', false, loadMoreWrapper, true)[0];

	if (currentErrorMessage) {
		currentErrorMessage.parentNode.removeChild(currentErrorMessage);
	}

	target.removeAttribute('disabled');
	loadMoreWrapper.insertAdjacentHTML('beforeend', paginationError(message));
};

/**
 * @function getNextPageItems
 * @description Ajax query to get the next set of items in a paged shortcode container.
 * @param e
 */
const getNextPageItems = (e) => {
	e.preventDefault();
	e.delegateTarget.setAttribute('disabled', 'disabled');
	const button = e.delegateTarget;
	const itemsURL = e.delegateTarget.dataset.href;

	if (!itemsURL) {
		return;
	}

	shortcodeState.isFetching = true;
	handleSpinnerState(button);

	request
		.get(itemsURL)
		.timeout({
			response: 5000, // 5 seconds to hear back from the server.
			deadline: 30000, // 30 seconds to finish the request process.
		})
		.end((err, res) => {
			shortcodeState.isFetching = false;
			handleSpinnerState(button);

			if (err) {
				handleRequestError(err, button);
				return;
			}

			handleItemsLoading(button, res.body);
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
		delegate(document, '[data-js="load-items-trigger-btn"]', 'click', getNextPageItems);
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
