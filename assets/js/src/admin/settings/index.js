/**
 * @module Shortcode Settings
 * @description Clearinghouse for shortcode settings page(s) scripts.
 */

import settings from './settings';
import toggleSection from './toggle-section';
import connectAccount from './connect-account';
import createAccount from './create-account';
import channels from './channels';
import productSync from './product-sync';
import dynamicStateField from './dynamicStateField';
import diagnostics from './diagnostics';

const init = () => {
	settings();
	toggleSection();
	connectAccount();
	createAccount();
	channels();
	productSync();
	dynamicStateField();
	diagnostics();
};

export default init;
