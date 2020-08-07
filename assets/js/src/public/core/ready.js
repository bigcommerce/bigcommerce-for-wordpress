/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

import _ from 'lodash';
import { on, ready } from 'utils/events';
import applyBrowserClasses from 'utils/dom/apply-browser-classes';

// you MUST do this in every module you use lodash in.
// A custom bundle of only the lodash you use will be built by babel.

import resize from './resize';
import plugins from './plugins';
import viewportDims from './viewport-dims';
import gallery from '../gallery/index';
import buttons from '../buttons/index';
import globalBCJS from '../api/index';
import cart from '../cart/index';
import checkout from '../checkout/index';
import loop from '../loop/index';
import page from '../page/index';
import product from '../product/index';
import wishLists from '../wish-list/index';

import analytics from '../analytics/index';

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */

const bindEvents = () => {
	on(window, 'resize', _.debounce(resize, 200, false));
};

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// apply browser classes

	applyBrowserClasses();

	// init external plugins

	plugins();

	// set initial states

	viewportDims();

	// initialize global events

	bindEvents();
	globalBCJS();

	// initialize the main scripts
	buttons();
	gallery();
	cart();
	checkout();
	loop();
	page();
	product();
	wishLists();
	analytics();

	console.info('BigCommerce FE: Initialized all javascript that targeted document ready.');
};

/**
 * @function domReady
 * @description Export our dom ready enabled init.
 */

const domReady = () => {
	ready(init);
};

export default domReady;

