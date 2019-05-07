/**
 * @module Multi Channel
 * @description Multi channel support scripts for the BC Admin screen.
 */

import delegate from 'delegate';
import * as tools from 'utils/tools';

const el = {
	container: tools.getNodes('.bc-settings-section--channel', false, document, true)[0],
};

const activateAction = (e) => {
	const actionButton = e.delegateTarget;
	const actionType = actionButton.dataset.actionType;

	switch (actionType) {
	case 'create':
	case 'rename': {
		e.preventDefault();
		const fieldRow = tools.closest(actionButton, '[data-js="bc-channel-row"]');
		const actionFieldRow = tools.getNodes('bc-channel-action', false, fieldRow)[0];
		const actionField = tools.getNodes('bigcommerce-channel-action-input', false, actionFieldRow)[0];

		tools.addClass(actionButton, 'disabled');
		actionFieldRow.style.display = 'block';
		actionField.focus();
		break;
	}
	default:
		break;
	}
};

const cancelAction = (e) => {
	e.preventDefault();
	const cancelButton = e.delegateTarget;
	const fieldRow = tools.closest(cancelButton, '[data-js="bc-channel-row"]');
	const actionButton = tools.getNodes('bc-channel-show-action', false, fieldRow)[0];
	const actionFieldRow = tools.getNodes('bc-channel-action', false, fieldRow)[0];
	const actionField = tools.getNodes('bigcommerce-channel-action-input', false, actionFieldRow)[0];

	actionFieldRow.style.display = 'none';
	actionField.value = cancelButton.dataset.channelName ? cancelButton.dataset.channelName : '';
	tools.removeClass(actionButton, 'disabled');
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-channel-show-action"]', 'click', activateAction);
	delegate(el.container, '[data-js="bc-channel-cancel-action"]', 'click', cancelAction);
};

const init = () => {
	if (!el.container) {
		return;
	}

	bindEvents();
};

export default init;
