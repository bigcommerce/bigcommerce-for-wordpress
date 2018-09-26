/**
 * @module Products CPT
 * @description Scripts used on the products CPT admin views.
 */

import * as tools from '../../utils/tools';

const el = {
	container: tools.getNodes('.edit-php.post-type-bigcommerce_product', false, document, true)[0],
};

const injectSyncButton = () => {
	const syncButton = tools.getNodes('bc-product-sync-button', false, el.container)[0];
	const syncButtonAnchor = tools.getNodes('.bc-admin-btn', false, syncButton, true)[0];
	const btnLocation = tools.getNodes('.wrap > .wp-heading-inline', false, el.container, true)[0];

	if (!syncButton) {
		return;
	}

	const icon = document.createElement('i');
	tools.addClass(icon, 'bc-icon');
	tools.addClass(icon, 'icon-bc-sync');
	syncButtonAnchor.insertBefore(icon, syncButtonAnchor.firstChild);
	tools.addClass(syncButton, 'bc-settings-header__cta-btn--active');
	btnLocation.appendChild(syncButton);
};

const init = () => {
	if (!el.container) {
		return;
	}

	injectSyncButton();
};

export default init;
