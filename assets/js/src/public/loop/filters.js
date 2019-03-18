/**
 * @module Product Loop Filter Refinery
 *
 * @description Handle the filter refinery state for any FE JS needs.
 */

import * as tools from '../../utils/tools';
import queryToJson from '../../utils/data/query-to-json';

const el = {
	refinery: tools.getNodes('bc-product-archive-refinery', false, document, false)[0],
};

const state = {
	currentSort: {
		key: 'bc-sort',
		value: '',
	},
};

/**
 * @function getCurrentSort
 * @description Since all refinements happen on page load, here we'll set `state.currentSort` to the current `bc-sort` selection.
 */
const getCurrentSort = () => {
	const query = queryToJson();
	if (!query[state.currentSort.key]) {
		return;
	}

	state.currentSort.value = query[state.currentSort.key];
};

/**
 * @function updateFilterReset
 * @description If `state.currentSort` is set, update the filter reset button `href` attribute to keep the current `bc-sort` query parameter.
 */
const updateFilterReset = () => {
	if (!state.currentSort.value || !el.resetButton) {
		return;
	}

	const href = el.resetButton.getAttribute('href');

	el.resetButton.href = `${href}?${state.currentSort.key}=${state.currentSort.value}`;
};

const cacheElements = () => {
	el.resetButton = tools.getNodes('bc-reset-filters', false, document, false)[0];
};

const init = () => {
	if (!el.refinery) {
		return;
	}

	cacheElements();
	getCurrentSort();
	updateFilterReset();
};

export default init;
