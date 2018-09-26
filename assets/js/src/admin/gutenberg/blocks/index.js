/**
 * @module Gutenberg Blocks
 * @description Clearinghouse for all Gutenberg blocks.
 */

import products from './products/products';
import cart from './cart/cart';
import account from './account-profile/account-profile';
import address from './address-list/address-list';
import orders from './order-history/order-history';
import login from './login-form/login-form';
import register from './registration-form/registration-form';
import giftForm from './gift-certificate-form/gift-certificate-form';
import giftBalance from './gift-certificate-balance/gift-certificate-balance';

const { registerBlockType } = wp.blocks;

const blocks =  [
	products,
	cart,
	account,
	address,
	orders,
	login,
	register,
	giftForm,
	giftBalance,
];

const initBlocks = () => {
	blocks.forEach((block) => {
		const blockName = `${block.id}`;
		registerBlockType(blockName, block);
	});
};

export default initBlocks;
