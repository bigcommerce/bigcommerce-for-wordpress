/**
 * @module Buttons Buttons Buttons
 * @description Clearing house to load all public button functionality.
 */

import quickViewDialog from './quick-view-dialog';
import pagination from './pagination';

const init = () => {
	quickViewDialog();
	pagination();
};

export default init;
