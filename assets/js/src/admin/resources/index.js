/**
 * @module BigCommerce Resources
 * @description Clearinghouse for resources page scripts.
 */

import tabs from './tabs';
import resources from './resources';

const init = () => {
	resources()
		.then(tabs);
};

export default init;
