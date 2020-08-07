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

const gaCrossDomainInit = async () => {
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
