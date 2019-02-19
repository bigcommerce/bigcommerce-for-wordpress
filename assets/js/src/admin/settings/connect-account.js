/**
 * @module Connect Account
 * @description Check for an account connection until we're successful.
 */

import request from 'superagent';
import _ from 'lodash';
import * as tools from '../../utils/tools';
import popup from '../../utils/dom/popup';
import { ACCOUNT_NONCE, ACCOUNT_ACTION } from '../config/wp-settings';
import { connectionError } from './templates';
import { I18N } from '../config/i18n';

const el = {
	accountAjax: tools.getNodes('account-pending-status')[0],
};

const state = {
	success: false,
	attempts: 0,
	maxAttempts: 100,
	excessiveAttempts: 10,
	popupOpen: false,
};

/**
 * @function excessiveAttemptsReached
 * @description when we've reached the state.excessiveAttempts threshold, print a message letting the user know we're still working on it.
 */
const excessiveAttemptsReached = () => {
	const element = document.createElement('div');

	el.connectionError.innerHTML = '';
	tools.addClass(element, 'bc-account-connection-response');
	element.textContent = I18N.messages.excessive_attempts;
	el.connectionError.appendChild(element);
};

/**
 * @function maxAttemptsReached
 * @description when we've reached state.maxAttempts, kill the polling process and print the error message.
 * @param data
 */
const maxAttemptsReached = (data) => {
	const element = document.createElement('div');
	const spinner = document.querySelector('.bc-connect-spinner');

	spinner.parentNode.removeChild(spinner);
	el.connectionError.innerHTML = '';
	tools.addClass(element, 'bc-account-connection-response');
	tools.addClass(element, 'bc-account-connection-response--error');
	element.innerHTML = connectionError(data);
	el.connectionError.appendChild(element);
};

/**
 * @function monitorWindow
 * @description Periodically checks if the popup from showPopup() has closed
 * @param win
 */
const monitorWindow = (win) => {
	if (win.closed !== false) {
		state.popupOpen = false;
		return;
	}

	state.popupOpen = true;
	_.delay(() => {
		monitorWindow(win);
	}, 100);
};

/**
 * @function showPopup
 * @description Opens a popup window to complete the oauth workflow
 * @param url
 */
const showPopup = (url) => {
	state.popupOpen = true; // it's not _really_ open, but close enough
	const newWindow = popup({
		url,
		center: true,
		specs: {
			width: 900,
			height: 550,
		},
	});

	// user has popups blocked, so provide a button to open it
	if (!newWindow || typeof newWindow === 'undefined') {
		const element = document.createElement('button');
		el.connectionError.innerHTML = '';
		tools.addClass(element, 'bc-account-connection-button');
		tools.addClass(element, 'bc-admin-btn');
		element.textContent = I18N.buttons.oauth_popup_trigger;
		el.connectionError.appendChild(element);
		element.addEventListener('click', () => {
			el.connectionError.removeChild(element);
			showPopup(url);
		});
		return;
	}
	monitorWindow(newWindow);
};

/**
 * @function pollAccountConnection
 * @description AJAX polling recursive function to check the status of the account connection on BigCommerce's end.
 */
const pollAccountConnection = () => {
	const url = el.accountAjax.dataset.url;

	if (state.popupOpen) {
		_.delay(() => {
			pollAccountConnection();
		}, 200);
		return;
	}

	if (state.attempts >= state.maxAttempts) {
		return;
	}

	// if the account message has been hidden (e.g., when the popup is open), bring it back
	tools.removeClass(el.accountMessage, 'hidden');

	request
		.get(url)
		.set('X-WP-Nonce', ACCOUNT_NONCE)
		.query({ _wpnonce: ACCOUNT_NONCE })
		.query({ action: ACCOUNT_ACTION })
		.timeout({
			response: 5000,
			deadline: 30000,
		})
		.end((err, res) => {
			state.attempts++;

			if (err) {
				console.error(err);
				_.delay(() => {
					pollAccountConnection();
				}, 5000);
				return;
			}

			// account is fully connected, we're ready to move on
			if (res.body.data.redirect) {
				window.location = res.body.data.redirect;
				return;
			}

			// we need to open up a popup for the user to login to BigCommerce and authorize the app
			if (res.body.data.popup) {
				state.attempts = 0; // we'll start over when the popup closes
				tools.addClass(el.accountMessage, 'hidden');
				showPopup(res.body.data.popup);
				_.delay(() => {
					pollAccountConnection();
				}, 5000);
				return;
			}

			// still waiting for the account to finish provisioning

			if (state.attempts >= state.excessiveAttempts) {
				excessiveAttemptsReached();
			}

			if (state.attempts >= state.maxAttempts) {
				maxAttemptsReached(res.body.data);
				return;
			}

			_.delay(() => {
				pollAccountConnection();
			}, 5000);
		});
};

const cacheElements = () => {
	el.accountMessage = tools.getNodes('.bc-welcome__pending-account-message', false, document, true)[0];
	el.connectionError = tools.getNodes('bc-welcome__account-connection-response')[0];
};

const init = () => {
	if (!el.accountAjax) {
		return;
	}

	cacheElements();
	pollAccountConnection();
};

export default init;
