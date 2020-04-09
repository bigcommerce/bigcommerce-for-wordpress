/**
 * @module Product Variants
 * @description Handle product form variant selections and update the form UI based on those selections.
 */

import _ from 'lodash';
import delegate from 'delegate';
import flatpickr from 'flatpickr';
import * as tools from 'utils/tools';
import queryToJson from 'utils/data/query-to-json';
import updateQueryVar from 'utils/data/update-query-var';
import { trigger } from 'utils/events';
import { PRODUCT_MESSAGES } from '../config/wp-settings';
import { productMessage } from '../templates/product-message';

const instances = {
	product: [],
	selections: [],
};

const state = {
	isValidOption: false,
	singleVariant: false,
	variantID: '',
	variantMessage: '',
	variantPrice: '',
	sku: '',
	variantImage: {
		url: '',
		template: '',
		zoom: '',
		srcset: '',
	},
};

const el = {
	singleWrapper: tools.getNodes('.bc-product-single', false, document, true)[0],
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
 * @function setSelectedVariantPrice
 * @description get the price of the current selected variant ID and replace the price element with it's
 *     formatted_price value.
 * @param wrapper
 */
const setSelectedVariantPrice = (wrapper = '') => {
	if (!wrapper || !state.variantPrice) {
		return;
	}

	const pricingWrapper = tools.getNodes('bc-cached-product-pricing', false, wrapper)[0];
	if (!pricingWrapper) {
		return;
	}
	const salePriceElement = pricingWrapper.querySelector('.bc-product__original-price');
	const priceElement = pricingWrapper.querySelector('.bc-product__price');

	if (salePriceElement) {
		salePriceElement.parentNode.removeChild(salePriceElement);
	}

	priceElement.textContent = state.variantPrice;
};

/**
 * @function showVariantImage
 * @description Shows the variant image if one is available from state.variantImage.url.
 * @param swiperInstance
 */
const showVariantImage = (swiperInstance) => {
	const slide = document.createElement('div');
	tools.addClass(slide, 'swiper-slide');
	tools.addClass(slide, 'bc-product-gallery__image-slide');
	tools.addClass(slide, 'bc-product-gallery__image-variant');
	slide.insertAdjacentHTML('beforeend', state.variantImage.template);

	const image = tools.getNodes('bc-variant-image', false, slide)[0];
	image.setAttribute('src', state.variantImage.url);
	image.setAttribute('alt', state.sku);
	image.setAttribute('data-zoom', state.variantImage.zoom);
	image.setAttribute('srcset', state.variantImage.srcset);

	swiperInstance.appendSlide(slide);
	swiperInstance.slideTo(swiperInstance.slides.length);

	_.delay(() => {
		trigger({ event: 'bigcommerce/init_slide_zoom', data: { container: slide.querySelector('img') }, native: false });
	}, 100);
};

/**
 * @function removeVariantImage
 * @description Hide the active variant image.
 * @param swiperInstance
 */
const removeVariantImage = (swiperInstance) => {
	let slideIndex = '';

	Object.entries(swiperInstance.slides).forEach(([key, slide]) => {
		if (key === 'length') {
			return;
		}

		if (tools.hasClass(slide, 'bc-product-gallery__image-variant')) {
			slideIndex = key;
		}
	});

	if (!slideIndex) {
		return;
	}

	swiperInstance.removeSlide(slideIndex);
	swiperInstance.slideTo(1);
};

/**
 * @function showHideVariantImage
 * @description Hides any active variant image and then displays a new one if it is available.
 * @param e
 * @param wrapper
 */
const showHideVariantImage = (e, wrapper = '') => {
	if (!e && !wrapper) {
		return;
	}

	const currentWrapper = e ? tools.closest(e.detail.currentGallery.el, '[data-js="bc-product-data-wrapper"]') : wrapper;
	const variantContainer = tools.getNodes('bc-product-variant-image', false, currentWrapper)[0];

	// Check that the proper variant image container is present in the DOM.
	if (!variantContainer) {
		return;
	}

	state.variantImage.template = variantContainer.innerHTML;
	const swiperInstance = tools.getNodes('bc-gallery-container', false, currentWrapper)[0].swiper;

	// hide the image after each variant request.
	removeVariantImage(swiperInstance);

	// If there is a variant image, show it with a short delay for animation purposes.
	if (state.variantImage.url) {
		showVariantImage(swiperInstance);
	}
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

	// Set the price and ID regardless of state.
	state.variantPrice = product.formatted_price;
	state.variantID = product.variant_id;
	state.sku = product.sku;

	// Case: product variant has a variant image.
	if (product.image.url.length > 0) {
		state.variantImage.url = product.image.url;
		state.variantImage.zoom = !_.isEmpty(product.zoom.url) ? product.zoom.url : '';
		state.variantImage.srcset = !_.isEmpty(product.image.srcset) ? product.image.srcset : '';
	}

	// Case: Current variant choice has inventory and is not disabled.
	if ((product.inventory > 0 || product.inventory === -1) && !product.disabled) {
		state.isValidOption = true;
		state.variantMessage = '';
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
		state.isValidOption = variants[0].inventory !== 0;
		state.singleVariant = true;
		state.variantID = variants[0].variant_id;
		state.sku = variants[0].sku;
		return;
	}

	// Try to match the selections to the option_ids in a variant.
	const variantIndex = _.findIndex(variants, variant => _.isEmpty(_.difference(variant.option_ids, choices)));

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
 * @function setProductURLParameter
 * @description Set and/or updates the variant_id query param in the url.
 */
const setProductURLParameter = () => {
	if (!state.variantID || !state.sku || !el.singleWrapper || state.singleVariant) {
		return;
	}

	window.history.replaceState(null, null, updateQueryVar('variant_id'));
	window.history.replaceState(null, null, updateQueryVar('sku', state.sku));
};

/**
 * @function validateTextArea
 * @description Listen for key presses and validate that the text meets the textarea's restrictions.
 * @param e
 */
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

/**
 * @function handleModifierFields
 * @description If there are fields present which allow manual user input (i.e. text, date, textarea A.K.A. Modifiers),
 *     allow for additional validation beyond HTML5 and overrides.
 * @param options
 */
const handleModifierFields = (options) => {
	delegate(options, '.bc-product-form__control.bc-product-form__control--textarea textarea', 'keydown', validateTextArea);

	tools.getNodes('.bc-product-form__control.bc-product-form__control--date', true, options, true).forEach((field) => {
		const dateField = tools.getNodes('input[type="date"]', false, field, true)[0];
		const defaultDate = dateField.value;
		const minDate = dateField.getAttribute('min');
		const maxDate = dateField.getAttribute('max');

		const fpOptions = {
			allowInput: false,
			altInput: true,
			altFormat: 'F j, Y',
			defaultDate,
			minDate,
			maxDate,
			static: true,
		};

		flatpickr(dateField, fpOptions);
	});
};

/**
 * @function handleSelections
 * @description On load or on selection change, determine which product form we are in and run all main functions.
 * @param e - a delegate event from a click.
 * @param node - a specific DOM node to use for options.
 */
const handleSelections = (e, node = '') => {
	const optionsContainer = e ? tools.closest(e.delegateTarget, '[data-js="product-options"]') : node;

	if (!optionsContainer) {
		return;
	}

	state.variantMessage = '';
	state.variantID = '';
	state.sku = '';
	state.variantPrice = '';
	state.singleVariant = false;
	state.variantImage.url = '';
	state.variantImage.zoom = '';

	const formWrapper = tools.closest(optionsContainer, '.bc-product-form');
	const productID = optionsContainer.dataset.productId;
	const submitButton = tools.getNodes('.bc-btn--form-submit', false, formWrapper, true)[0];

	let metaWrapper = tools.closest(optionsContainer, '[data-js="bc-product-data-wrapper"]');
	if (!metaWrapper) {
		metaWrapper = tools.closest(optionsContainer, '[data-wrapper="bc-product-data-wrapper"]');
	}

	buildSelectionArray(instances.selections[productID], optionsContainer);
	parseVariants(instances.product[productID], instances.selections[productID]);
	setProductURLParameter();
	setVariantIDHiddenField(formWrapper);
	setSelectedVariantPrice(metaWrapper);
	showHideVariantImage(null, metaWrapper);
	setButtonState(submitButton);
	handleAlertMessage(formWrapper);
};

/**
 * @function handleOptionClicks
 * @description Click/change event listener for form fields on each product. Runs our main handleSelections function on
 *     the event.
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
 * @function handleProductQueryParam
 * @description Creates an added layer of variant checking to ensure that a URL with a variant_id param is set properly.
 *
 * @param options
 */
const handleProductQueryParam = (options = []) => {
	// Assumes this is the PDP single page.
	const variantID = queryToJson().variant_id;
	const sku = queryToJson().sku;
	if ((!variantID && !sku) || !el.singleWrapper) {
		handleSelections(null, options);
		return;
	}

	const productOptions = tools.getNodes('product-options', false, el.singleWrapper)[0];
	const formWrapper = el.singleWrapper.querySelector('.bc-product-form');

	tools.addClass(formWrapper, 'bc-product__is-setting-options');
	handleSelections(null, productOptions);
	_.delay(() => tools.removeClass(formWrapper, 'bc-product__is-setting-options'), 500);
};

/**
 * @function initializeUniqueFieldIDs
 * @description Add a UID to each field and label set for option in order to avoid collisions with form control.
 * @param options
 * @param productVariantsID
 */
const initializeUniqueFieldIDs = (options = [], productVariantsID = '') => {
	tools.getNodes('product-form-option', true, options).forEach((option) => {
		const fieldType = option.dataset.field;

		// Set a UID for all labels
		tools.getNodes('label', true, option, true).forEach((label) => {
			const labelFor = `${label.getAttribute('for')}[${productVariantsID}]`;
			label.setAttribute('for', labelFor);
		});

		// Set the same UID for each radio input.
		if (fieldType === 'product-form-option-radio') {
			tools.getNodes('input[type=radio]', true, option, true).forEach((radio) => {
				const fieldID = `${radio.getAttribute('id')}[${productVariantsID}]`;
				radio.setAttribute('id', fieldID);
			});
		}

		// Set the same UID for each select field.
		if (fieldType === 'product-form-option-select') {
			tools.getNodes('select', true, option, true).forEach((select) => {
				const fieldID = `${select.getAttribute('id')}[${productVariantsID}]`;
				select.setAttribute('id', fieldID);
			});
		}
	});
};

/**
 * @function initOptionsPickers
 * @description Traverse the dom and find forms that have not been initialized. Add a unique ID and setup instanced
 *     containers for handling product form data.
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
		initializeUniqueFieldIDs(options, productVariantsID);
		tools.addClass(options, 'initialized');

		// Add the unique ID to the current options node for easily selecting the form parent associated with this product.
		options.setAttribute('data-product-id', productVariantsID);

		// On initialization, setup our form.
		handleOptionClicks(options);
		handleModifierFields(options);
		handleProductQueryParam(options);
	});
};

const init = (container) => {
	if (!container) {
		return;
	}

	initOptionsPickers();
};

export default init;
