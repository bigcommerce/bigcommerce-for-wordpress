/**
 * @module Navigation Menus
 * @description Scripts that handle interactions on the Nav Menu setup screen for onboarding.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';

const el = {
	container: tools.getNodes('.bc-settings-bigcommerce_nav_setup', false, document, true)[0],
};

/**
 * @function handleMenuSelectField
 * @description on load and on change, check the select field value and show or hide the new menu name field.
 */
const handleMenuSelectField = () => {
	// If the value is not set to 'new', hide the New Menu Name text field.
	if (el.menuSelectField.value !== 'new') {
		el.newMenuNameField.style.display = 'none';
		tools.removeClass(el.newMenuNameField, 'bc-settings-field-active');
		return;
	}

	// If the value is set to 'new', show the New Menu Name text field.
	el.newMenuNameField.style.display = 'table-row';
};

const cacheElements = () => {
	el.menuSelectField = tools.getNodes('bc-settings-select-menu-field', false, el.container)[0];
	el.newMenuNameField = tools.getNodes('.bc-settings-new-menu-field', false, el.container, true)[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-settings-select-menu-field"]', 'change', handleMenuSelectField);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
	handleMenuSelectField();
};

export default init;
