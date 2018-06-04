/**
 * @module Gutenberg
 * @description Clearinghouse for loading all Gutenberg scripts.
 */

import products from './blocks/products/products';
import dialog from '../shortcode-ui/dialog-ui';

const init = () => {
	dialog();
	products();

	console.info('Big Commerce: Initialized Gutenberg scripts.');
};

init();
