/**
 * @module Product Variants
 * @description Handle product form variant selections and update the form UI based on those selections.
 */

import _ from 'lodash';
import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { PRODUCT_MESSAGES } from '../config/wp-settings';
import { productMessage } from '../templates/product-message';

const instances = {
	product: [],
	selections: [],
};

const state = {
	isValidOption: false,
	variantID: '',
	variantMessage: '',
};

/**
 * @function disableActionButton
 * @description Disable the product form submit button.
 * @param button - Button node of the current form.
 */
const disableActionButton = (button) => {
	button.setAttribute('disabled', 'disabled');
};

/**
 * @function enableActionButton
 * @description Enable the product form submit button.
 * @param button - Button node of the current form.
 */
const enableActionButton = (button) => {
	button.removeAttribute('disabled');
};

/**
 * @function setButtonState
 * @description Toggle the state of the current form button based on chosen options.
 * @param button - Button node of the current form.
 */
const setButtonState = (button) => {
	if (state.isValidOption) {
		enableActionButton(button);
		return;
	}

	disableActionButton(button);
};

/**
 * @function setVariantIDHiddenField
 * @description If this is a valid variant, set its ID to the value of the form's hidden variant_id field.
 * @param formWrapper - Parent form node.
 */
const setVariantIDHiddenField = (formWrapper) => {
	const variantField = tools.getNodes('.variant_id', false, formWrapper, true)[0];

	if (!state.variantID) {
		variantField.value = '';
		return;
	}

	variantField.value = state.variantID;
};

/**
 * @function handleAlertMessage
 * @description Add or remove a variant message from the current product form.
 * @param formWrapper - Parent form node.
 */
const handleAlertMessage = (formWrapper = '') => {
	const container = tools.getNodes('bc-product-message', false, formWrapper)[0];
	const message = tools.getNodes('.bc-alert', false, container, true)[0];
	if (message) {
		message.parentNode.removeChild(message);
	}

	if (state.variantMessage.length <= 0) {
		return;
	}

	container.insertAdjacentHTML('beforeend', productMessage(state.variantMessage));
};

/**
 * @function handleSelectedVariant
 * @description Takes the current variant and handles status and messaging.
 * @param product
 */
const handleSelectedVariant = (product = {}) => {
	if (!product) {
		return;
	}

	// Case: Current variant choice has inventory and is not disabled.
	if ((product.inventory > 0 || product.inventory === -1) && !product.disabled) {
		state.isValidOption = true;
		state.variantMessage = '';
		state.variantID = product.variant_id;
		return;
	}

	// Case: Current variant is disabled.
	if (product.disabled) {
		state.isValidOption = false;
		state.variantMessage = product.disabled_message;
		return;
	}

	// Case: Current variant is out of stock.
	if (product.inventory === 0) {
		state.isValidOption = false;
		state.variantMessage = PRODUCT_MESSAGES.not_available;
		return;
	}

	// Case: We're assuming there are no issues with the current selections and the form action can be used.
	state.isValidOption = true;
	state.variantID = product.variant_id;
	state.variantMessage = '';
};

/**
 * @function parseVariants
 * @description Check to see if the current selections match a variant and handle the variant status within the form.
 * @param variants The current products' full variants object
 * @param choices The current products' variant choices array
 */
const parseVariants = (variants, choices) => {
	// Case: This is a product without variants.
	if (variants.length === 1) {
		state.isValidOption = true;
		state.variantID = variants[0].variant_id;
		return;
	}

	// Try to match the selections to the option_ids in a variant.
	const variantIndex = _.findIndex(variants, variant => _.isEmpty(_.difference(variant.option_ids, choices)) && _.isEmpty(_.difference(choices, variant.option_ids)));

	// Case: The current selection(s) do not match any product variants.
	if (variantIndex === -1) {
		state.isValidOption = false;
		return;
	}

	handleSelectedVariant(variants[variantIndex]);
};

/**
 * @function buildSelectionArray
 * @description On load or on selection change, build an array of variant IDs used to check for product matches.
 * @param selectionArray
 * @param optionsContainer
 */
const buildSelectionArray = (selectionArray, optionsContainer) => {
	// Reset the current array.
	selectionArray.length = 0;

	let selection = '';
	tools.getNodes('product-form-option', true, optionsContainer).forEach((field) => {
		const fieldType = field.dataset.field;

		if (fieldType === 'product-form-option-radio') {
			selection = tools.getNodes('input:checked', false, field, true)[0];
		}

		if (fieldType === 'product-form-option-select') {
			selection = tools.getNodes('select', false, field, true)[0];
		}

		if (!selection) {
			return;
		}

		selectionArray.push(parseInt(selection.value, 10));
	});
};

/**
 * @function handleSelections
 * @description On load or on selection change, determine which product form we are in and run all main functions.
 * @param e - a delegate event from a click or an options node.
 */
const handleSelections = (e = '') => {
	let optionsContainer = '';
	state.variantMessage = '';
	state.variantID = '';

	if (e.delegateTarget) {
		optionsContainer = tools.closest(e.delegateTarget, '[data-js="product-options"]');
	} else {
		optionsContainer = e;
	}

	const formWrapper = tools.closest(optionsContainer, '.bc-product-form');
	const productID = optionsContainer.dataset.productId;
	const submitButton = tools.getNodes('.bc-btn--form-submit', false, formWrapper, true)[0];

	buildSelectionArray(instances.selections[productID], optionsContainer);
	parseVariants(instances.product[productID], instances.selections[productID]);
	setVariantIDHiddenField(formWrapper);
	setButtonState(submitButton);
	handleAlertMessage(formWrapper);
};

/**
 * @function handleOptionClicks
 * @description Click/change event listener for form fields on each product. Runs our main handleSelections function on the event.
 * @param options - the current .initialized form options node.
 */
const handleOptionClicks = (options = '') => {
	tools.getNodes('product-form-option', true, options).forEach((option) => {
		const fieldType = option.dataset.field;

		if (fieldType === 'product-form-option-radio') {
			delegate(option, 'input[type=radio]', 'click', handleSelections);
		}

		if (fieldType === 'product-form-option-select') {
			delegate(option, 'select', 'change', handleSelections);
		}
	});
};

/**
 * @function initOptionsPickers
 * @description Traverse the dom and find forms that have not been initialized. Add a unique ID and setup instanced containers for handling product form data.
 */
const initOptionsPickers = () => {
	let variantsObj;
	tools.getNodes('.bc-product-form__options:not(.initialized)', true, document, true).forEach((options) => {
		const productVariantsID = _.uniqueId('product-');
		const variants = tools.getNodes('product-variants-object', false, options)[0];

		// Setup this products' variants obj.
		variantsObj = JSON.parse(variants.dataset.variants);

		// Assign the obj to its local instanced product object.
		instances.product[productVariantsID] = variantsObj;

		// Setup a blank instanced selections array for the current product.
		instances.selections[productVariantsID] = [];

		// "Initialize" the current form options.
		tools.addClass(options, 'initialized');

		// Add the unique ID to the current options node for easily selecting the form parent associated with this product.
		options.setAttribute('data-product-id', productVariantsID);

		// On initialization, setup our form.
		handleOptionClicks(options);
		handleSelections(options);
	});
};

const init = (container) => {
	if (!container) {
		return;
	}

	initOptionsPickers();
};

export default init;
