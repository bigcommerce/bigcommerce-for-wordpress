/**
 * @module Product Modifiers
 * @description Handle special cases for product modifiers for entering data and validation.
 */

import delegate from 'delegate';
import _ from 'lodash';
import flatpickr from 'flatpickr';
import * as tools from '../../utils/tools';

const validateTextArea = (e) => {
	const maxRows = e.delegateTarget.dataset.maxrows;
	const currentValue = e.delegateTarget.value;
	const currentLineCount = currentValue.split(/\r*\n/).length;

	if (e.which === 13 && currentLineCount >= maxRows) {
		e.preventDefault();
		return false;
	}

	return true;
};

const handleModifierFields = (modifiers) => {
	tools.getNodes('.bc-product-form__control.bc-product-form__control--textarea', true, modifiers, true).forEach((field) => {
		delegate(field, 'textarea', 'keydown', validateTextArea);
	});

	tools.getNodes('.bc-product-form__control.bc-product-form__control--date', true, modifiers, true).forEach((field) => {
		const dateField = tools.getNodes('input[type="date"]', false, field, true)[0];
		const defaultDate = dateField.value;
		const minDate = dateField.getAttribute('min');
		const maxDate = dateField.getAttribute('max');

		const options = {
			defaultDate,
			minDate,
			maxDate,
		};

		flatpickr(dateField, options);
	});
};

/**
 * @function initOptionsPickers
 * @description Traverse the dom and find forms that have not been initialized. Add a unique ID and setup instanced containers for handling product form data.
 */
const initModifiers = () => {
	tools.getNodes('.bc-product-form__modifiers:not(.initialized)', true, document, true).forEach((modifiers) => {
		const productModifiersID = _.uniqueId('product-modifiers-');
		// "Initialize" the current form options.
		tools.addClass(modifiers, 'initialized');

		// Add the unique ID to the current options node for easily selecting the form parent associated with this product.
		modifiers.setAttribute('data-product-modifiers-id', productModifiersID);

		// On initialization, setup our form.
		handleModifierFields(modifiers);
	});
};

const init = (container) => {
	if (!container) {
		return;
	}

	initModifiers();
};

export default init;
