/**
 * @module Gutenberg Plugins
 * @description Clearing house for gutenberg plugins.
 */

import storeLink from './store-link';

const initPlugins = () => {
	storeLink();

	console.info('BigCommerce: Initialized Gutenberg plugin scripts.');
};

const init = () => {
	window.bigcommerce_gutenberg_config.initPlugins = initPlugins;
};

export default init;
