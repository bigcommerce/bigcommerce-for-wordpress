export const gutenbergconfig = window.bigcommerce_gutenberg_config || {};

export const GUTENBERG_BLOCKS = gutenbergconfig.blocks || {};
export const GUTENBERG_PRODUCTS = GUTENBERG_BLOCKS['bigcommerce/products'] || {};
export const GUTENBERG_CART = GUTENBERG_BLOCKS['bigcommerce/cart'] || {};
export const GUTENBERG_ACCOUNT = GUTENBERG_BLOCKS['bigcommerce/account-profile'] || {};
export const GUTENBERG_ADDRESS = GUTENBERG_BLOCKS['bigcommerce/address-list'] || {};
export const GUTENBERG_ORDERS = GUTENBERG_BLOCKS['bigcommerce/order-history'] || {};
export const GUTENBERG_LOGIN = GUTENBERG_BLOCKS['bigcommerce/login-form'] || {};
export const GUTENBERG_REGISTER = GUTENBERG_BLOCKS['bigcommerce/registration-form'] || {};
export const GUTENBERG_STORE_LINK = gutenbergconfig.store_link || '';
