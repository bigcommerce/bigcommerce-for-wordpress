/**
 * @module Form Errors
 * @description Scripts for handling form error messages.
 */

import _ from 'lodash';
import * as tools from '../../utils/tools';
import { formErrorMessage } from '../templates/form-error-message';

const el = {
	container: tools.getNodes('.bc-alert-group--error', false, document, true)[0],
};

const cacheElements = () => {
	el.formWrapper = tools.getNodes('.bc-form--has-errors', false, document, true)[0];
};

/**
 * @function initFormErrorMessageHandler
 * @description If we have an error message alert, loop through the messages and find ones with related form fields. If
 *     we have a match, move that message inline to the form and hide the error in the alert node.
 */
const initFormErrorMessageHandler = () => {
	tools.getNodes('.bc-alert--error', true, el.container, true).forEach((error) => {
		const key = error.dataset.messageKey;
		const input = tools.getNodes(`[data-form-field="bc-form-field-${key}"]`, false, el.formWrapper, true)[0];

		if (!input) {
			return;
		}

		error.style.display = 'none';
		input.parentNode.insertAdjacentHTML('beforeend', formErrorMessage(error.innerHTML));
	});

	_.delay(() => tools.addClass(el.container, 'bc-fade-in-alert-group'), 50);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	initFormErrorMessageHandler();
};

export default init;
