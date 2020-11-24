/**
 * @module Cart
 * @description Clearinghouse for all cart scripts.
 */

import cartItemsAjax from './ajax-items';
import { updateMenuQtyOnPageLoad } from './cart-menu-item';
import cartPage from './cart-page';
import addToCart from './add-to-cart';
import miniCartWidget from './mini-cart-widget';
import miniCartNav from './mini-cart-nav';
import shippingCalc from './shipping-calculator';

const init = () => {
	cartItemsAjax();
	updateMenuQtyOnPageLoad();
	cartPage();
	addToCart();
	miniCartWidget();
	miniCartNav();
	shippingCalc();
};

export default init;
