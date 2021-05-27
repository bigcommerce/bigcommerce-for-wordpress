/**
 * @module Banners
 * @description Adds banners to your site.
 */

import * as GLOBALS from 'publicConfig/wp-settings';
import { bannerWrapper, bannerContent } from '../templates/banners';

const banners = {
	top: '',
	bottom: '',
};

/**
 * Determines if the given banner should be displayed.
 *
 * @param {*} banner BigCommerce Banner object.
 * @return {boolean} True if banner should be displayed false otherwise.
 */
const showBanner = (banner) => {
	const now = Math.round(new Date().getTime() / 1000);

	// check if banner is visible and not expired
	if (!banner.visible || (banner.date_type === 'custom' && now >= banner.date_to)) {
		return false;
	}

	return true;
};

const init = () => {
	if (!GLOBALS.BANNERS) {
		return;
	}

	// Loop through banners array
	GLOBALS.BANNERS.items.forEach((banner) => {
		if (!showBanner(banner)) {
			return;
		}

		// append banner to location
		banners[banner.location] += bannerContent(banner.content);
	});

	const styles = `background-color: ${GLOBALS.BANNERS.bg_color}; color: ${GLOBALS.BANNERS.text_color};`;

	// insert "top" banners
	if (banners.top) {
		document.body.insertAdjacentHTML('afterbegin', bannerWrapper(styles, banners.top));
	}

	// insert "bottom" banners
	if (banners.bottom) {
		document.body.insertAdjacentHTML('beforeend', bannerWrapper(styles, banners.bottom));
	}
};

export default init;
