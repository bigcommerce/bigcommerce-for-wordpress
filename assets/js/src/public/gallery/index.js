/**
 * @module BigCommerce Product Gallery Sliders
 * @description Clearinghouse for loading all gallery JS.
 */

import gallery from './productGallery';
import videos from './productVideos';

const init = () => {
	gallery();
	videos();
};

export default init;
