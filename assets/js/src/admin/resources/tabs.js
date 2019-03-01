/**
 * @module Resources Page Tabs
 * @description Tab functionality for the Resources page content.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { trigger, on } from '../../utils/events';

const el = {
	container: tools.getNodes('bc-resources-wrapper')[0],
};

/**
 * @function handleBGCardImage
 * @description A lazyload-esq function that allows us to determine which image from the srcset is being used, then
 *     display it as a BG image to control the dims.
 * @param e
 * @param container
 */
const handleBGCardImage = (e, container = '') => {
	const cardContainer = e ? e.detail.container : container;

	tools.getNodes('bc-resource-card', true, cardContainer).forEach((card) => {
		const image = card.querySelector('img');
		image.onload = () => tools.addClass(image.parentNode, 'bc-resource-card-image-loaded');
	});
};

const initTabClicks = (e) => {
	const tab = e ? e.delegateTarget : el.firstTab; // If we have a click event, use that to get the target. Otherwise, default to the first tab target.
	const tabTarget = tab.dataset.target;
	const targetContainer = tools.getNodes(`[data-tab-name="${tabTarget}"]`, false, el.container, true)[0];
	const tabListItem = tools.closest(tab, '[data-js="bc-resources-tab"]');

	// If we're already on this tab, do not run the process again.
	if (tools.hasClass(targetContainer, 'bc-tab-active')) {
		return;
	}

	// Clear all the current active tab classes.
	el.tabs.forEach((tabItem) => {
		tools.removeClass(tabItem, 'bc-tab-active');
		tabItem.setAttribute('aria-selected', false);
	});

	// Clear all the current active container classes.
	el.contentContainers.forEach((tabItem) => {
		tools.removeClass(tabItem, 'bc-tab-active');
		tabItem.setAttribute('aria-hidden', true);
	});

	// Set the active tab classes and attributes.
	tools.addClass(tabListItem, 'bc-tab-active');
	tools.addClass(targetContainer, 'bc-tab-active');
	tabListItem.setAttribute('aria-selected', true);
	targetContainer.setAttribute('aria-hidden', false);

	// Go grab the first page of cards for this tab.
	trigger({ event: 'bigcommerce/get_paginated_resource_cards', data: { cardKey: tabTarget, pageButton: 1 }, native: false });
	// Run the lazyload BG srcset image function on those cards.
	handleBGCardImage(null, targetContainer);
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-resources-tab-button"]', 'click', initTabClicks);
	on(document, 'bigcommerce/handle_resources_card_image', handleBGCardImage);
};

const cacheElements = () => {
	el.tabs = tools.getNodes('bc-resources-tab', true, el.container);
	el.firstTab = tools.getNodes('bc-resources-tab-button', false, el.container)[0];
	el.contentContainers = tools.getNodes('bc-resource-tab-content', true, el.container);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	initTabClicks();
	bindEvents();
};

export default init;
