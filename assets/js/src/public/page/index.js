/**
 * @module Page scripts clearinghouse.
 */

import address from './address';
import dynamicStateField from './dynamicStateField';
import formQueryParam from './formQueryParam';
import formErrors from './formErrors';

const init = () => {
	address();
	dynamicStateField();
	formQueryParam();
	formErrors();
};

export default init;
