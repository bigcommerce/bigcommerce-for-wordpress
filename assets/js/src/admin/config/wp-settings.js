const wpadmin = window.bigcommerce_admin_config || {};
const editorDialog = wpadmin.editor_dialog || {};

export const PRODUCTS_ENDPOINT = editorDialog.product_api_url || '';
export const SHORTCODE_ENDPOINT = editorDialog.shortcode_api_url || '';
export const ADMIN_IMAGES = wpadmin.images_url || '';
export const ADMIN_ICONS = wpadmin.icons_url || '';
export const PRODUCTS_CATEGORY = wpadmin.categories;
export const PRODUCTS_FLAG = wpadmin.flags;
export const PRODUCTS_BRAND = wpadmin.brands;
export const PRODUCTS_SEARCH = wpadmin.search;
export const PRODUCTS_RECENT = wpadmin.recent;
export const PRODUCTS_ORDER = wpadmin.sort_order;
export const ACCOUNT_NONCE = wpadmin.account_rest_nonce;
export const ACCOUNT_ACTION = wpadmin.account_rest_action;
export const COUNTRIES_OBJ = wpadmin.countries;
