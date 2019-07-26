/**
 * @module Gutenberg Blocks
 * @description Clearinghouse for all Gutenberg blocks.
 */

import ShortcodeBlock from './shortcode-block/shortcode-block';
import ProductsBlock from './products/products';
import ProductReviewsBlock from './product-reviews/product-reviews';
import ProductComponentsBlock from './product-components/product-components';

import {
	GUTENBERG_ACCOUNT,
	GUTENBERG_ADDRESS,
	GUTENBERG_CART,
	GUTENBERG_CHECKOUT,
	GUTENBERG_GIFT_CERTIFICATE_BALANCE,
	GUTENBERG_GIFT_CERTIFICATE_FORM,
	GUTENBERG_LOGIN,
	GUTENBERG_ORDERS,
	GUTENBERG_PRODUCT_COMPONENTS,
	GUTENBERG_PRODUCT_REVIEWS,
	GUTENBERG_PRODUCTS,
	GUTENBERG_REGISTER,
	GUTENBERG_WISHLIST,
} from '../config/gutenberg-settings';

const { registerBlockType } = wp.blocks;

const blocks = [
	new ProductsBlock(GUTENBERG_PRODUCTS),
	new ShortcodeBlock(GUTENBERG_CART),
	new ShortcodeBlock(GUTENBERG_CHECKOUT),
	new ShortcodeBlock(GUTENBERG_ACCOUNT),
	new ShortcodeBlock(GUTENBERG_ADDRESS),
	new ShortcodeBlock(GUTENBERG_ORDERS),
	new ShortcodeBlock(GUTENBERG_LOGIN),
	new ShortcodeBlock(GUTENBERG_REGISTER),
	new ShortcodeBlock(GUTENBERG_GIFT_CERTIFICATE_FORM),
	new ShortcodeBlock(GUTENBERG_GIFT_CERTIFICATE_BALANCE),
	new ShortcodeBlock(GUTENBERG_WISHLIST),
	new ProductReviewsBlock(GUTENBERG_PRODUCT_REVIEWS),
	new ProductComponentsBlock(GUTENBERG_PRODUCT_COMPONENTS),
];

const initBlocks = () => {
	blocks.forEach((block) => {
		if (!block.id) {
			return;
		}
		registerBlockType(`${block.id}`, block);
	});
};

export default initBlocks;
