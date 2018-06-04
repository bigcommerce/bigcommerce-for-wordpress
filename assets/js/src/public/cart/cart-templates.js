/**
 * @module Cart Templates
 */

import { NLS } from '../config/i18n';

export const cartEmpty = (
	`
		<div class="bc-cart__empty">
			<h2 class="bc-cart__title--empty">${NLS.cart.message_empty}</h2>
			<a href="${NLS.cart.continue_shopping_url}" class="bc-cart__continue-shopping">${NLS.cart.continue_shopping_label}</a>
		</div>
	`
);
