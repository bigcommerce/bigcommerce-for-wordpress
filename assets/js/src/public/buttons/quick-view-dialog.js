/**
 * @module Quick View Dialog Modal
 * @description Create a dialog modal for products in grids that have options.
 */

import A11yDialog from 'mt-a11y-dialog';
import _ from 'lodash';
import * as tools from '../../utils/tools';
import gallery from '../gallery/productGallery';
import variants from '../product/variants';

const container = tools.getNodes('bc-product-loop-card');
const instances = {
	dialogs: {},
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
		const trigger = tools.getChildren(dialog)[0];
		const target = tools.getChildren(dialog)[1];

		dialog.classList.add('initialized');
		trigger.setAttribute('data-content', dialogID);
		trigger.setAttribute('data-trigger', dialogID);
		target.setAttribute('data-js', dialogID);
		instances.dialogs[dialogID] = new A11yDialog(getOptions(dialogID));

		instances.dialogs[dialogID].on('render', () => {
			_.delay(gallery, 50);
			_.delay(variants, 150);
		});
	});
};

const init = () => {
	if (!container) {
		return;
	}

	initDialogs();
};

export default init;
