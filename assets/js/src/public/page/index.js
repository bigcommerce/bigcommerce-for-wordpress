/**
 * @module Page scripts clearinghouse.
 */

import address from './address';
import dynamicStateField from './dynamicStateField';
import formQueryParam from './formQueryParam';

const init = () => {
	address();
	dynamicStateField();
	formQueryParam();
};

export default init;
