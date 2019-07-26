/**
 * @module Wish Lists
 *
 * @description Clearinghouse for Wish Lists Scripts
 *
 */

import manage from './manage-dialogs';
import share from './share';
import product from './product';

const init = () => {
	manage();
	share();
	product();
};

export default init;
