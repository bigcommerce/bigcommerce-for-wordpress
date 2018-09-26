/**
 * @module Gutenberg
 * @description Clearinghouse for loading all Gutenberg scripts.
 */

import dialog from '../shortcode-ui/dialog-ui';
import plugins from './plugins/index';
import initBlocks from './blocks/index';
import shortcodeState from '../config/shortcode-state';

const initGutenbergScripts = () => {
	shortcodeState.isGutenberg = true;
	plugins();
	dialog();
	initBlocks();
};

initGutenbergScripts();
