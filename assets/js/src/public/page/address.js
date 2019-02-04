/**
 * @module Address page and shortcode scripts.
 */

import _ from 'lodash';
import delegate from 'delegate';
import A11yDialog from 'mt-a11y-dialog';
import * as tools from '../../utils/tools';
import dynamicStateField from './dynamicStateField';
import { deleteConfirmation } from '../templates/address-delete';


const el = {
	container: tools.getNodes('bc-account-addresses')[0],
};

const instances = {
	dialogs: {},
};

/**
 * @function getOptions
 * @description Set standard A11yDialog options
 * @param dialogID
 * @returns {{appendTarget: string, trigger: string, bodyLock: boolean, effect: string, effectSpeed: number, effectEasing: string, overlayClasses: string, contentClasses: string, wrapperClasses: string, closeButtonClasses: string}}
 */
const getOptions = dialogID => ({
	appendTarget: '.bc-account-addresses__list',
	trigger: `[data-trigger="${dialogID}"]`,
	bodyLock: false,
	effect: 'fade',
	effectSpeed: 200,
	effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
	overlayClasses: 'bc-account-address-form__overlay',
	contentClasses: 'bc-account-address-form__content',
	wrapperClasses: 'bc-account-address-form__wrapper',
	closeButtonClasses: 'bc-account-address-form__close-button bc-icon icon-bc-cross u-bc-visual-hide',
});

/**
 * @function initHideDialog
 * @description Setup the currently active and rendered dialog box with a new .hide() trigger.
 * @param dialogEl
 * @param dialogID
 */
const initHideDialog = (dialogEl, dialogID) => {
	const cancelEdit = tools.getNodes('bc-account-address-form-cancel', false, dialogEl, false)[0];
	cancelEdit.setAttribute('data-dialogid', dialogID);
};

/**
 * @function hideDialog
 * @description Hide the current dialog when triggered.
 * @param e
 */
const hideDialog = (e) => {
	const dialogID = e.delegateTarget.dataset.dialogid;
	instances.dialogs[dialogID].hide();
};

/**
 * @function handleDeleteConfirmation
 * @description Handle the delete confirmation process on an address.
 * @param card
 * @param form
 */
const handleDeleteConfirmation = (card, form) => {
	const template = tools.getNodes('.bc-account-address__delete-confirmation', false, card, true)[0];
	tools.addClass(template, 'bc-confirmation-active');

	delegate(card, '[data-js="bc-confirm-address-deletion"]', 'click', () => {
		form.submit();
	});

	delegate(card, '[data-js="bc-confirm-address-cancel"]', 'click', () => {
		tools.removeClass(template, 'bc-confirmation-active');
	});
};

/**
 * @function displayDeleteConfirmation
 * @description Display the delete confirmation screen.
 * @param card
 */
const displayDeleteConfirmation = (card) => {
	card.insertAdjacentHTML('beforeend', deleteConfirmation);
};

/**
 * @function handleDeleteAddress
 * @description Present the user with confirm and cancel buttons when attempting to delete an address.
 * @param e
 */
const handleDeleteAddress = (e) => {
	e.preventDefault();

	const form = tools.closest(e.delegateTarget, '.bc-account-address__delete-form');
	const card = tools.closest(e.delegateTarget, 'li.bc-account-addresses__item');

	displayDeleteConfirmation(card);
	handleDeleteConfirmation(card, form);
};

/**
 * @function handleDialogWithErrors
 * @description display dialog with form errors and only allow save action
 * @param dialogID
 */
const handleDialogWithErrors = (dialogID) => {
	//remove transitions so display is instant
	const dialog = instances.dialogs[dialogID];
	dialog.options.effectSpeed = 0;
	el.addressList.style.transitionDuration = '0';

	//  Show the dialog
	dialog.show();
	tools.addClass(el.addressList, 'bc-account-address--form-active');

	// Delay the activation of the cancel button so we can ensure the dialog is rendered.
	_.delay(() => {
		initHideDialog(dialog.node, dialogID);
	}, 50);
};

/**
 * @function initDialogs
 * @description setup all the dialog boxes and triggers for each available address on the page.
 */
const initDialogs = () => {
	tools.getNodes('[data-js="bc-account-address-actions"]:not(.initialized)', true, el.container, true)
		.forEach((dialog) => {
			const dialogID = _.uniqueId('bc-account-address-form-dialog-');
			const trigger = tools.getChildren(dialog)[0];
			const target = tools.getChildren(dialog)[1];

			dialog.classList.add('initialized');
			trigger.setAttribute('data-content', dialogID);
			trigger.setAttribute('data-trigger', dialogID);
			target.setAttribute('data-js', dialogID);
			instances.dialogs[dialogID] = new A11yDialog(getOptions(dialogID));

			// open dialog if any forms contain error class
			if (target.textContent.includes('bc-form__control--error')) {
				handleDialogWithErrors(dialogID);
			}

			instances.dialogs[dialogID].on('render', () => {
				// On the first time this dialog is rendered, perform these actions.
				const alertSuccess = tools.getNodes('.bc-alert-group--success', false, el.container.parentElement, true)[0];

				// On successful update, remove the success message when the next dialog is rendered.
				// Note: Since this is a page refresh, no dialogs have been rendered yet if success occurs.
				if (alertSuccess) {
					alertSuccess.parentNode.removeChild(alertSuccess);
				}
			});

			instances.dialogs[dialogID].on('show', (dialogEl) => {
				// Every time a dialog is shown, perform these actions.
				tools.addClass(el.addressList, 'bc-account-address--form-active');

				_.delay(() => {
					initHideDialog(dialogEl, dialogID);
					dynamicStateField();
				}, 50);
			});

			instances.dialogs[dialogID].on('hide', () => {
				// Every time a dialog is closed, perform these actions.
				tools.removeClass(el.addressList, 'bc-account-address--form-active');
			});
		});
};

/**
 * @function cacheElements
 * @description elements to store if el.container exists.
 */
const cacheElements = () => {
	el.addressList = tools.getNodes('.bc-account-addresses__list', false, el.container, true)[0];
};

/**
 * @function bindEvents
 * @description bind all event handlers and listeners for addresses.
 */
const bindEvents = () => {
	delegate(el.container, '[data-js="bc-account-address-form-cancel"]', 'click', hideDialog);
	delegate(el.container, '[data-js="bc-account-address-delete"]', 'click', handleDeleteAddress);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	initDialogs();
	bindEvents();
};

export default init;
