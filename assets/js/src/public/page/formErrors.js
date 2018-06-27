/**
 * @module Form Errors
 * @description Scripts for handling form error messages.
 */

import * as tools from '../../utils/tools';
import { formErrorMessage } from '../templates/form-error-message';

const el = {
	container: tools.getNodes('.bc-alert-group--error', false, document, true)[0],
};

const cacheElements = () => {
	el.formWrapper = tools.getNodes('.bc-form--has-errors', false, document, true)[0];
};

const initFormErrorMessageHandler = () => {
	tools.getNodes('.bc-alert--error', true, el.container, true).forEach((error) => {
		const key = error.dataset.messageKey;
		const message = error.innerHTML;
		const input = tools.getNodes(`[data-form-field="bc-form-field-${key}"]`, false, el.formWrapper, true)[0];
		input.parentNode.insertAdjacentHTML('beforeend', formErrorMessage(message));
	});
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	initFormErrorMessageHandler();
};

export default init;
