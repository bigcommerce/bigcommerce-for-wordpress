/**
 * @module Customizer
 * @description Handles data sync for the multiple checkbox field
 */

import delegate from 'delegate';
import { trigger } from '../../utils/events';
import * as tools from '../../utils/tools';


const el = {};

/**
 * Theme customizer JS only works with a single field per setting.
 * Multiple checkboxes just confuse it. This syncs the values
 * of those checkboxes with a hidden field, which the customizer
 * treats as the canonical value store.
 */
const syncCheckboxStore = (e) => {
	const parent = tools.closest(e.delegateTarget, '.customize-control');
	const checkboxes = tools.getNodes('input[type="checkbox"]:checked', true, parent, true);
	const store = tools.getNodes('input[type="hidden"]', false, parent, true)[0];
	const values = checkboxes.map(checkbox => checkbox.value);
	store.value = values.join(',');
	trigger({ event: 'change', el: store });
};

const bindEvents = () => {
	delegate(el.container, '.customize-control-checkbox-multiple input[type="checkbox"]', 'change', syncCheckboxStore);
};

const init = (container) => {
	if (!container) {
		return;
	}

	el.container = container;
	bindEvents();
};

export default init;
