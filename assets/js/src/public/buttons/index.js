/**
 * @module Buttons Buttons Buttons
 * @description Clearing house to load all public button functionality.
 */

import quickViewDialog from './quick-view-dialog';
import pagination from './pagination';
import analytics from './analytics';

const init = () => {
	quickViewDialog();
	pagination();
	analytics();
};

export default init;
