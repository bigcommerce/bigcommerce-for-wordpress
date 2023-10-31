/**
 * @module Resources Content
 * @description Build resources content based on the JSON blob.
 */

import delegate from 'delegate';
import _ from 'lodash';
import * as tools from '../../utils/tools';
import { resourceCard, resourceVideoCard, tabButton, tabCardsContent, tabVideoCardsContent, tabContentContainer, paginationLink, noDataAvailable, videoPlaylist } from '../templates/resources';
import { ADMIN_IMAGES } from '../config/wp-settings';
import { on, trigger } from '../../utils/events';
import scrollTo from '../../utils/dom/scroll-to';
import { WP_ADMIN_BAR_HEIGHT } from '../config/options';
import { I18N } from '../config/i18n';

const el = {
	container: tools.getNodes('bc-resources-wrapper')[0],
};

const tabInstances = {
	tabs: [],
	cards: [],
	videos: [],
	cards_per_page: 12,
};

const scrollToOptions = {
	auto_coefficient: 2.5,
	duration: 500,
	easing: 'linear',
	offset: -WP_ADMIN_BAR_HEIGHT + -60,
	$target: jQuery('.bc-resources-tabs'),
};

/**
 * @function noJSONData
 * @description When we don't have data or there are no results in the specified array key, show this message.
 * @param container
 */
const noJSONData = (container = el.contentContainer) => {
	container.innerHTML = noDataAvailable;
};

/**
 * @function createCards
 * @description Build the HTML elements for each card data available in the bigcommerce_resources_json JSON.
 * @param cards
 * @param label
 */
const createCards = (cards, label) => {
	Object.values(cards).forEach((card) => {
		const smallThumb = _.get(card, 'thumbnail.small');
		const largeThumb = _.get(card, 'thumbnail.large');
		// If we have images, use them. Otherwise, we'll use the BigCommerce logo thumbnail placeholder.
		const image1 = smallThumb ? `${smallThumb} 1x,` : `${ADMIN_IMAGES}bigcommerce-resource-thumbnail.png 1x,`;
		const image2 = largeThumb ? `${largeThumb} 2x,` : `${ADMIN_IMAGES}bigcommerce-resource-thumbnail-2x.png 2x`;
		// Create a card and push it as a new indexed item in the cards array.
		tabInstances.cards[label].push(resourceCard(image1, image2, card.name, card.description, card.url));
	});
};

/**
 * @function createVideoCards
 * @description Build the HTML elements for each video card data available in the bigcommerce_resources_json JSON.
 * @param videos
 * @param label
 * @param playlistLabel
 */
const createVideoCards = (videos = {}, label = '', playlistLabel = '') => {
	Object.values(videos).forEach((card) => {
		const smallThumb = _.get(card, 'thumbnail.small');
		const largeThumb = _.get(card, 'thumbnail.large');
		// If we have images, use them. Otherwise, we'll use the BigCommerce logo thumbnail placeholder.
		const image1 = smallThumb ? `${smallThumb} 1x,` : `${ADMIN_IMAGES}bigcommerce-resource-thumbnail.png 1x,`;
		const image2 = largeThumb ? `${largeThumb} 2x,` : `${ADMIN_IMAGES}bigcommerce-resource-thumbnail-2x.png 2x`;
		// Create a card and push it as a new indexed item in the cards array.
		tabInstances.videos[playlistLabel].push(resourceVideoCard(image1, image2, card.name, card.video_length, card.description, card.url));
	});
};

/**
 * @function createVideoPlaylists
 * @description Creates an array of playlists with completed markup.
 * @param playlists
 * @param label
 */
const createVideoPlaylists = (playlists = {}, label = '') => {
	Object.values(playlists).forEach((playlist) => {
		const playlistLabel = playlist.playlist_label;
		tabInstances.videos[playlistLabel] = [];

		tabInstances.videos[playlistLabel].push(createVideoCards(playlist.videos, label, playlistLabel));
		tabInstances.cards[label].push(videoPlaylist(playlistLabel, playlist.playlist, tabInstances.videos[playlistLabel].join(''), tabInstances.videos[playlistLabel].length - 1));
	});
};

/**
 * @function createTabs
 * @description Create a new tab button for each available tabInstances.tabs;
 */
const createTabs = () => {
	const tabContainer = tools.getNodes('bc-resources-tabs-list')[0];
	const tabsMarkup = tabInstances.tabs.length > 0 ? tabInstances.tabs.join('') : '';

	// If for some reason the data is missing or no longer available, stop production and print the error message.
	if (!tabsMarkup) {
		noJSONData();
		return;
	}

	// Let's make some tabs.
	tabContainer.innerHTML = tabsMarkup;
};

/**
 * @function createPagination
 * @description If we have a list of cards longer than 12 items, create pagination buttons for each available page.
 * @param key
 * @param items
 */
const createPagination = (key = '', items = 0) => {
	const tabContainer = tools.getNodes(`[data-tab-name="${key}"]`, false, el.contentContainer, true)[0];
	const pages = items / tabInstances.cards_per_page;
	const paginationWrapper = tabContainer.querySelector('.bc-resources-pagination-wrapper');

	if (!paginationWrapper) {
		return;
	}

	for (let i = 0; i < pages; i++) {
		paginationWrapper.insertAdjacentHTML('beforeend', paginationLink(i + 1, key));
	}
};

/**
 * @function createTabContentContainers
 * @description Create the empty parent containers used by the tab buttons to populate paged card nodes.
 */
const createTabContentContainers = () => {
	// Create a wrapper element to attach the containers to.
	const contentWrapper = document.createElement('div');
	tools.addClass(contentWrapper, 'bc-resources-tabs__content');
	el.contentContainer.appendChild(contentWrapper);

	// Loop through the available keys and create the parent container tied to the tab button.
	Object.entries(tabInstances.cards).forEach(([key, value]) => {
		contentWrapper.insertAdjacentHTML('beforeend', tabContentContainer(key));
		// If we have more than 12 cards, create a pagination node for this container.
		if (value.length > 12) {
			createPagination(key, value.length);
		}
	});
};

/**
 * @function setActivePaginationButton
 * @description Set the proper active page button. Can be determined by the bigcommerce/get_paginated_resource_cards
 *     custom event or by clicking directly on the button.
 * @param e
 * @param button
 */
const setActivePaginationButton = (e, button) => {
	let pageButton = button;
	if (e) {
		const tabParent = tools.getNodes(`[data-tab-name="${e.detail.cardKey}"]`, false, el.container, true)[0];
		pageButton = tools.getNodes('bc-resources-pagination-button', false, tabParent)[0];
	}

	if (!pageButton) {
		return;
	}

	tools.addClass(pageButton, 'bc-resources-page-active');
	pageButton.setAttribute('aria-current', true);
};

/**
 * @function resetPaginationButtons
 * @description Reset all the active states for any existing pagination buttons.
 * @param contentContainer
 */
const resetPaginationButtons = (contentContainer = '') => {
	// Reset pagination all the buttons to the inactive state.
	tools.getNodes('bc-resources-pagination-button', true, contentContainer).forEach((button) => {
		tools.removeClass(button, 'bc-resources-page-active');
		button.setAttribute('aria-current', false);
	});
};

/**
 * @function resetPagedResultsNodes
 * @description Remove active classes/attributes from paged content after a tab click or pagination button click.
 * @param containers
 */
const resetPagedResultsNodes = (containers = []) => {
	containers.forEach((page) => {
		tools.removeClass(page, 'bc-cards-page-active');
		page.setAttribute('aria-hidden', true);
	});
};

/**
 * @function focusFirstPagedCard
 * @description When we display a page of cards, focus the first one to help with keyboard navigation.
 * @param key
 * @param currentPage
 */
const focusFirstPagedCard = (key, currentPage) => {
	const page = tools.getNodes(`[data-resource-page-name="${key}"][data-resource-page-number="${currentPage}"]`, false, el.contentContainer, true)[0];
	const firstCard = tools.getNodes('.bc-resource-card__link', false, page, true)[0];

	if (!firstCard) {
		return;
	}

	_.delay(() => firstCard.focus(), 300);
};

/**
 * @function getPaginatedItems
 * @description Main Payload Function. This function gets the paged cards and injects the node into the corresponding
 *     parent container.
 * @param e
 * @param cardKey
 * @param currentPage
 */
const getPaginatedItems = (e, cardKey = '', currentPage = 1) => {
	const key = e ? e.detail.cardKey : cardKey;
	const cardPageWrapper = tools.getNodes(`[data-tab-name="${key}"]`, false, el.container, true)[0];
	const pagedContainer = cardPageWrapper.querySelector(`[data-resource-page-name="${key}"][data-resource-page-number="${currentPage}"]`);

	// If there are no card items in the array, return the noJSON message.
	if (tabInstances.cards[key].length === 0) {
		noJSONData(cardPageWrapper);
		return;
	}

	// Reset active pages.
	resetPagedResultsNodes(tools.getNodes('.bc-cards-page-active', true, cardPageWrapper, true));

	// If the bigcommerce/get_paginated_resource_cards event triggers this call, allow the pagination to be updated.
	if (e) {
		// Reset pagination buttons
		resetPaginationButtons(cardPageWrapper);
		// Set the clicked button to the active state.
		setActivePaginationButton(e, '');
	}

	// If we have a previously cached container with these results, do not rebuild it, just display it.
	if (pagedContainer) {
		tools.addClass(pagedContainer, 'bc-cards-page-active');
		pagedContainer.setAttribute('aria-hidden', 'false');
		focusFirstPagedCard(key, currentPage);
		return;
	}

	// If we do not have a container with these results in it yet, create one and show it.
	const offset = (currentPage - 1) * tabInstances.cards_per_page;
	const paginatedItems = tabInstances.cards[key].slice(offset, offset + tabInstances.cards_per_page);

	if (key === 'Tutorials') {
		cardPageWrapper.insertAdjacentHTML('beforeend', tabVideoCardsContent(key, paginatedItems.join(''), currentPage));
	} else {
		cardPageWrapper.insertAdjacentHTML('beforeend', tabCardsContent(key, paginatedItems.join(''), currentPage));
	}

	focusFirstPagedCard(key, currentPage);
};

/**
 * @function handlePagedContentClick
 * @description If pagination is present, handle the paged button clicks.
 * @param e
 */
const handlePagedContentClick = (e) => {
	const pageNumber = e.delegateTarget.dataset.pageNumber;
	const pageName = e.delegateTarget.dataset.pageName;
	const contentContainer = tools.getNodes(`[data-tab-name=${pageName}]`, false, el.container, true)[0];
	const pagedContainers = tools.getNodes(`[data-resource-page-name="${pageName}"]`, true, contentContainer, true);

	// Reset pagination all the buttons to the inactive state.
	resetPaginationButtons(contentContainer);

	// If there is a currently active page, hide it and reset it to the inactive state.
	resetPagedResultsNodes(pagedContainers);

	// Set the clicked button to the active state.
	setActivePaginationButton(null, e.delegateTarget);

	// Now we can get the paged cards and display them.
	getPaginatedItems(null, pageName, pageNumber);

	// The eventListener will trigger the lazyload effect of the card thumbnail.
	trigger({ event: 'bigcommerce/handle_resources_card_image', data: { container: contentContainer }, native: false });
	scrollTo(scrollToOptions);
};

/**
 * @function initResourceCards
 * @description Setup the main tabInstances object with all the JSON data and then create the tabs and containers.
 */
const initResourceCards = () => {
	if (!window.bigcommerce_resources_json) {
		noJSONData();
		return;
	}

	// Run through the JSON data and create a cached object for getting and displaying tab content.
	Object.values(window.bigcommerce_resources_json.sections).forEach((section) => {
		// If we have no entries for this object key, move on and do not create a tabInstance for it.
		if (section.resources.length === 0) {
			return;
		}

		tabInstances.tabs.push(tabButton(section.label));
		tabInstances.cards[section.label] = [];

		if (section.label === 'Tutorials') {
			createVideoPlaylists(section.resources, section.label);
		} else {
			createCards(section.resources, section.label);
		}
	});

	createTabs();
	createTabContentContainers();
};

const cacheElements = () => {
	el.jsonBlob = tools.getNodes('bigcommerce-resources-json')[0];
	el.contentContainer = tools.getNodes('bc-resources-tab-content')[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-resources-pagination-button"]', 'click', handlePagedContentClick);
	on(document, 'bigcommerce/get_paginated_resource_cards', getPaginatedItems);
};

/**
 * @function init
 * @description By using a Promise here, we can setup the cached object, tabs and containers, and then allow the tabs
 *     functionality to attach itself to the tabs after we've successfully configured and printed our content.
 * @returns {Promise<any>}
 */
const init = () => new Promise((resolve, reject) => {
	if (!el.container) {
		return;
	}

	cacheElements();

	if (!el.jsonBlob) {
		noJSONData();
		reject(new Error(I18N.messages.no_resources_json_data));
	}

	initResourceCards();
	bindEvents();
	resolve();
});

export default init;
