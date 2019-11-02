/**
 * @module Quick View Dialog Modal
 * @description Create a dialog modal for products in grids that have options.
 */

import A11yDialog from 'mt-a11y-dialog';
import _ from 'lodash';
import { trigger } from 'utils/events';
import * as tools from '../../utils/tools';
import gallery from '../gallery/productGallery';
import videos from '../gallery/productVideos';
import variants from '../product/variants';

const container = tools.getNodes('bc-product-loop-card');

const instances = {
	dialogs: {},
};

const state = {
	delay: 150,
};

const getOptions = dialogID => ({
	appendTarget: 'body',
	trigger: `[data-trigger="${dialogID}"]`,
	bodyLock: true,
	effect: 'fade',
	effectSpeed: 200,
	effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
	overlayClasses: 'bc-product-quick-view__overlay',
	contentClasses: 'bc-product-quick-view__content',
	wrapperClasses: 'bc-product-quick-view__wrapper',
	closeButtonClasses: 'bc-product-quick-view__close-button bc-icon icon-bc-cross',
});

const initDialogs = () => {
	tools.getNodes('[data-js="bc-product-loop-card"]:not(.initialized)', true, document, true).forEach((dialog) => {
		const dialogID = _.uniqueId('bc-product-quick-view-dialog-');
		const dialogTrigger = tools.getNodes('bc-product-quick-view-dialog-trigger', false, dialog)[0];
		const target = tools.getNodes('[data-quick-view-script]', false, dialog, true)[0];

		if (!dialogTrigger || !target) {
			return;
		}

		dialog.classList.add('initialized');
		dialogTrigger.setAttribute('data-content', dialogID);
		dialogTrigger.setAttribute('data-trigger', dialogID);
		target.setAttribute('data-js', dialogID);
		instances.dialogs[dialogID] = new A11yDialog(getOptions(dialogID));

		instances.dialogs[dialogID].on('render', () => {
			_.delay(() => gallery(), state.delay);
			_.delay(() => videos(), state.delay);
			_.delay(() => variants(dialog), state.delay);
			_.delay(() => trigger({ event: 'bigcommerce/get_pricing', data: { quickView: true }, native: false }), state.delay);
		});

		instances.dialogs[dialogID].on('hide', () => trigger({ event: 'bigcommerce/gallery_slide_changed', data: { quickView: instances.dialogs[dialogID] }, native: false }));
	});
};

const init = () => {
	if (!container) {
		return;
	}

	initDialogs();
};

export default init;
