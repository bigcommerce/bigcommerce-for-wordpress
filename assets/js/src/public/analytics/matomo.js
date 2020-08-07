/**
 * @module Matomo Analytics Tracking
 * @description Handle Matomo analytics tracking events.
 */

import delegate from 'delegate';
import * as tools from 'utils/tools';
import { on } from 'utils/events';

const matomoConfig = {
	matomoAPI: typeof _paq !== 'undefined', // Check for the global _paq function and confirm it's an object.
};

const setChannelVisitTracking = () => {
	if (!window.bigcommerce_config) {
		return;
	}

	_paq.push(['setCustomVariable',
		window.bigcommerce_config.matomo.custom_variables.var_1.id,   // Index, the number from 1 to 5 where this custom variable name is stored
		window.bigcommerce_config.matomo.custom_variables.var_1.name, // Name, the name of the variable, for example: BC_Channel
		`${window.bigcommerce_config.channel.id} - ${window.bigcommerce_config.channel.name}`,  // Value, for example: "Channel Name", "Channel ID", "3324567"
		'visit',      // Scope of the custom variable, "visit" means the custom variable applies to the current visit
	]);
};

const handleAddToCartTracker = (e) => {
	const cartTrigger = e ? e.detail.cartButton : tools.getNodes('[data-tracking-event="add_to_cart_message"]', false, document, true)[0];

	if (!cartTrigger) {
		return;
	}

	const analyticsData = cartTrigger.dataset.trackingData;
	const jsonData = JSON.parse(analyticsData);

	setChannelVisitTracking();

	// add the first product to the order
	_paq.push(['addEcommerceItem',
		jsonData.product_id, // (required) SKU: Product unique identifier
		jsonData.name, // (optional) Product name
	]);

	console.info(`Matomo has recorded the following cart tracking data: ${analyticsData}`);
};

const handleProductView = (e) => {
	const target = e.delegateTarget;
	const analyticsData = target.dataset.trackingData;
	if (!analyticsData) {
		return;
	}

	const jsonData = JSON.parse(analyticsData);

	setChannelVisitTracking();

	_paq.push(['setEcommerceView',
		jsonData.product_id, // (required) SKU: Product unique identifier
		jsonData.name, // (optional) Product name
	]);

	// Calling trackPageView is required when tracking a product view
	_paq.push(['trackPageView']);

	console.info(`Matomo has recorded the following tracking data: ${analyticsData}`);
};

const bindEvents = () => {
	tools.getNodes('bc-product-loop-card', true, document).forEach((product) => {
		delegate(product, '[data-js="bc-product-quick-view-dialog-trigger"]', 'click', handleProductView);
		delegate(product, '.bc-product__title-link', 'click', handleProductView);
	});

	tools.getNodes('bc-product-quick-view-content', true, document).forEach((dialog) => {
		delegate(dialog, '.bc-product__title-link', 'click', handleProductView);
	});

	on(document, 'bigcommerce/analytics_trigger', handleAddToCartTracker);
};

const init = () => {
	if (!matomoConfig.matomoAPI) {
		return;
	}

	bindEvents();
	handleAddToCartTracker();
};

export default init;
