/**
 * @module Customizer
 * @description Clearinghouse for loading all Theme Customizer scripts.
 */

import multiCheckboxes from './multiple-checkboxes';
import * as tools from '../../utils/tools';

const init = () => {
	/** Note: make sure to look for element after "DOM ready" */
	const customizer = tools.getNodes('#customize-controls', false, document, true)[0];

	if (!customizer) {
		return;
	}

	multiCheckboxes(customizer);

	console.info('BigCommerce: Initialized Theme Customizer Scripts.');
};

export default init;
