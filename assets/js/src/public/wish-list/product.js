/**
 * @module Product Detail Page Wish List
 *
 * @description Scripts used for adding an item to a Wish List on the PDP.
 */

import delegate from 'delegate';
import * as tools from 'utils/tools';

const el = {
	container: tools.getNodes('bc-pdp-add-to-wish-list')[0],
};

const hideWishLists = (wrapper = '', button = '', list = '') => {
	tools.removeClass(wrapper, 'bc-show-lists');
	tools.removeClass(button, 'bc-show-lists');
	tools.removeClass(list, 'bc-show-lists');
	button.setAttribute('aria-expanded', false);
};

const showWishLists = (wrapper = '', button = '', list = '') => {
	tools.addClass(wrapper, 'bc-show-lists');
	tools.addClass(button, 'bc-show-lists');
	tools.addClass(list, 'bc-show-lists');
	button.setAttribute('aria-expanded', true);
};

const handleClickOutsideList = (e) => {
	const openLists = tools.getNodes('[data-js="bc-pdp-add-to-wish-list"].bc-show-lists', true, e.currentTarget, true);

	if (!openLists) {
		return;
	}

	openLists.forEach((wrapper) => {
		if (wrapper.contains(e.target)) {
			return;
		}

		const button = tools.getNodes('bc-pdp-wish-list-toggle', false, wrapper)[0];
		const list = tools.getNodes('bc-pdp-wish-lists', false, wrapper)[0];
		hideWishLists(wrapper, button, list);
	});
};

const toggleWishListsList = (e) => {
	const button = e.delegateTarget;
	const wrapper = tools.closest(button, '[data-js="bc-pdp-add-to-wish-list"]');
	const list = tools.getNodes('bc-pdp-wish-lists', false, wrapper)[0];

	if (tools.hasClass(button, 'bc-show-lists')) {
		hideWishLists(wrapper, button, list);
		return;
	}

	showWishLists(wrapper, button, list);
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-pdp-wish-list-toggle"]', 'click', toggleWishListsList);
	document.addEventListener('click', handleClickOutsideList);
};

const init = () => {
	if (!el.container) {
		return;
	}

	bindEvents();
};

export default init;
