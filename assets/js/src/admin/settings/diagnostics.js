/**
 * @module Toggle Sections
 * @description Settings page toggle sections scripts.
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import { wpAdminAjax } from '../../utils/ajax';
import { bigCommerceDiagnostics, bigCommerceDiagnosticsRequestError } from '../templates/diagnostics';
import { DIAGNOSTICS_ACTION, DIAGNOSTICS_NONCE } from '../config/wp-settings';

const el = {
	container: tools.getNodes('.bc-settings-form', false, document, true)[0],
};

const state = {
	diagnosticsSet: false,
	isFetching: false,
};

/**
 * @function getPluginDiagnostics
 * @description get and display plugin diagnostics information
 */

const getPluginDiagnostics = (target) => {
	const targetCell = target.delegateTarget ? tools.closest(target.delegateTarget, 'td') : target.querySelector('td');
	const loader = tools.getNodes('.bc-admin-diagnostics-loader', false, targetCell, true)[0];
	const activeWrapper = tools.getNodes('.bc-settings-diagnostics-wrapper', false, targetCell, true)[0];
	const getDiagnosticsWrapper = tools.closest(target.delegateTarget, '.bc-diagnostics-data');

	state.isFetching = true;
	loader.classList.add('is-active');

	if (activeWrapper) {
		activeWrapper.parentNode.removeChild(activeWrapper);
	}

	wpAdminAjax({ action: DIAGNOSTICS_ACTION, _wpnonce: DIAGNOSTICS_NONCE })
		.end((err, res) => {
			const wrapper = document.createElement('div');
			wrapper.classList.add('bc-settings-diagnostics-wrapper');

			state.isFetching = false;
			loader.classList.remove('is-active');

			if (err || !res) {
				console.error(err);
				state.diagnosticsSet = false;
				wrapper.innerHTML = bigCommerceDiagnosticsRequestError;
				targetCell.appendChild(wrapper);
				return;
			}

			if (res.body.success === false) {
				state.diagnosticsSet = false;
				wrapper.innerHTML = bigCommerceDiagnosticsRequestError;
				targetCell.appendChild(wrapper);
				return;
			}

			state.diagnosticsSet = true;
			getDiagnosticsWrapper.parentNode.removeChild(getDiagnosticsWrapper);
			wrapper.innerHTML = bigCommerceDiagnostics(res.body);
			targetCell.appendChild(wrapper);
		});
};

const cacheElements = () => {
	el.diagnosticsPanel = tools.getNodes('.bc-settings-section--bigcommerce_diagnostics', false, el.container, true)[0];
};

/**
 * @function bindEvents
 * @description bind all event listeners to this function.
 */
const bindEvents = () => {
	delegate(el.container, '[data-js="bc-admin-get-diagnostics"]', 'click', getPluginDiagnostics);
};

const init = () => {
	if (!el.container) {
		return;
	}

	cacheElements();
	bindEvents();
};

export default init;
