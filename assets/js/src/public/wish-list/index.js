/**
 * @module Wish Lists
 *
 * @description Clearinghouse for Wish Lists Scripts
 *
 */

import manage from './manage-dialogs';
import share from './share';
import product from './product';
import list from './list';

const init = () => {
	manage();
	share();
	product();
	list();
};

export default init;
