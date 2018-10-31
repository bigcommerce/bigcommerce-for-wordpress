/**
 * @module Country/state component for dynamic field values
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { COUNTRIES_OBJ } from '../config/wp-settings';
import { stateSelectField } from '../templates/dynamic-state-select';
import { stateInputField } from '../templates/dynamic-state-input';


const el = {
	container: tools.getNodes('bigcommerce_new_account')[0],
};

const countryState = {
	countryWithStates: false,
};

/**
 * @function swapStateProvinceSelectTextField
 * @description If a country that has states is chosen, hide the text field and show a new select field.
 * @param stateControlContainer
 * @param countryHasStates
 * @param countryStates
 */
const swapStateProvinceSelectTextField = (stateControlContainer = '', countryHasStates = false, countryStates = '') => {
	const stateControl = tools.getNodes('bc-dynamic-state-control', false, stateControlContainer)[0];
	let newStateField;
	let fieldValue;

	if (countryState.currentCountry === countryState.initialCountryValue) {
		fieldValue = countryState.initialStateValue;
	}

	if (countryHasStates) {
		newStateField = stateSelectField(countryStates, countryState.stateFieldId, countryState.stateFieldName, countryState.initialStateValue);
	} else {
		newStateField = stateInputField(countryState.stateFieldId, countryState.stateFieldName, fieldValue);
	}

	stateControlContainer.removeChild(stateControl);
	stateControlContainer.insertAdjacentHTML('beforeend', newStateField);
};

/**
 * @function parseCountryObject
 * @description traverse the selected country object and determine if states are available. Process handlers if this is true.
 * @param selectedCountry
 * @param stateControlContainer
 */
const parseCountryObject = (selectedCountry = '', stateControlContainer = '') => {
	if (!selectedCountry && !stateControlContainer) {
		return;
	}

	const countryObj = COUNTRIES_OBJ.filter(item => item.country_iso2 === selectedCountry);
	const countryStates = countryObj[0].states;

	if (!countryStates) {
		countryState.countryWithStates = false;
	} else {
		countryState.countryWithStates = true;
	}

	swapStateProvinceSelectTextField(stateControlContainer, countryState.countryWithStates, countryStates);
};

/**
 * @function storeInitialFieldStates
 * @description stores current state field type and value in countryState object
 */
const storeInitialFieldStates = () => {
	const countryControl = tools.getNodes('bc-dynamic-country-select')[0];
	const stateControl = tools.getNodes('bc-dynamic-state-control')[0];
	const stateFieldType = stateControl.tagName.toLowerCase();

	if (stateFieldType === 'select') {
		countryState.initialStateValue = stateControl.options[stateControl.selectedIndex].value;
	} else {
		countryState.initialStateValue = stateControl.value;
	}

	countryState.stateFieldId = stateControl.id;
	countryState.stateFieldName = stateControl.name;
	countryState.initialCountryValue = countryControl.options[countryControl.selectedIndex].value;
};

/**
 * @function handleCountriesSelection
 * @description When a country is selected, setup the process for determining the form changes.
 * @param e
 */
const handleCountriesSelection = (e) => {
	const selectedCountry = e.delegateTarget.value;
	const form = tools.closest(e.delegateTarget, '.bc-settings-form--bigcommerce_new_account');
	const stateControlContainer = tools.getNodes('bc-dynamic-state-control', false, form)[0].parentNode;

	countryState.currentCountry = e.target.options[e.target.selectedIndex].value;
	parseCountryObject(selectedCountry, stateControlContainer);
};

/**
 * @function cacheElements
 * @description check for el.container rendered after page load
 */
const cacheElements = () => {
	el.container = tools.getNodes('bigcommerce_new_account')[0];
};

/**
 * @function bindEvents
 * @description bind all event handlers and listeners for addresses.
 */
const bindEvents = () => {
	delegate(el.container, '[data-js="bc-dynamic-country-select"]', 'change', handleCountriesSelection);
};

const init = () => {
	cacheElements();
	if (!el.container) {
		return;
	}

	bindEvents();
	storeInitialFieldStates();
};

export default init;
