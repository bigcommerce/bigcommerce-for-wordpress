/**
 * @module BigCommerce Product Gallery Sliders
 * @description Clearinghouse for loading all gallery JS.
 */

import gallery from './productGallery';
import videos from './productVideos';
import imageZoom from './productGalleryZoom';

const init = () => {
	gallery();
	videos();
	imageZoom();
};

export default init;
