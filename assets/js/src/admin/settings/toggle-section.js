/**
 * @module Toggle Sections
 * @description Settings page toggle sections scripts.
 */

import _ from 'lodash';
import delegate from 'delegate';
import queryToJson from 'utils/data/query-to-json';
import Choices from 'choices.js';
import * as tools from '../../utils/tools';
import { up, down } from '../../utils/dom/slide';
import parseUrl from '../../utils/data/parse-url';
import scrollTo from '../../utils/dom/scroll-to';

const el = {
	container: tools.getNodes('.bc-settings-form', false, document, true)[0],
	page: tools.getNodes('.bc-settings', false, document, true)[0],
	url: parseUrl(window.location.href),
};

const state = {
	timeout: 300,
};

const scrollToOptions = {
	duration: 250,
	easing: 'linear',
	offset: 0,
	$target: '',
};

/**
 * @function scrollToSection
 * @description animate the page scroll position to the deep linked section.
 */
const scrollToSection = (section) => {
	scrollToOptions.$target = jQuery(section);
	scrollToOptions.offset = -40;
	scrollTo(scrollToOptions);
};

/**
 * @function expandSection
 * @description expand section and control timing of overlay style.
 */
const expandSection = (section, target) => {
	if (el.settingSections.length === 0) {
		return;
	}
	const trigger = tools.getNodes('section-toggle-trigger', false, section)[0];
	trigger.setAttribute('aria-expanded', true);

	tools.addClass(section, 'bc-settings-section--open');
	down(target, state.timeout);
	_.delay(() => {
		target.style.overflow = 'visible';
	}, (state.timeout));
	target.removeAttribute('hidden');
};

/**
 * @function collapseSection
 * @description collapse section and control timing of overlay style.
 */
const collapseSection = (section, target) => {
	if (el.settingSections.length === 0) {
		return;
	}
	const trigger = tools.getNodes('section-toggle-trigger', false, section)[0];
	trigger.setAttribute('aria-expanded', false);

	tools.removeClass(section, 'bc-settings-section--open');
	target.style.overflow = 'hidden';
	up(target, state.timeout);
	target.setAttribute('hidden', '');
};

/**
 * @function toggleSection
 * @description toggle the setting sections open and closed depending on state.
 */
const toggleSection = (e) => {
	e.preventDefault();

	if (el.settingSections.length === 0) {
		return;
	}

	const section = tools.closest(e.target, '[data-js="section-toggle"]');
	const target = tools.getNodes('section-toggle-target', false, section)[0];

	if (tools.hasClass(section, 'bc-settings-section--open')) {
		collapseSection(section, target);
	} else {
		expandSection(section, target);
	}
};

/**
 * @function keyboardNavigation
 * @description allow arrow up/down buttons to control accordion trigger focus.
 */
const keyboardNavigation = (e) => {
	const triggers = tools.getNodes('section-toggle-trigger', true, el.container);
	const target = e.target;
	const key = e.which.toString();

	if (target.classList.contains('bc-settings-section__header')) {
		if (key.match(/38|40/)) {
			const index = triggers.indexOf(target);
			const direction = (key.match(/34|40/)) ? 1 : -1;
			const newIndex = (index + triggers.length + direction) % triggers.length;

			triggers[newIndex].focus();

			e.preventDefault();
		}
	}

	// Prevents spacebar triggering select dropdown with page scroll
	if (target.matches('.bc-choices.is-focused') && e.keyCode === 32) {
		e.preventDefault();
	}
};

/**
 * @function initChoices
 * @description initialize the settings page choices fields.
 */
const initChoices = () => {
	const options = {
		duplicateItemsAllowed: false,
		searchEnabled: false,
		placeholder: false,
		shouldSort: false,
		classNames: {
			containerOuter: 'bc-choices choices',
		},
	};

	el.settingSelectChoices = new Choices('.bc-field-choices', options);
};

const cacheElements = () => {
	el.settingSections = tools.getNodes('section-toggle', true, el.container);
	el.settingSelectFields = tools.getNodes('.bc-field-choices', true, el.container, true);
};

/**
 * @function maybeOpenSections
 * @description Maybe open specific sections depending on the page we're displaying.
 */
const maybeOpenSections = () => {
	// Case: BigCommerce onboading Channel Settings page should have sections open.
	if (queryToJson(el.url.query).page === 'bigcommerce_channel') {
		el.settingSections.forEach((section) => {
			const target = tools.getNodes('section-toggle-target', false, section)[0];
			expandSection(section, target);
		});
	}
};

/**
 * @function bindEvents
 * @description bind all event listeners to this function.
 */
const bindEvents = () => {
	delegate(el.container, '[data-js="section-toggle-trigger"]', 'click', toggleSection);

	if (el.page) {
		delegate(el.page, '.bc-settings-form', 'keydown', keyboardNavigation);
	}
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();

	if (el.settingSelectFields.length) {
		initChoices();
	}

	if (el.settingSections.length === 1) {
		const section = el.settingSections[0];
		const target = tools.getNodes('section-toggle-target', false, section)[0];
		expandSection(section, target);
	} else if (el.url.fragment) {
		const section = tools.getNodes(`#${el.url.fragment}`, false, el.container, true)[0];
		const target = tools.getNodes('section-toggle-target', false, section)[0];
		expandSection(section, target);
		scrollToSection(section);
	}

	maybeOpenSections();
	bindEvents();
};

export default init;
