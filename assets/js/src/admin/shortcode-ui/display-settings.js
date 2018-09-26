/**
 * @module Display Settings
 * @description Scripts for settings additional shortcode parameters for display settings.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import shortcodeState from '../config/shortcode-state';
import { on, trigger } from '../../utils/events';
import { termTemplate } from './term-template';

const el = {};

const state = {
	termListActive: false,
};

const setPostsPerPageIndicator = (value) => {
	if (!value) {
		return;
	}

	el.postsPerPageIndicator.textContent = '';
	el.postsPerPageIndicator.textContent = value;
};

const setPostsPerPageResetValue = (value) => {
	if (!value) {
		return;
	}

	el.resetButton.dataset.resetValue = value;
};

const resetPostsPerPage = (event) => {
	if (!event) {
		return;
	}

	const value = event.delegateTarget.dataset.resetValue;
	shortcodeState.wpAPIDisplaySettings.per_page = value;
	el.postsPerPageField.value = value;
	setPostsPerPageIndicator(value);
};

const setPostsPerPage = (event, params = {}) => {
	let paramPerPage = '';
	if (event) {
		paramPerPage = event.delegateTarget.value;
		shortcodeState.wpAPIDisplaySettings.per_page = paramPerPage;
		setPostsPerPageIndicator(paramPerPage);
		return;
	}

	if (!params.per_page) {
		setPostsPerPageIndicator(el.postsPerPageIndicator.dataset.default);
		el.postsPerPageField.value = el.postsPerPageIndicator.dataset.default;
		return;
	}

	paramPerPage = params.per_page;
	shortcodeState.wpAPIDisplaySettings.per_page = paramPerPage;
	el.postsPerPageField.value = paramPerPage;
	setPostsPerPageIndicator(paramPerPage);
	setPostsPerPageResetValue(paramPerPage);
};

const setOrderParam = (event, params = {}) => {
	if (event) {
		shortcodeState.wpAPIDisplaySettings.order = event.delegateTarget.value;
		return;
	}

	const orderParam = params.order ? params.order.toLowerCase() : 'asc';
	const field = tools.getNodes(`#bc-shortcode-ui__product-order--${orderParam}`, false, el.displaySettings, true);

	if (field.length === 0) {
		return;
	}

	shortcodeState.wpAPIDisplaySettings.order = orderParam;
	field[0].checked = true;
};

const setOrderbyParam = (event, params = {}) => {
	if (event) {
		shortcodeState.wpAPIDisplaySettings.orderby = event.delegateTarget.value;
		return;
	}

	const orderByParam = params.orderby ? params.orderby.toLowerCase() : 'date';
	const field = tools.getNodes(`#bc-shortcode-ui__product-orderby--${orderByParam}`, false, el.displaySettings, true);

	if (field.length === 0) {
		return;
	}

	shortcodeState.wpAPIDisplaySettings.orderby = orderByParam;
	field[0].checked = true;
};

const showHideDefaultSettingsHeader = (active = false) => {
	const header = tools.getNodes('.bc-shortcode-ui__default-header', false, el.displaySettingsHeader, true)[0];

	if (!active) {
		tools.removeClass(header, 'active');
		return;
	}

	tools.addClass(header, 'active');
};

const showHideTermsList = (active = false) => {
	const header = tools.getNodes('.bc-shortcode-ui__dynamic-listing-header', false, el.displaySettingsHeader, true)[0];

	if (!active) {
		tools.removeClass(header, 'active');
		tools.removeClass(el.termsListWrapper, 'active');
		state.termListActive = false;
		return;
	}

	state.termListActive = true;
	tools.addClass(header, 'active');
	tools.addClass(el.termsListWrapper, 'active');
};

const showHideProductList = (active = false) => {
	const header = tools.getNodes('.bc-shortcode-ui__manual-listing-header', false, el.displaySettingsHeader, true)[0];

	if (!active) {
		tools.removeClass(header, 'active');
		tools.removeClass(el.productListWrapper, 'active');
		return;
	}

	tools.addClass(el.productListWrapper, 'active');
	tools.addClass(header, 'active');
};

const toggleDisplaySettings = () => {
	if (tools.getChildren(el.productList).length > 0) {
		showHideDefaultSettingsHeader(false);
		showHideTermsList(false);
		showHideProductList(true);
		return;
	}

	if (tools.getChildren(el.productList).length <= 0 && tools.getChildren(el.termsList).length > 0) {
		showHideDefaultSettingsHeader(false);
		showHideProductList(false);
		showHideTermsList(true);
		return;
	}

	showHideTermsList(false);
	showHideProductList(false);
	showHideDefaultSettingsHeader(true);
};

const handleQueryTermRemoval = (event = {}, value = '') => {
	let termID = '';
	if (event) {
		termID = event.detail.value;
	} else {
		termID = value;
	}

	const term = tools.getNodes(`[data-value="${termID}"]`, false, el.termsList, true)[0];
	if (!term) {
		return;
	}

	const termItem = tools.closest(term, '.bc-shortcode-ui__terms-list-item');
	termItem.parentNode.removeChild(termItem);
	toggleDisplaySettings();
};

const removeTermOnClick = (event) => {
	const value = event.delegateTarget.dataset.value;
	handleQueryTermRemoval(null, value);
	trigger({ event: 'bigcommerce/remove_query_term', data: { value, fromSettings: true }, native: false });
};

const handleQueryTermAddition = (data) => {
	const termItem = tools.getNodes(`[data-value="${data.detail.value}"]`, false, el.termsList, true)[0];
	if (termItem) {
		return;
	}

	el.termsList.insertAdjacentHTML('beforeend', termTemplate(data.detail));
	toggleDisplaySettings();
};

const resetDisplaySettings = () => {
	setPostsPerPageIndicator(el.resetButton.dataset.resetValue);
	shortcodeState.wpAPIDisplaySettings.per_page = '';
	shortcodeState.wpAPIDisplaySettings.orderby = '';
	shortcodeState.wpAPIDisplaySettings.order = '';
	el.termsList.textContent = '';
};

const handleSavedUIDisplaySettings = (event) => {
	const params = event.detail.params;

	resetDisplaySettings();
	setOrderbyParam(null, params);
	setOrderParam(null, params);
	setPostsPerPage(null, params);
	toggleDisplaySettings();
};

const cacheElements = () => {
	el.settingsSidebar = tools.getNodes('bc-shortcode-ui-settings')[0];
	el.displaySettings = tools.getNodes('bc-shortcode-ui-display-settings', false, el.settingsSidebar)[0];
	el.resetButton = tools.getNodes('bc-shortcode-ui-reset-posts-per-page', false, el.displaySettings)[0];
	el.postsPerPageField = tools.getNodes('#bc-shortcode-ui__posts-per-page', false, el.displaySettings, true)[0];
	el.postsPerPageIndicator = tools.getNodes('.bc-shortcode-ui__posts-per-page-value', false, el.displaySettings, true)[0];
	el.displaySettingsHeader = tools.getNodes('bc-shortcode-ui-settings-header', false, el.settingsSidebar)[0];
	el.productListWrapper = tools.getNodes('bc-shortcode-ui-selected-products', false, el.settingsSidebar)[0];
	el.productList = tools.getNodes('bc-shortcode-ui-product-list', false, el.productListWrapper)[0];
	el.termsListWrapper = tools.getNodes('bc-shortcode-ui-selected-terms', false, el.settingsSidebar)[0];
	el.termsList = tools.getNodes('bc-shortcode-ui-terms-list', false, el.termsListWrapper)[0];
};

const bindEvents = () => {
	delegate(el.displaySettings, '#bc-shortcode-ui__posts-per-page', 'input', setPostsPerPage);
	delegate(el.displaySettings, '[name="bc-shortcode-ui__product-order"]', 'click', setOrderParam);
	delegate(el.displaySettings, '[name="bc-shortcode-ui__product-orderby"]', 'click', setOrderbyParam);
	delegate(el.displaySettings, '[data-js="bc-shortcode-ui-reset-posts-per-page"]', 'click', resetPostsPerPage);
	delegate(el.settingsSidebar, '[data-js="bc-shortcode-ui-remove-term"]', 'click', removeTermOnClick);
	on(document, 'bigcommerce/set_shortcode_ui_state', handleSavedUIDisplaySettings);
	on(document, 'bigcommerce/shortcode_product_list_event', toggleDisplaySettings);
	on(document, 'bigcommerce/shortcode_query_term_added', handleQueryTermAddition);
	on(document, 'bigcommerce/shortcode_query_term_removed', handleQueryTermRemoval);
};

const init = () => {
	cacheElements();
	bindEvents();
};

export default init;
