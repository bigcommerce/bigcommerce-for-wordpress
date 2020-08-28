/**
 * @module Wish List List Module
 *
 * @description scripts used to share wishlists in wishlist lists
 */

import delegate from 'delegate';
import { NLS } from 'publicConfig/i18n';
import * as tools from 'utils/tools';

const el = {
	container: tools.getNodes('.bc-wish-list-body', false, document, true)[0],
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

	const button = e.delegateTarget;
	const shareWrapper = tools.closest(e.delegateTarget, '[data-js="bc-wish-list-actions"]');
	const shareField = tools.getNodes('.bc-wishlist-link-input', false, shareWrapper, true)[0];
	const tooltip = document.createElement('div');
	shareField.type = 'text';

	tools.addClass(tooltip, 'bc-copied-wish-list-wrapper');
	shareField.select();
	document.execCommand('copy');
	shareField.type = 'hidden';

	button.innerHTML = NLS.wish_lists.copied;
	button.disabled = true;

	setTimeout(() => {
		button.innerHTML = NLS.wish_lists.copy_link;
		button.disabled = false;
	}, 1000);
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-copy-wishlist-url"]', 'click', handleShareCopy);
};

const init = () => {
	if (!el.container) {
		return;
	}

	bindEvents();
};

export default init;
