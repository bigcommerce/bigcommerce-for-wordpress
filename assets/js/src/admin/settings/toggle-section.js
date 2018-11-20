/**
 * @module Toggle Sections
 * @description Settings page toggle sections scripts.
 */

import _ from 'lodash';
import delegate from 'delegate';
import Choices from 'choices.js';
import * as tools from '../../utils/tools';
import { up, down } from '../../utils/dom/slide';
import parseUrl from '../../utils/data/parse-url';
import scrollTo from '../../utils/dom/scroll-to';
import { wpAdminAjax } from '../../utils/ajax';
import { bigCommerceDiagnostics, bigCommerceDiagnosticsError } from '../templates/diagnostics';
import { DIAGNOSTICS_ACTION, DIAGNOSTICS_NONCE } from '../config/wp-settings';

const el = {
	container: tools.getNodes('.bc-settings-form', false, document, true)[0],
	url: parseUrl(window.location.href),
};

const state = {
	timeout: 300,
	diagnosticsSet: false,
	isFetching: false,
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

const getPluginDiagnostics = (target) => {
	const targetCell = target.delegateTarget ? tools.closest(target.delegateTarget, 'td') : target.querySelector('td');
	const loader = tools.getNodes('.bc-admin-diagnostics-loader', false, targetCell, true)[0];
	const activeWrapper = tools.getNodes('.bc-setings-diagnostics-wrapper', false, targetCell, true)[0];
	const getDiagnosticsWrapper = tools.closest(target.delegateTarget, '.bc-diagnostics-data');

	state.isFetching = true;
	loader.classList.add('is-active');

	if (activeWrapper) {
		activeWrapper.parentNode.removeChild(activeWrapper);
	}

	wpAdminAjax({ action: DIAGNOSTICS_ACTION, _wpnonce: DIAGNOSTICS_NONCE })
		.end((err, res) => {
			const wrapper = document.createElement('div');
			wrapper.classList.add('bc-setings-diagnostics-wrapper');

			state.isFetching = false;
			loader.classList.remove('is-active');

			if (err || !res) {
				console.error(err);
				state.diagnosticsSet = false;
				wrapper.innerHTML = bigCommerceDiagnosticsError;
				targetCell.appendChild(wrapper);
				return;
			}

			if (res.body.success === false) {
				state.diagnosticsSet = false;
				wrapper.innerHTML = bigCommerceDiagnosticsError;
				targetCell.appendChild(wrapper);
				return;
			}

			state.diagnosticsSet = true;
			getDiagnosticsWrapper.parentNode.removeChild(getDiagnosticsWrapper);
			wrapper.innerHTML = bigCommerceDiagnostics(res.body);
			targetCell.appendChild(wrapper);
		});
};

/**
 * @function expandSection
 * @description expand section and control timing of overlay style.
 */
const expandSection = (section, target) => {
	if (el.settingSections.length === 0) {
		return;
	}

	tools.addClass(section, 'bc-settings-section--open');
	down(target, state.timeout);
	_.delay(() => {
		target.style.overflow = 'visible';
	}, (state.timeout));
};

/**
 * @function collapseSection
 * @description collapse section and control timing of overlay style.
 */
const collapseSection = (section, target) => {
	if (el.settingSections.length === 0) {
		return;
	}

	tools.removeClass(section, 'bc-settings-section--open');
	target.style.overflow = 'hidden';
	up(target, state.timeout);
};

/**
 * @function toggleSection
 * @description toggle the setting sections open and closed depending on state.
 */
const toggleSection = (e) => {
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
 * @function initChoices
 * @description initialize the settings page choices fields.
 */
const initChoices = () => {
	const options = {
		duplicateItems: false,
		searchEnabled: false,
		placeholder: false,
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
 * @function bindEvents
 * @description bind all event listeners to this function.
 */
const bindEvents = () => {
	delegate(el.container, '[data-js="section-toggle-trigger"]', 'click', toggleSection);
	delegate(el.container, '[data-js="bc-admin-get-diagnostics"]', 'click', getPluginDiagnostics);
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

	bindEvents();
};

export default init;
