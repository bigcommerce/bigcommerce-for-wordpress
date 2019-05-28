/**
 * @module Account Reset
 * @description Reset your account using the start over button.
 */

import _ from 'lodash';
import A11yDialog from 'mt-a11y-dialog';
import delegate from 'delegate';
import * as tools from '../.././utils/tools';

const el = {
	container: tools.getNodes('bc-welcome-start-over-trigger')[0],
};

const instances = {};

const dialogSettings = {
	appendTarget: '#wpbody',
	bodyLock: false,
	trigger: el.container,
	overlayClasses: 'bc-welcome__account-reset-overlay',
	wrapperClasses: 'bc-welcome__account-reset-wrapper',
	contentClasses: 'bc-welcome__account-reset-content',
	closeButtonClasses: 'bc-icon icon-bc-cross bc-welcome__account-reset-close-button',
};

/**
 * @function handleHideDialog
 * @description Allow other buttons to control the hiding of this dialog box.
 * @param event
 */
const handleHideDialog = (event) => {
	if (!event) {
		return;
	}

	instances.dialog.hide();
};

/**
 * @function triggerAccountReset
 * @description If the user confirms they want to start over, send them to the reset URL.
 * @param event
 */
const triggerAccountReset = (event) => {
	if (!event) {
		return;
	}

	window.location.href = event.delegateTarget.dataset.url;
};

/**
 * @function bindDialogEvents
 * @description after the dialog box renders, allow the buttons to trigger events.
 * @param dialogEl
 */
const bindDialogEvents = (dialogEl) => {
	delegate(dialogEl, '[data-js="bc-welcome-account-reset-cancel"]', 'click', handleHideDialog);
	delegate(dialogEl, '[data-js="bc-welcome-account-reset-confirm"]', 'click', triggerAccountReset);
};

/**
 * @function initResetDialog
 * @description Create a dialog box that allows the user to restart the account sign up process.
 */
const initResetDialog = () => {
	instances.dialog = new A11yDialog(dialogSettings);

	instances.dialog.on('render', (dialogEl) => {
		_.delay(() => bindDialogEvents(dialogEl), 50);
	});
};

const init = () => {
	if (!el.container) {
		return;
	}

	initResetDialog();
};

export default init;
