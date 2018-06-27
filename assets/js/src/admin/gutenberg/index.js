/**
 * @module Gutenberg
 * @description Clearinghouse for loading all Gutenberg scripts.
 */

import products from './blocks/products/products';
import cart from './blocks/cart/cart';
import account from './blocks/account-profile/account-profile';
import address from './blocks/address-list/address-list';
import orders from './blocks/order-history/order-history';
import login from './blocks/login-form/login-form';
import register from './blocks/registration-form/registration-form';
import dialog from '../shortcode-ui/dialog-ui';
import storeLink from './plugins/store-link';

const initBlocks = () => {
	dialog();
	products();
	cart();
	account();
	address();
	orders();
	login();
	register();

	console.info('Big Commerce: Initialized Gutenberg block scripts.');
};

const initPlugins = () => {
	storeLink();

	console.info('Big Commerce: Initialized Gutenberg plugin scripts.');
};

window.bigcommerce_gutenberg_config.initPlugins = initPlugins;

initBlocks();
