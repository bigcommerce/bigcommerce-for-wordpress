/**
 * @module Segment Analytics Tracking
 * @description Allow Facebook Pixel and Google Analytics tracking on specified Segment events.
 */

import delegate from 'delegate';
import { STORE_DOMAIN } from 'publicConfig/wp-settings';
import * as tools from 'utils/tools';
import { on } from 'utils/events';

const el = {
	segment: tools.getNodes('bc-segment-tracker')[0],
};

/**
 * @function handleAddToCartTracker
 * @description Event handler for tracking products added to the cart.
 * @param e
 */
const handleAddToCartTracker = (e) => {
	const cartTrigger = e ? e.detail.cartButton : tools.getNodes('[data-tracking-event="add_to_cart_message"]', false, document, true)[0];

	if (!cartTrigger) {
		return;
	}

	const analyticsData = cartTrigger.dataset.trackingData;
	const jsonData = JSON.parse(analyticsData);

	analytics.track('Product Added', {
		cart_id: e ? e.detail.cart_id : jsonData.cart_id,
		product_id: jsonData.product_id,
		variant: jsonData.variant_id,
	});
	console.info(`Segment has sent the following cart tracking data to your analytics account(s): ${analyticsData}`);
};

/**
 * @function handleClickTracker
 * @description Event handler for clicking on products to view PDP or Quick View.
 * @param e
 */
const handleClickTracker = (e) => {
	const target = e.delegateTarget;
	const analyticsData = target.dataset.trackingData;
	if (!analyticsData) {
		return;
	}

	const jsonData = JSON.parse(analyticsData);

	analytics.track('Product Viewed', {
		product_id: jsonData.product_id,
		name: jsonData.name,
	});
	console.info(`Segment has sent the following tracking data to your analytics account(s): ${analyticsData}`);
};

/**
 * @function handleOrderCompleteTracker
 * @description Event handler for embedded checkout order completion.
 * @param e
 * TODO: This needs to be overhauled once BC can provide proper order data in the ECO response.
 */
const handleOrderCompleteTracker = (e) => {
	if (!e.detail) {
		return;
	}

	const cartID = e.detail.cart_id;
	analytics.track('BigCommerce Order Completed', {
		cart_id: cartID,
	});

	console.info(`Segment has sent the following tracking data to your analytics account(s): Order Completed. Cart ID: ${cartID}`);
};

/**
 * @function gaCrossDomainInit
 * @description Enable GA x-domain tracking by default.
 * @return {Promise<void>}
 */
const gaCrossDomainInit = async () => {
	// Check for the global ga function and confirm it's an object.
	if (typeof ga === 'undefined') {
		return;
	}

	await analytics.ready(() => {
		ga('require', 'linker');
		ga('linker:autoLink', [STORE_DOMAIN]);
	});
};

const bindEvents = () => {
	tools.getNodes('bc-product-loop-card', true, document).forEach((product) => {
		delegate(product, '[data-js="bc-product-quick-view-dialog-trigger"]', 'click', handleClickTracker);
		delegate(product, '.bc-product__title-link', 'click', handleClickTracker);
	});

	tools.getNodes('bc-product-quick-view-content', true, document).forEach((dialog) => {
		delegate(dialog, '.bc-product__title-link', 'click', handleClickTracker);
	});

	on(document, 'bigcommerce/analytics_trigger', handleAddToCartTracker);
	on(document, 'bigcommerce/order_complete', handleOrderCompleteTracker);
};

const init = () => {
	if (!el.segment) {
		return;
	}

	gaCrossDomainInit();
	bindEvents();
	handleAddToCartTracker();
};

export default init;
