/**
 * @module Product Query Builder
 * @description Using the links list, and direct text entry, add query vars to the search field.
 */

import _ from 'lodash';
import delegate from 'delegate';
import Choices from 'choices.js';
import * as tools from 'utils/tools';
import * as slide from 'utils/dom/slide';
import { setAccActiveAttributes, setAccInactiveAttributes } from 'utils/dom/accessibility';
import { on, trigger } from 'utils/events';
import shortcodeState from 'adminConfig/shortcode-state';

const el = {};

const state = {
	slideSpeed: 150,
	delaySpeed: 100,
};

/**
 * @function openChildMenu
 * @description Toggle the accordion open
 */
const openChildMenu = (header, content) => {
	tools.addClass(header.parentNode, 'active');
	setAccActiveAttributes(header, content);
	slide.down(content, state.slideSpeed);
};

/**
 * @function closeChildMenu
 * @description Toggle the accordion closed
 */
const closeChildMenu = (header, content) => {
	tools.removeClass(header.parentNode, 'active');
	setAccInactiveAttributes(header, content);
	slide.up(content, state.slideSpeed);
};

/**
 * @function toggleChildMenu
 * @description Toggle child menu lists.
 * @param e event
 */
const toggleChildMenu = (e) => {
	const header = e.delegateTarget;
	const content = header.nextElementSibling;

	if (tools.hasClass(header.parentNode, 'active')) {
		closeChildMenu(header, content);
	} else {
		openChildMenu(header, content);
	}
};

/**
 * @function buildQueryObject
 * @description add new values to the wpAPIQueryObj
 * @param key string
 * @param value string
 */
const buildQueryObject = (key = '', value) => {
	if (!key) {
		shortcodeState.wpAPIQueryObj.search.push(value);
		return;
	}

	shortcodeState.wpAPIQueryObj[key].push(value);
};

/**
 * @function reduceQueryObject
 * @description remove existing values from the wpAPIQueryObj
 * @param key string
 * @param value string
 */
const reduceQueryObject = (key, value) => {
	let valIndex = '';

	if (!key) {
		valIndex = shortcodeState.wpAPIQueryObj.search.indexOf(value);
		shortcodeState.wpAPIQueryObj.search.splice(valIndex, 1);
		return;
	}

	valIndex = shortcodeState.wpAPIQueryObj[key].indexOf(value);
	shortcodeState.wpAPIQueryObj[key].splice(valIndex, 1);
};

/**
 * @function addChoice
 * @description add a choice to the search query.
 */
const addChoice = (value, label) => {
	el.searchInput.setValue([
		{
			value,
			label,
		},
	]);

	trigger({ event: 'bigcommerce/shortcode_query_term_added', data: { value, label }, native: false });
};

/**
 * @function removeChoice
 * @description remove a choice from the search query.
 */
const removeChoice = (value) => {
	el.searchInput.removeItemsByValue(value);
	trigger({ event: 'bigcommerce/shortcode_query_term_removed', data: { value }, native: false });
};

/**
 * @function handleChoiceAddition
 * @description run special functionality based on addItem event in Choices.js
 * @param e event object created by addItem
 */
const handleChoiceAddition = (e) => {
	if (e.detail.value === e.detail.label) {
		buildQueryObject('', e.detail.value);
		trigger({ event: 'bigcommerce/shortcode_query_term_added', data: { value: e.detail.value, label: e.detail.label }, native: false });
	}
};

/**
 * @function handleChoiceRemoval
 * @description run special functionality based on removeItem event in Choices.js
 * @param e event object created by removeItem
 */
const handleChoiceRemoval = (e) => {
	const value = e.detail.value;
	const link = tools.getNodes(`[data-value="${value}"]`, false, el.linkList, true)[0];
	trigger({ event: 'bigcommerce/shortcode_query_term_removed', data: { value }, native: false });

	if (e.detail.fromSettings) {
		removeChoice(value);
	}

	if (!link) {
		reduceQueryObject('', value);
		return;
	}

	if (link && link.classList.contains('bcqb-item-selected')) {
		const key = link.dataset.key;

		link.classList.remove('bcqb-item-selected');
		reduceQueryObject(key, value);
	}
};

/**
 * @function handleLinks
 * @description Handle the link click event and add/remove items from the search query.
 * @param e event
 */
const handleLinks = (e) => {
	const element = e.delegateTarget ? e.delegateTarget : e;
	const key = e.delegateTarget ? e.delegateTarget.dataset.key : e.dataset.key;
	const value = e.delegateTarget ? e.delegateTarget.dataset.value : e.dataset.value;
	const label = e.delegateTarget ? e.delegateTarget.text : e.text;

	element.classList.toggle('bcqb-item-selected');

	if (element.classList.contains('bcqb-item-selected')) {
		addChoice(value, label);
		buildQueryObject(key, value);
		element.setAttribute('aria-selected', 'true');
		return;
	}

	element.setAttribute('aria-selected', 'false');
	removeChoice(value);
	reduceQueryObject(key, value);
};

/**
 * @function resetChannelSelect
 * @description Upon opening the UI dialog box, reset the channel select field to the default value.
 */
const resetChannelSelect = () => {
	if (!el.channels) {
		return;
	}

	for (let i = 0; i < el.channels.length; i++) {
		const primary = el.channels.options[i].dataset.primary;
		if (primary.length > 0) {
			el.channels.selectedIndex = i;
		}
	}
};

/**
 * @function handleChannelSelection
 * @description When the channel select field is changed, immediately submit a new ajax query to get that channel's products.
 * @param e
 */
const handleChannelSelection = (e) => {
	shortcodeState.wpAPIQueryObj.bigcommerce_channel = e.delegateTarget.value;
	trigger({ event: 'bigcommerce/get_channel_products', native: false });
};

/**
 * @function clearSearch
 * @description clear all choices from the search input and reset links and objects.
 */
const clearSearch = () => {
	el.searchInput.clearStore();
	shortcodeState.wpAPIQueryObj = {
		bigcommerce_flag: [],
		bigcommerce_brand: [],
		bigcommerce_category: [],
		bigcommerce_channel: '',
		recent: [],
		search: [],
	};

	el.linkList.querySelectorAll('.bcqb-item-selected').forEach((link) => {
		link.classList.remove('bcqb-item-selected');
	});
};

/**
 * @function addSavedUICustomChoices
 * @description Add custom search/query terms to the search field before running the query.
 * @param choices
 */
const addSavedUICustomChoices = (choices) => {
	choices.forEach(choice => addChoice(choice, choice));
};

/**
 * @function initQueryParamSelections
 * @description If a saved term exists, fire a click event on that item to add it to the search bar and state object.
 * @param terms
 */
const initQueryParamSelections = (terms) => {
	if (!terms) {
		return;
	}

	terms.forEach((slug) => {
		const listLink = tools.getNodes(`[data-slug="${slug}"]`, false, el.linkList, true)[0];
		const listParent = tools.closest(listLink, '[data-js="bcqb-parent-list-item"]:not(.active)');

		handleLinks(listLink);

		if (slug[0] && listParent) {
			const header = tools.getNodes('bcqb-has-child-list', false, listParent)[0];
			const content = header.nextElementSibling;
			_.delay(() => openChildMenu(header, content), state.delaySpeed);
		}
	});
};

/**
 * @function setShortcodeState
 * @description When the UI dialog is triggered, reset the UI and, if applicable, populate it with saved state data.
 * @param event
 */
const setShortcodeState = (event) => {
	if (!event.detail.params) {
		return;
	}

	const currentBlockParams = event.detail.params;

	clearSearch();

	Object.entries(currentBlockParams).forEach(([key, value]) => {
		switch (key) {
		case 'brand':
		case 'category':
			initQueryParamSelections([...value.split(',')]);
			break;
		case 'featured':
		case 'sale':
		case 'recent':
			initQueryParamSelections([key]);
			break;
		case 'search':
			addSavedUICustomChoices([...value.split(',')]);
			break;
		default:
			break;
		}
	});

	resetChannelSelect();

	_.delay(() => trigger({ event: 'bigcommerce/shortcode_ui_state_ready', native: false }), state.delaySpeed);
};

const cacheElements = () => {
	el.wrapper = tools.getNodes('bc-shortcode-ui-container', false, document)[0];
	el.productsBlock = tools.getNodes('bc-shortcode-ui-products', false, document)[0];
	el.linkList = tools.getNodes('bcqb-list')[0];
	el.searchForm = tools.getNodes('bc-shortcode-ui-search', false, el.productsBlock)[0];
	el.channels = tools.getNodes('bcqb-channels', false, el.wrapper)[0];
	el.searchField = document.querySelector('.bc-shortcode-ui__search-input');
	el.searchInput = new Choices(el.searchField, {
		removeItemButton: true,
		duplicateItemsAllowed: false,
	});
};

const bindEvents = () => {
	delegate(el.linkList, '[data-js="bcqb-has-child-list"]', 'click', toggleChildMenu);
	delegate(el.linkList, '.bc-shortcode-ui__query-builder-anchor', 'click', handleLinks);
	delegate(el.wrapper, '[data-js="bcqb-channels"]', 'change', handleChannelSelection);
	el.searchField.addEventListener('removeItem', handleChoiceRemoval);
	el.searchField.addEventListener('addItem', handleChoiceAddition);
	delegate('[data-js="bcqb-clear"]', 'click', clearSearch);
	on(document, 'bigcommerce/set_shortcode_ui_state', setShortcodeState);
	on(document, 'bigcommerce/remove_query_term', handleChoiceRemoval);
};

const init = () => {
	cacheElements();
	bindEvents();
};

export default init;
