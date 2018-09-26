/**
 * @module Shortcode Settings
 * @description Clearinghouse for shortcode settings page(s) scripts.
 */

import settings from './settings';
import toggleSection from './toggle-section';

const init = () => {
	settings();
	toggleSection();
};

export default init;
