/**
 * @module Product Sync Import Progress
 */

import _ from 'lodash';
import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { wpAdminAjax } from '../../utils/ajax';
import { I18N } from '../config/i18n';
import { IMPORT_PROGRESS_ACTION, IMPORT_PROGRESS_NONCE } from '../config/wp-settings';
import { importProgress, importCloseButton } from '../templates/import-progress';

const el = {
	container: tools.getNodes('bc-import-progress-status')[0],
};

const state = {
	isFetching: false,
	syncCompleted: false,
	syncError: false,
	pollingDelay: 1000,
};

/**
 * @function dismissImportNotice
 * @description Remove the import notice from the DOM
 */
const dismissImportNotice = () => {
	el.container.parentNode.removeChild(el.container);
};

/**
 * @function importSuccess
 * @description Handle the creation and display of the successful import message.
 * @param node
 * @param icon
 * @param response
 */
const importSuccess = (node, icon, response = '') => {
	let messageWrapper = tools.getNodes('.bc-import-progress-bar__wrapper', false, document, true)[0];

	if (!messageWrapper) {
		messageWrapper = document.createElement('div');
		tools.addClass(messageWrapper, 'bc-import-progress-bar__wrapper');
		el.container.appendChild(messageWrapper);
	}

	messageWrapper.innerHTML = importCloseButton;
	tools.removeClass(icon, 'icon-bc-sync');
	tools.addClass(icon, 'icon-bc-check');
	tools.addClass(el.container, 'bigcommerce-notice__import-status--success');
	node.textContent = response;
};

/**
 * @function importInProgress
 * @description Handle the display of the ongoing import messages.
 * @param node
 * @param icon
 * @param response
 */
const importInProgress = (node, icon, response = '') => {
	tools.addClass(icon, 'icon-bc-sync');
	tools.addClass(icon, 'bc-icon');
	tools.removeClass(icon, 'dashicons');
	tools.removeClass(icon, 'dashicons-warning');
	tools.removeClass(el.container, 'bigcommerce-notice__import-status--error');
	node.textContent = response;
};

/**
 * @function importError
 * @description Handle the creation and display of the error message on failed imports.
 * @param node
 * @param icon
 * @param error
 */
const importError = (node, icon, error = '') => {
	const errorMessage = error.length > 0 ? error : I18N.messages.sync.error;

	tools.removeClass(icon, 'icon-bc-sync');
	tools.removeClass(icon, 'bc-icon');
	tools.addClass(icon, 'dashicons');
	tools.addClass(icon, 'dashicons-warning');
	tools.addClass(el.container, 'bigcommerce-notice__import-status--error');
	node.textContent = errorMessage;
};

/**
 * @function handleStatusMessage
 * @description display a message from the current step being processed by the cron job.
 * @param response
 */
const handleStatusMessage = (response = '') => {
	const currentMessage = tools.getNodes('.bc-import-status-message', false, el.container, true)[0];
	const icon = tools.getNodes('bc-import-status-icon', false, el.container)[0];

	if (state.syncError) {
		// We have an error and need to create an error message.
		importError(currentMessage, icon, response);
		return;
	}

	if (state.syncCompleted) {
		// We have a successfully completed sync and need to create a success message.
		importSuccess(currentMessage, icon, response);
		return;
	}

	// We're still processing the sync procedure and need to print messages regarding the current steps.
	importInProgress(currentMessage, icon, response);
};

/**
 * @function updateProgressBar
 * @description use the current values returned from the bron job for total and completed products to move, update and
 *     animate the progress bar.
 * @param progressBar
 * @param status
 * @param position
 * @param percent
 */
const updateProgressBar = (progressBar, status, position, percent) => {
	const currentCount = progressBar.querySelector('.bc-import-progress-count');
	const currentProgress = progressBar.querySelector('.bc-import-progress-bar__mask');
	const currentPercent = progressBar.querySelector('.bc-import-progress-bar__percent');

	currentCount.textContent = status;
	currentProgress.style.left = `${position}%`;
	currentPercent.textContent = `${percent}%`;
};


/**
 * @function handleProgressBar
 * @description get the current progress bar status and either inject it or update it with the current data.
 * @param total
 * @param completed
 * @param status
 */
const handleProgressBar = (total = '', completed = '', status = '') => {
	if ((!total && !completed)) {
		return;
	}

	let progressBar;
	const percent = ((parseInt(completed, 10) / parseInt(total, 10)) * 100).toFixed();
	const position = (percent - 100).toFixed();

	progressBar = tools.getNodes('.bc-import-progress-bar__wrapper', false, document, true)[0];

	if (progressBar) {
		updateProgressBar(progressBar, status, position, percent);
		return;
	}

	progressBar = document.createElement('div');
	tools.addClass(progressBar, 'bc-import-progress-bar__wrapper');
	progressBar.innerHTML = importProgress(status, position, percent);
	el.container.appendChild(progressBar);
};

/**
 * @function pollProductSyncWatcher
 * @description Function to recursively ping the ajax action to check the progress of the import cron job process.
 */
const pollProductSyncWatcher = () => {
	if (state.syncCompleted) {
		// The sync process had completed and should not be run again.
		return;
	}

	// Begin the sync polling AJAX process.
	state.isFetching = true;

	// Query the admin ajax SuperAgent call to the product sync action.
	wpAdminAjax({ action: IMPORT_PROGRESS_ACTION, _wpnonce: IMPORT_PROGRESS_NONCE })
		.timeout({
			response: 15000,  // Wait 15 seconds for the server to start sending,
			deadline: 60000, // but allow 1 minute for the file to finish loading.
		})
		.end((err, res) => {
			state.isFetching = false;

			if (err) {
				// We have an error with the AJAX call and will handle the status codes.
				state.syncError = true;
				const errCode = parseFloat(err.status);

				if (err.timeout) {
					state.syncCompleted = false;
					handleStatusMessage(I18N.messages.sync.timeout);
				} else if (errCode >= 500) {
					const errorMessage = err.response.body ? err.response.body.data.message : I18N.messages.sync.server_error;
					state.syncCompleted = false;
					handleStatusMessage(errorMessage);
				} else if (errCode >= 400 && errCode < 500) {
					state.syncCompleted = true;
					handleStatusMessage(I18N.messages.sync.unauthorized);
				} else {
					state.syncCompleted = true;
					handleStatusMessage(I18N.messages.sync.error);
				}
			} else if (!res.body) {
				// Here we have a 200 response from the server but an empty AJAX JSON response.
				state.syncCompleted = false;
				state.syncError = true;
				handleStatusMessage(I18N.messages.sync.server_error);
			} else {
				// Here we have good response from the AJAX call and handle the data accordingly.
				state.syncError = false;
				// Handle the messaging for all steps.
				handleStatusMessage(res.body.data.message);

				// Once we reach the products step, handle the progress bar and messaging.
				const data = res.body.data;
				if (data.status === 'processing_queue') {
					// Still working on the products queue.
					state.syncCompleted = false;
					handleProgressBar(data.products.total, data.products.completed, data.products.status);
				} else if (data.status === 'not_started') {
					// The import is done.
					state.syncCompleted = true;
					if (data.previous !== 'failed') {
						// The sync has not failed and still contains a status response.
						handleStatusMessage(data.products.status);
					} else {
						// The sync has failed.
						state.syncError = true;
						handleStatusMessage(I18N.messages.sync.error);
					}
				}
			}

			// Set the delay for the next call based on syncError status.
			state.pollingDelay = state.syncError ? 20000 : 1000;

			_.delay(() => {
				pollProductSyncWatcher();
			}, state.pollingDelay); // Check after 1 second for a new cron update.
		});
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-import-close-notification"]', 'click', dismissImportNotice);
};

const init = () => {
	if (!el.container) {
		return;
	}

	pollProductSyncWatcher();
	bindEvents();
};

export default init;
