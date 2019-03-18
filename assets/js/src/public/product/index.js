/**
 * @module Product Variants.
 */

import * as tools from 'utils/tools';
import variants from './variants';
import reviews from './reviews';
import pricing from './pricing';

const el = {
	container: tools.getNodes('bc-product-single', false, document)[0],
};

const init = () => {
	variants(el.container);
	reviews(el.container);
	pricing();
};

export default init;
