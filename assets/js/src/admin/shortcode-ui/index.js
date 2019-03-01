/**
 * @module Shortcode UI
 * @description Clearinghouse for loading all Shortcode UI scripts.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { trigger } from '../../utils/events';
import dialog from './dialog-ui';

const el = {
	trigger: tools.getNodes('bc-add-products', false)[0],
	uiContainer: tools.getNodes('bc-shortcode-ui-container', false)[0],
};

const triggerDialog = (e) => {
	const { target } = e;
	trigger({ event: 'bigcommerce/init_shortcode_ui', data: { target }, native: false });
};

const bindEvents = () => {
	delegate(document, '[data-js="bc-add-products"]', 'click', triggerDialog);
};

const init = () => {
	if (!el.trigger) {
		return;
	}

	dialog();

	bindEvents();

	console.info('BigCommerce: Initialized Shortcode UI Scripts.');
};

export default init;
