/**
 * @module Form query parameter removal scripts.
 */

import _ from 'lodash';
import queryToJson from '../../utils/data/query-to-json';

const el = {
	query: queryToJson(),
	queryVars: ['bc-error', 'bc-message'],
};

/**
 * @function removeQueryVars
 * @description search query for keys matching queryVars array. If found, set URL to current path without page refresh.
 */
const removeQueryVars = () => {
	Object.keys(el.query).forEach((key, i) => {
		if (key === el.queryVars[i]) {
			//remove the parameters without a change in the page
			window.history.replaceState(null, null, window.location.pathname);
		}
	});
};

const init = () => {
	if (_.isEmpty(el.query)) {
		return;
	}

	removeQueryVars();
};

export default init;
