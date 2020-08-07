/**
 * @module Analytics Tracking Events
 * @description Clearing house to load all public analytics functionality.
 */

import segment from './segment';
import matomo from './matomo';

const init = () => {
	segment();
	matomo();
};

export default init;
