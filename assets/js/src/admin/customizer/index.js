/**
 * @module Customizer
 * @description Clearinghouse for loading all Theme Customizer scripts.
 */

import multiCheckboxes from './multiple-checkboxes';
import * as tools from '../../utils/tools';

const el = {
	customizer: tools.getNodes('#customize-controls', false, document, true)[0],
};

const init = () => {
	if (!el.customizer) {
		return;
	}

	multiCheckboxes(el.customizer);

	console.info('BigCommerce: Initialized Theme Customizer Scripts.');
};

export default init;
