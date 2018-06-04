/**
 * @module Product Query Builder
 * @description Using the links list, and direct text entry, add query vars to the search field.
 */

import delegate from 'delegate';
import Choices from 'choices.js';
import * as tools from '../../utils/tools';
import * as slide from '../../utils/dom/slide';
import { setAccActiveAttributes, setAccInactiveAttributes } from '../../utils/dom/accessibility';
import shortCodestate from '../config/shortcode-state';
import { on } from '../../utils/events';

const el = {};

/**
 * @function openChildMenu
 * @description Toggle the accordion open
 */
const openChildMenu = (header, content) => {
	tools.addClass(header.parentNode, 'active');
	setAccActiveAttributes(header, content);
	slide.down(content, 150);
};

/**
 * @function closeChildMenu
 * @description Toggle the accordion closed
 */
const closeChildMenu = (header, content) => {
	tools.removeClass(header.parentNode, 'active');
	setAccInactiveAttributes(header, content);
	slide.up(content, 150);
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
		shortCodestate.wpAPIQueryObj.search.push(value);
		return;
	}

	shortCodestate.wpAPIQueryObj[key].push(value);
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
		valIndex = shortCodestate.wpAPIQueryObj.search.indexOf(value);
		shortCodestate.wpAPIQueryObj.search.splice(valIndex, 1);
		return;
	}

	valIndex = shortCodestate.wpAPIQueryObj[key].indexOf(value);
	shortCodestate.wpAPIQueryObj[key].splice(valIndex, 1);
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
};

/**
 * @function removeChoice
 * @description remove a choice from the search query.
 */
const removeChoice = (value) => {
	el.searchInput.removeItemsByValue(value);
};

/**
 * @function handleChoiceAddition
 * @description run special functionality based on addItem event in Choices.js
 * @param e event object created by addItem
 */
const handleChoiceAddition = (e) => {
	if (e.detail.value === e.detail.label) {
		buildQueryObject('', e.detail.value);
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
	const key = e.delegateTarget.dataset.key;
	const value = e.delegateTarget.dataset.value;
	const label = e.delegateTarget.text;

	e.delegateTarget.classList.toggle('bcqb-item-selected');

	if (e.delegateTarget.classList.contains('bcqb-item-selected')) {
		addChoice(value, label);
		buildQueryObject(key, value);
		e.delegateTarget.setAttribute('aria-selected', 'true');
		return;
	}

	e.delegateTarget.setAttribute('aria-selected', 'false');
	removeChoice(value);
	reduceQueryObject(key, value);
};

/**
 * @function clearSearch
 * @description clear all choices from the search input and reset links and objects.
 */
const clearSearch = () => {
	el.searchInput.clearStore();
	shortCodestate.wpAPIQueryObj = {
		bigcommerce_flag: [],
		bigcommerce_brand: [],
		bigcommerce_category: [],
		recent: [],
		search: [],
	};

	el.linkList.querySelectorAll('.bcqb-item-selected').forEach((link) => {
		link.classList.remove('bcqb-item-selected');
	});
};

const cacheElements = () => {
	el.dialog = tools.getNodes('bc-shortcode-ui-products', false, document, false)[0];
	el.linkList = tools.getNodes('bcqb-list')[0];
	el.searchForm = tools.getNodes('bc-shortcode-ui-search', false, el.dialog, false)[0];
};

const bindEvents = () => {
	el.searchInput = new Choices('.bc-shortcode-ui__search-input', {
		removeItemButton: true,
	});

	delegate(el.linkList, '[data-js="bcqb-has-child-list"]', 'click', toggleChildMenu);
	delegate(el.linkList, '.bc-shortcode-ui__query-builder-anchor', 'click', handleLinks);
	el.searchInput.passedElement.addEventListener('removeItem', handleChoiceRemoval);
	el.searchInput.passedElement.addEventListener('addItem', handleChoiceAddition);
	delegate('[data-js="bcqb-clear"]', 'click', clearSearch);
	on(document, 'bigcommerce/reset_shortcode_ui', clearSearch);
};

const init = () => {
	cacheElements();
	bindEvents();
};

export default init;
