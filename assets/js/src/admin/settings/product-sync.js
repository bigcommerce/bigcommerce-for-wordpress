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
 * @param response
 * @param node
 * @param icon
 */
const importSuccess = (response, node, icon) => {
	state.syncCompleted = true;
	const messageWrapper = tools.getNodes('.bc-import-progress-bar__wrapper', false, document, true)[0];

	messageWrapper.innerHTML = importCloseButton;
	tools.removeClass(icon, 'icon-bc-sync');
	tools.addClass(icon, 'icon-bc-check');
	tools.addClass(el.container, 'bigcommerce-notice__import-status--success');
	node.textContent = response.data.products.status;
};

/**
 * @function importError
 * @description Handle the creation and display of the error message on failed imports.
 * @param node
 * @param icon
 */
const importError = (node, icon) => {
	tools.removeClass(icon, 'icon-bc-sync');
	tools.removeClass(icon, 'bc-icon');
	tools.addClass(icon, 'dashicons');
	tools.addClass(icon, 'dashicons-warning');
	tools.addClass(el.container, 'bigcommerce-notice__import-status--error');
	node.textContent = I18N.messages.sync_error;
};

/**
 * @function handleStatusMessage
 * @description display a message from the current step being processed by the cron job.
 * @param response
 */
const handleStatusMessage = (response = '') => {
	if (!response) {
		return;
	}

	const currentMessage = tools.getNodes('.bc-import-status-message', false, el.container, true)[0];
	const icon = tools.getNodes('.icon-bc-sync', false, el.container, true)[0];

	if (state.syncFailed) {
		importError(currentMessage, icon);
		return;
	}


	if (response.data.status === 'not_started') {
		if (response.success) {
			importSuccess(response, currentMessage, icon);
			return;
		}

		importError(currentMessage, icon);
		return;
	}

	currentMessage.textContent = response.data.message;
};

/**
 * @function updateProgressBar
 * @description use the current values returned from the bron job for total and completed products to move, update and animate the progress bar.
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
	if ((!total && !completed) || state.syncCompleted) {
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
 * @function removeImportErrorNotice
 * @description If an import error notice exists at the time a new sync is triggered, hide the previous error notice.
 */
const removeImportErrorNotice = () => {
	const notice = tools.getNodes('.bigcommerce-notice__import-status--error', false, document, true)[0];
	if (!notice) {
		return;
	}

	notice.parentNode.removeChild(notice);
};

/**
 * @function pollProductSyncWatcher
 * @description Function to recursively ping the ajax action to check the progress of the import cron job process.
 */
const pollProductSyncWatcher = () => {
	if (state.syncCompleted) {
		return;
	}

	state.isFetching = true;
	removeImportErrorNotice();

	wpAdminAjax({ action: IMPORT_PROGRESS_ACTION, _wpnonce: IMPORT_PROGRESS_NONCE })
		.timeout({
			response: 15000,  // Wait 15 seconds for the server to start sending,
			deadline: 60000, // but allow 1 minute for the file to finish loading.
		})
		.end((err, res) => {
			state.isFetching = false;

			if (err) {
				if (err.timeout) {
					pollProductSyncWatcher();
				} else {
					console.error(err);
					state.syncCompleted = true;
					state.syncFailed = true;
				}
			}

			const data = res.body.data;
			handleStatusMessage(res.body);

			switch (data.status) {
			case 'processing_queue':
				handleProgressBar(data.products.total, data.products.completed, data.products.status);
				break;
			case 'fetched_store':
			case 'fetching_store':
			case 'not_started':
				handleProgressBar(data.products.total, data.products.total, data.products.status);
				break;
			default:
				break;
			}
		});

	_.delay(() => {
		pollProductSyncWatcher();
	}, 2000); // Check every 2 seconds for a new cron update.
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
