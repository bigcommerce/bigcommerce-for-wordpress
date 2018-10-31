/**
 * @module Shortcode Settings
 * @description Clearinghouse for shortcode settings page(s) scripts.
 */

import settings from './settings';
import toggleSection from './toggle-section';
import connectAccount from './connect-account';
import channels from './channels';
import dynamicStateField from './dynamicStateField';

const init = () => {
	settings();
	toggleSection();
	connectAccount();
	channels();
	dynamicStateField();
};

export default init;
