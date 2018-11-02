/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */

import { embedCheckout } from '@bigcommerce/checkout-sdk';
import { cartEmpty } from '../cart/cart-templates';
import * as tools from '../../utils/tools';

const el = {
	container: tools.getNodes('bc-embedded-checkout')[0],
};

const loadEmbeddedCheckout = () => {
	const config = JSON.parse(el.container.dataset.config);

	if (!config.url || config.url < 0) {
		el.container.innerHTML = cartEmpty;
		return;
	}

	embedCheckout(config);
};

const init = () => {
	if (!el.container) {
		return;
	}

	loadEmbeddedCheckout();
};

export default init;
