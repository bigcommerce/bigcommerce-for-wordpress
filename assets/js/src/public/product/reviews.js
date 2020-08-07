/**
 * @module Product Reviews
 * @description Product reviews scripts.
 */

import delegate from 'delegate';
import * as tools from 'utils/tools';
import scrollTo from 'utils/dom/scroll-to';
import _ from 'lodash';
import shortcodeState from 'adminConfig/shortcode-state';
import request from 'superagent';
import { NLS } from 'publicConfig/i18n';
import { paginationError } from '../templates/errors';

const el = {
	container: tools.getNodes('#bc-single-product__reviews', false, document, true)[0],
	firstPageURL: tools.getNodes('bc-reviews-ajax-url')[0],
};

const state = {
	formActive: false,
};

const options = {
	delay: 150,
	afterLoadDelay: 250,
};

const scrollToOptions = {
	duration: 250,
	easing: 'linear',
	offset: 0,
	$target: '',
};

/**
 * @function scrollToReviews
 * @description animate the page scroll position to the reviews section.
 */
const scrollToReviews = (e) => {
	if (e) {
		e.preventDefault();
	}

	scrollToOptions.$target = jQuery('.bc-single-product__reviews');
	scrollToOptions.offset = -30;
	scrollTo(scrollToOptions);
};

/**
 * @function scrollToReviewForm
 * @description animate the page scroll position to the review form.
 */
const scrollToReviewForm = () => {
	scrollToOptions.$target = jQuery('.bc-product-review-form');
	scrollToOptions.offset = 40;
	scrollTo(scrollToOptions);
};

/**
 * @function enableProductReviewForm
 * @description show the review form.
 * @param e
 */
const enableProductReviewForm = (e) => {
	const target = e.delegateTarget;
	const formWrapper = tools.closest(target, '.bc-product-review-form-wrapper');

	state.formActive = true;
	tools.addClass(formWrapper, 'bc-product-review-form--active');
	scrollToReviewForm();
};

/**
 * @function disableProductReviewForm
 * @description hide the product review form.
 * @param e
 */
const disableProductReviewForm = (e) => {
	const target = e.delegateTarget;
	const formWrapper = tools.closest(target, '.bc-product-review-form-wrapper');

	state.formActive = false;
	tools.removeClass(formWrapper, 'bc-product-review-form--active');

	scrollToReviews();
};

/**
 * @function handleFormAlert
 * @description on page load, if we have an alert from the form submission, determine its type and scroll to the message.
 */
const handleFormAlert = () => {
	const alert = tools.getNodes('.bc-alert-group', false, el.container, true)[0];

	if (!alert) {
		return;
	}

	if (tools.hasClass(alert, 'bc-alert-group--error')) {
		tools.addClass(el.formWrapper, 'bc-product-review-form--active');
		scrollToReviewForm();
		return;
	}

	scrollToReviews();
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
	const loadMoreWrapper = tools.closest(target, '.bc-product-review-list');

	target.removeAttribute('disabled');
	loadMoreWrapper.insertAdjacentHTML('beforeend', paginationError(message));
	initializeItems(loadMoreWrapper);
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
		initializeItems(itemContainer);
	}, options.delay);
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
 * @function getNextPageItems
 * @description Ajax query to get the next set of items in a paged shortcode container.
 */
const getFirstPageItems = () => {
	const firstRequestURL = el.firstPageURL.value;

	if (_.isEmpty(firstRequestURL)) {
		return;
	}

	shortcodeState.isFetching = true;
	handleSpinnerState(el.firstPageURL);

	request
		.get(firstRequestURL)
		.timeout({
			response: 5000, // 5 seconds to hear back from the server.
			deadline: 30000, // 30 seconds to finish the request process.
		})
		.end((err, res) => {
			shortcodeState.isFetching = false;
			handleSpinnerState(el.firstPageURL);

			if (err) {
				handleRequestError(err, el.firstPageURL);
				return;
			}

			handleItemsLoading(el.firstPageURL, res.body);
		});
};

const cacheElements  = () => {
	el.productSingle = tools.getNodes('.bc-product-single', false, document, true)[0];
	el.formWrapper = tools.getNodes('bc-product-review-form-wrapper')[0];
};

const bindEvents = () => {
	handleFormAlert();

	if (el.productSingle) {
		delegate(el.productSingle, '[data-js="bc-single-product-reviews-anchor"]', 'click', scrollToReviews);
	}

	if (el.formWrapper) {
		delegate(el.formWrapper, '[data-js="bc-product-review-write"]', 'click', enableProductReviewForm);
		delegate(el.formWrapper, '[data-js="bc-product-review-cancel-write"]', 'click', disableProductReviewForm);
	}

	if (el.firstPageURL) {
		getFirstPageItems();
	}
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
