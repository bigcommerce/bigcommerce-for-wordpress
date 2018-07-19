/**
 * @module Analytics Tracking
 * @description Allow Facebook Pixel and Google Analytics tracking on specified events.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';

const el = {
	fbpixel: tools.getNodes('bc-facebook-pixel')[0],
	ga: tools.getNodes('bc-ga-tracker')[0],
};

const handleAddToCartTracker = () => {
	const cartTrigger = tools.getNodes('[data-tracking-event="add_to_cart"]', false, document, true)[0];

	if (!cartTrigger) {
		return;
	}

	const analyticsData = cartTrigger.dataset.trackingData;
	const jsonData = JSON.stringify(analyticsData);

	if (el.ga) {
		gtag('event', 'add_to_cart', {
			jsonData,
		});
		console.info(`An event occurred that sent tracking data to your Google Analytics account for a product added to the users cart: ${analyticsData}`);
	}

	if (el.fbpixel) {
		fbq('track', 'AddToCart', {
			jsonData,
		});

		console.info(`An event occurred that sent tracking data to your facebook pixel for a product added to the users cart: ${analyticsData}`);
	}
};

const handleClickTracker = (e) => {
	const target = e.delegateTarget;
	const analyticsData = target.dataset.trackingData;
	if (!analyticsData) {
		return;
	}

	const jsonData = JSON.stringify(analyticsData);

	if (el.ga) {
		gtag('event', 'view_item', {
			jsonData,
		});
		console.info(`An event occurred that sent tracking data to your Google Analytics account: ${analyticsData}`);
	}

	if (el.fbpixel) {
		fbq('track', 'ViewContent', {
			jsonData,
		});

		console.info(`An event occurred that sent tracking data to your facebook pixel: ${analyticsData}`);
	}
};

const bindEvents = () => {
	tools.getNodes('bc-product-loop-card', true, document).forEach((product) => {
		delegate(product, '[data-js="bc-product-quick-view-dialog-trigger"]', 'click', handleClickTracker);
		delegate(product, '.bc-product__title-link', 'click', handleClickTracker);
	});

	tools.getNodes('bc-product-quick-view-content', true, document).forEach((dialog) => {
		delegate(dialog, '.bc-product__title-link', 'click', handleClickTracker);
	});
};

const init = () => {
	if (!el.fbpixel && !el.ga) {
		return;
	}

	bindEvents();
	handleAddToCartTracker();
};

export default init;
