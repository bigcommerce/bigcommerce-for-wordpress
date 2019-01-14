/**
 * @module Cart
 * @description Clearinghouse for all cart scripts.
 */

import cartItemsAjax from './ajax-items';
import { updateMenuQtyOnPageLoad } from './cart-menu-item';
import cartPage from './cart-page';
import addToCart from './add-to-cart';

const init = () => {
	cartItemsAjax();
	updateMenuQtyOnPageLoad();
	cartPage();
	addToCart();
};

export default init;
