/**
 * @module Create Account
 * @description Account creation sign up form script.
 */

import _ from 'lodash';
import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { I18N } from '../config/i18n';
import { bcAdminSpinner } from './templates';

const el = {
	container: tools.getNodes('bigcommerce_new_account')[0],
};

/**
 * @function handleAccountCreationSubmit
 * @description When the account creation form is submitted, disable the button and add an indicator that the form is processing.
 * @param e
 */
const handleAccountCreationSubmit = (e) => {
	const button = e.delegateTarget;
	const wrapper = document.createElement('div');

	_.delay(() => button.setAttribute('disabled', 'disabled'), 50);
	tools.addClass(wrapper, 'bc-settings-account-creation-wrapper');
	wrapper.innerHTML = `${bcAdminSpinner} ${I18N.messages.account_creation_message}`;
	button.parentNode.appendChild(wrapper);
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-settings-create-account-button"]', 'click', handleAccountCreationSubmit);
};

const init = () => {
	if (!el.container) {
		return;
	}

	bindEvents();
};

export default init;
