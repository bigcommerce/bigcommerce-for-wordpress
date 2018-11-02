/**
 * @module Checkout
 * @description Clearinghouse for all checkout scripts.
 */

import embeddedCheckout from './embedded-checkout';

const init = () => {
	embeddedCheckout();
};

export default init;
