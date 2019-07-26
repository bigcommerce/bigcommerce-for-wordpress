export const gutenbergconfig = window.bigcommerce_gutenberg_config || {};

export const GUTENBERG_BLOCKS = gutenbergconfig.blocks || {};
export const GUTENBERG_PRODUCTS = GUTENBERG_BLOCKS['bigcommerce/products'] || {};
export const GUTENBERG_CART = GUTENBERG_BLOCKS['bigcommerce/cart'] || {};
export const GUTENBERG_CHECKOUT = GUTENBERG_BLOCKS['bigcommerce/checkout'] || {};
export const GUTENBERG_ACCOUNT = GUTENBERG_BLOCKS['bigcommerce/account-profile'] || {};
export const GUTENBERG_ADDRESS = GUTENBERG_BLOCKS['bigcommerce/address-list'] || {};
export const GUTENBERG_ORDERS = GUTENBERG_BLOCKS['bigcommerce/order-history'] || {};
export const GUTENBERG_LOGIN = GUTENBERG_BLOCKS['bigcommerce/login-form'] || {};
export const GUTENBERG_REGISTER = GUTENBERG_BLOCKS['bigcommerce/registration-form'] || {};
export const GUTENBERG_GIFT_CERTIFICATE_FORM = GUTENBERG_BLOCKS['bigcommerce/gift-certificate-form'] || {};
export const GUTENBERG_GIFT_CERTIFICATE_BALANCE = GUTENBERG_BLOCKS['bigcommerce/gift-certificate-balance'] || {};
export const GUTENBERG_PRODUCT_REVIEWS = GUTENBERG_BLOCKS['bigcommerce/product-reviews'] || {};
export const GUTENBERG_PRODUCT_COMPONENTS = GUTENBERG_BLOCKS['bigcommerce/product-components'] || {};
export const GUTENBERG_WISHLIST = GUTENBERG_BLOCKS['bigcommerce/wishlist'] || {};
export const GUTENBERG_STORE_LINK = gutenbergconfig.store_link || '';
export const GUTENBERG_CHANNEL_INDICATOR = gutenbergconfig.channel_indicator || '';
