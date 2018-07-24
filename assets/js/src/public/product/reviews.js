/**
 * @module Product Reviews
 * @description Product reviews scripts.
 */

import $ from 'jquery';
import delegate from 'delegate';
import * as tools from '../../utils/tools';
import scrollTo from '../../utils/dom/scroll-to';

const el = {
	container: tools.getNodes('.bc-product-single', false, document, true)[0],
};

const state = {
	formActive: false,
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
	e.preventDefault();
	scrollToOptions.$target = $('.bc-single-product__reviews');
	scrollToOptions.offset = -30;
	scrollTo(scrollToOptions);
};

/**
 * @function scrollToReviewForm
 * @description animate the page scroll position to the review form.
 */
const scrollToReviewForm = () => {
	scrollToOptions.$target = $('.bc-product-review-form');
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
 * @description on page load, if we have an alert from the form submission, determine its type and show the user the state.
 */
const handleFormAlert = () => {
	const alert = tools.getNodes('.bc-alert-group--error', false, el.productWrapper, true)[0];

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

const cacheElements  = () => {
	el.formWrapper = tools.getNodes('bc-product-review-form-wrapper')[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-single-product-reviews-anchor"]', 'click', scrollToReviews);
	handleFormAlert();

	if (el.formWrapper) {
		delegate(el.formWrapper, '[data-js="bc-product-review-write"]', 'click', enableProductReviewForm);
		delegate(el.formWrapper, '[data-js="bc-product-review-cancel-write"]', 'click', disableProductReviewForm);
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
