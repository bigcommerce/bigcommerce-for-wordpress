/**
 * @module Shortcode Settings
 * @description Clearinghouse for shortcode settings page(s) scripts.
 */

import * as tools from '../../utils/tools';
import settings from './settings';

const el = {
	container: tools.getNodes('.bigcommerce_product_page_bigcommerce', false, document, true)[0],
};

const init = () => {
	if (!el.container) {
		return;
	}

	settings(el.container);
};

export default init;
