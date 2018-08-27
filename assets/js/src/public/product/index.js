/**
 * @module Product Variants.
 */

import * as tools from '../../utils/tools';
import variants from './variants';
import modifiers from './modifiers';
import reviews from './reviews';

const el = {
	container: tools.getNodes('.bc-product-single', false, document, true)[0],
};

const init = () => {
	variants(el.container);
	modifiers(el.container);
	reviews(el.container);
};

export default init;
