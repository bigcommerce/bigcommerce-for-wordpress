/**
 *
 */

import * as tools from 'utils/tools';
import _ from 'lodash';
import A11yDialog from 'mt-a11y-dialog';
import delegate from 'delegate';

const el = {
	container: tools.getNodes('bc-manage-wish-list')[0],
};

const instances = {
	dialogs: {},
};

const getOptions = dialogID => ({
	appendTarget: 'body',
	trigger: `[data-trigger="${dialogID}"]`,
	bodyLock: true,
	effect: 'fade',
	effectSpeed: 200,
	effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
	overlayClasses: 'bc-wish-list-dialog__overlay',
	contentClasses: 'bc-wish-list-dialog-content-wrapper',
	wrapperClasses: 'bc-wish-list-dialog__wrapper',
	closeButtonClasses: 'bc-product-quick-view__close-button bc-icon icon-bc-cross',
});

/**
 * @function initHideDialog
 * @description Setup the currently active and rendered dialog box with a new .hide() trigger.
 * @param dialogEl
 * @param dialogID
 */
const initHideDialog = (dialogEl, dialogID) => {
	const cancelEdit = tools.getNodes('bc-wish-list-dialog-close', false, dialogEl, false)[0];
	if (!cancelEdit) {
		return;
	}

	cancelEdit.setAttribute('data-dialogid', dialogID);
};

/**
 * @function hideDialog
 * @description Hide the current dialog when triggered.bc-create-wish-list-form--new
 * @param e
 */
const hideDialog = (e) => {
	const dialogID = e.delegateTarget.dataset.dialogid;
	instances.dialogs[dialogID].hide();
};

const initDialogs = () => {
	tools.getNodes('[data-js="bc-manage-wish-list"]:not(.initialized)', true, document, true).forEach((dialog) => {
		tools.getNodes('bc-wish-list-dialog-trigger', false, dialog).forEach((dialogTrigger) => {
			const dialogID = dialogTrigger.dataset.content;
			const target = tools.getNodes(dialogID, false, dialog)[0];

			if (!dialogTrigger || !target) {
				return;
			}

			dialog.classList.add('initialized');
			dialog.setAttribute('dialogid', dialogID);
			instances.dialogs[dialogID] = new A11yDialog(getOptions(dialogID));

			instances.dialogs[dialogID].on('hide', (elem) => {
				const editNameField = tools.getNodes('.bc-wish-list-name-field', false, elem, true)[0];

				if (!editNameField) {
					return;
				}

				editNameField.value = editNameField.dataset.defaultValue;
			});

			instances.dialogs[dialogID].on('render', (elem) => {
				_.delay(() => initHideDialog(elem, dialogID), 50);
			});
		});
	});
};

const bindEvents = () => {
	delegate('[data-js="bc-wish-list-dialog-close"]', 'click', hideDialog);
};

const init = () => {
	if (!el.container) {
		return;
	}

	initDialogs();
	bindEvents();
};

export default init;
