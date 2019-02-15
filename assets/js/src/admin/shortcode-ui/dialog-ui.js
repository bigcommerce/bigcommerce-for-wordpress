/**
 * @module Shortcode UI Dialog Box
 * @description Initialize the dialog box for the Shortcode UI.
 */

import _ from 'lodash';
import A11yDialog from 'mt-a11y-dialog';
import * as tools from '../../utils/tools';

import queryBuilder from './query-builder';
import productSelection from './product-selection';
import displaySettings from './display-settings';
import createShortcode from './create-shortcode';
import ajaxQuery from './ajax-query';
import shortcodeState from '../config/shortcode-state';
import { on, trigger } from '../../utils/events';

const el = {
	container: tools.getNodes('bc-shortcode-ui')[0],
};

const instances = {};

const state = {
	rendered: false,
	delaySpeed: 50,
};

const hideDialog = () => {
	instances.dialog.hide();
};

const showDialog = () => {
	instances.dialog.show();

	if (!shortcodeState.isGutenberg) {
		_.delay(() => trigger({ event: 'bigcommerce/shortcode_ui_state_ready', native: false }), state.delaySpeed);
	}
};

const setQueryParams = (params) => {
	if (!params) {
		trigger({ event: 'bigcommerce/set_shortcode_ui_state', native: false });
		return;
	}

	trigger({ event: 'bigcommerce/set_shortcode_ui_state', data: { params }, native: false });
};

/**
 * @function initDialogUI
 * @description initialize the dialog box.
 * @param target
 */
const initDialogUI = (target, params) => {
	const options = {
		appendTarget: '#wpwrap',
		trigger: target,
		bodyLock: true,
		effect: 'fade',
		effectSpeed: 200,
		effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
		overlayClasses: 'bc-shortcode-ui__overlay',
		contentClasses: 'bc-shortcode-ui__content',
		wrapperClasses: 'bc-shortcode-ui__wrapper',
		closeButtonClasses: 'bc-shortcode-ui__close-button bc-icon icon-bc-cross',
	};

	instances.dialog = new A11yDialog(options);

	state.rendered = true;

	instances.dialog.on('render', (dialogEl, e) => {
		_.delay(() => {
			productSelection();
			displaySettings();
			queryBuilder();
			ajaxQuery();
			createShortcode();
			setQueryParams(params);
		}, state.delaySpeed);

		const button = !e ? target : e.target;
		shortcodeState.currentEditor = tools.closest(button, '.wp-editor-wrap');
	});

	instances.dialog.on('show', (dialogEl, e) => {
		const button = !e ? target : e.target;
		shortcodeState.currentEditor = tools.closest(button, '.wp-editor-wrap');
	});

	instances.dialog.on('hide', () => {
		shortcodeState.currentEditor = '';
	});

	showDialog();
};

/**
 * @function dialogPostRender
 * @description create a new A11yDialog and set dialog state functions after dialog renders.
 */
const toggleShortcodeUIDialog = (event) => {
	const target = event.detail.target;
	const params = event.detail.queryParams;

	if (state.rendered && event.detail.hide) {
		hideDialog();
		trigger({ event: 'bigcommerce/hide_shortcode_ui', native: false });
		return;
	}

	if (state.rendered) {
		setQueryParams(params);
		showDialog();
		return;
	}

	initDialogUI(target, params);
};

const init = () => {
	if (!el.container) {
		return;
	}

	on(document, 'bigcommerce/init_shortcode_ui', toggleShortcodeUIDialog);
};

export default init;
