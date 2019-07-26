/**
 * @module Share Wish List
 *
 * @description scripts used to share wishlists.
 */

import delegate from 'delegate';
import _ from 'lodash';
import * as tools from 'utils/tools';
import { copyToolTip } from '../templates/wish-lists';

const el = {
	container: tools.getNodes('.bc-manage-wish-list-header', false, document, true)[0],
};

/**
 * @function animateToolTip
 * @description Animate and then remove the tooltip for the copied wishlist URL.
 * @param tooltip
 */
const animateToolTip = (tooltip) => {
	if (!tooltip) {
		return;
	}

	el.copyButton.setAttribute('disabled', 'disabled');
	_.delay(() => tools.addClass(tooltip, 'active'), 150);
	_.delay(() => tools.removeClass(tooltip, 'active'), 2000);
	_.delay(() => {
		tooltip.parentNode.removeChild(tooltip);
		el.copyButton.removeAttribute('disabled');
	}, 2150);
};

/**
 * @function handleShareCopy
 * @description Handle the click event for the copy button and copy the URL to the clipboard.
 * @param e
 */
const handleShareCopy = (e) => {
	if (!e) {
		return;
	}

	const shareWrapper = tools.closest(e.delegateTarget, '[data-js="bc-manage-wish-list-share"]');
	const shareField = tools.getNodes('#bc-wish-list-share', false, shareWrapper, true)[0];
	const tooltip = document.createElement('div');

	tools.addClass(tooltip, 'bc-copied-wish-list-wrapper');
	tooltip.innerHTML = copyToolTip;
	shareField.select();
	document.execCommand('copy');

	if (tools.hasClass(tooltip, 'active')) {
		return;
	}

	shareWrapper.appendChild(tooltip);
	animateToolTip(tooltip);
};

const cacheElements = () => {
	el.copyButton = tools.getNodes('bc-copy-wishlist-url', false, el.container)[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-copy-wishlist-url"]', 'click', handleShareCopy);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
