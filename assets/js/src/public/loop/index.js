/**
 * @module Loop
 *
 * @description Clearinghouse for Loop/Archive scripts.
 */

import * as tools from '../../utils/tools';
import filters from './filters';

const el = {
	container: tools.getNodes('.post-type-archive-bigcommerce_product', false, document, true)[0],
};

const init = () => {
	if (!el.container) {
		return;
	}

	filters();
};

export default init;
