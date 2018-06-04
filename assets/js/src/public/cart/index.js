/**
 * @module Cart
 * @description Clearinghouse for all cart scripts.
 */

import cartItemsAjax from './ajax-items';
import { updateMenuQtyOnPageLoad } from './cart-menu-item';
import cartPage from './cart-page';

const init = () => {
	cartItemsAjax();
	updateMenuQtyOnPageLoad();
	cartPage();
};

export default init;
