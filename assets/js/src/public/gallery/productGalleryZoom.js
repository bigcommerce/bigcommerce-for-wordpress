/**
 * @module Product Gallery Zoom
 * @description If zoom is enabled in the customizer, setup the image zoom functionality.
 */

import Drift from 'drift-zoom';
import _ from 'lodash';
import * as tools from 'utils/tools';
import { on } from 'utils/events';

const el = {
	container: tools.getNodes('bc-product-image-zoom')[0],
};

const instances = {
	zoomers: {},
};

const imageZoomOptions = {
	containInline: true,
	paneContainer: '',
	zoomFactor: 2,
	inlinePane: 320,
};

const initImageZoomers = () => {
	const image = tools.getNodes('[data-js="bc-gallery-container"] .bc-product-gallery__image-slide:not(.initialized)', true, document, true);

	image.forEach((img) => {
		const zoomMainId = _.uniqueId('zoom-');

		tools.addClass(img, 'initialized');
		img.dataset.zoomid = zoomMainId;
		imageZoomOptions.paneContainer = img;

		instances.zoomers[zoomMainId] = new Drift(img.querySelector('img'), imageZoomOptions);
	});
};

const init = () => {
	if (!el.container) {
		return;
	}

	initImageZoomers();

	on(document, 'bigcommerce/init_slide_zoom', initImageZoomers);
};

export default init;
