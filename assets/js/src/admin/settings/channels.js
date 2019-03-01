/**
 * @module Channels Settings
 * @description JS needed for Channels Settings section of BigCommerce Plugin Settings
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { channelConfirmation } from './templates';

const el = {
	container: tools.getNodes('.bc-settings-section--channel_select', false, document, true)[0],
};

const state = {
	canSubmit: false,
};

/**
 * @function handleSubmitButtonState
 * @description Enable or disable the submit button based on the current form state.
 */
const handleSubmitButtonState = () => {
	if (!state.canSubmit) {
		el.channelsSubmit.setAttribute('disabled', 'disabled');
		return;
	}

	el.channelsSubmit.removeAttribute('disabled');
};

/**
 * @function checkNewChannelValue
 * @description checks the length of the new channel field to make sure a value is present.
 */
const checkNewChannelValue = (e) => {
	const field = e ? e.delegateTarget : el.newChannelField;

	state.canSubmit = field.value.length > 0;
	handleSubmitButtonState();
};

/**
 * @function removeChannelConfirmation
 * @description If a confirmation message exists, remove it.
 */
const removeChannelConfirmation = () => {
	const confirmation = tools.getNodes('bc-channel-selection-confirmation', false, el.container)[0];

	if (!confirmation) {
		return;
	}

	confirmation.parentNode.removeChild(confirmation);
};

/**
 * @function showChannelConfirmation
 * @description if a channel is selected, print a message under the field notifying users of the outcome.
 * @param field
 * @param label
 */
const showChannelConfirmation = (field, label) => {
	const confirmationWrapper = document.createElement('div');

	removeChannelConfirmation();
	confirmationWrapper.innerHTML = channelConfirmation(label);
	field.parentNode.appendChild(confirmationWrapper);
	el.newChannel.style.display = 'none';
};

/**
 * @function handleChannelSelection
 * @description When a user changes the select field option, handle and run the necessary events.
 * @param e
 */
const handleChannelSelection = (e) => {
	const selectField = e ? e.delegateTarget : el.channelList;
	const selectionValue = e ? e.delegateTarget.value : el.channelList.value;
	const selectionLabel = selectField.options[selectField.selectedIndex].text;

	if (!selectionValue) {
		return;
	}

	el.newChannel.style.display = 'none';

	switch (selectionValue) {
	case 'create':
		checkNewChannelValue();
		removeChannelConfirmation();
		el.newChannel.style.display = 'table-row';
		el.newChannelField.focus();
		break;
	case '0':
		state.canSubmit = false;
		handleSubmitButtonState();
		removeChannelConfirmation();
		break;
	default:
		state.canSubmit = true;
		handleSubmitButtonState();
		showChannelConfirmation(selectField, selectionLabel);
	}
};

const cacheElements = () => {
	el.channelList = tools.getNodes('bc-settings__channel-select', false, el.container)[0];
	el.newChannel = tools.getNodes('.bc-create-channel-wrapper', false, el.container, true)[0];
	el.newChannelField = tools.getNodes('input[name="bigcommerce_new_channel_name"]', false, el.newChannel, true)[0];
	el.channelsSubmit = tools.getNodes('bc-settings-channel-submit', false, document)[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-settings__channel-select"]', 'change', handleChannelSelection);
	delegate(el.container, 'input[name="bigcommerce_new_channel_name"]', 'change', checkNewChannelValue);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
	handleChannelSelection();
};

export default init;
