/**
 * @module Gutenberg Plugins
 * @description Clearing house for gutenberg plugins.
 */

import channelIndicator from './channel-indicator';
import storeLink from './store-link';

const initPlugins = () => {
	channelIndicator();
	storeLink();

	console.info('BigCommerce: Initialized Gutenberg plugin scripts.');
};

const init = () => {
	window.bigcommerce_gutenberg_config.initPlugins = initPlugins;
};

export default init;
