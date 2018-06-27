/* eslint-disable */
/**
 * @module Gutenberg
 * @description Add the "Open in BigCommerce" link to the publish meta box
 */

import {GUTENBERG_STORE_LINK} from '../config/gutenberg-settings';

const { Fragment } = wp.element;
const { registerPlugin } = wp.plugins;


const register = () => {
	const { PluginPostStatusInfo } = wp.editPost;
	const StoreLink = () => (
		<Fragment>
			<PluginPostStatusInfo>
				<div className="misc-pub-section misc-pub-bigcommerce">
					<span className="dashicons dashicons-bigcommerce" /> <a href={ GUTENBERG_STORE_LINK.url } target="_blank">{ GUTENBERG_STORE_LINK.label }</a>
				</div>
			</PluginPostStatusInfo>
		</Fragment>
	);

	registerPlugin( 'bigcommerce-store-link', {
		render: StoreLink,
	} );

};

const init = () => {
	if (!GUTENBERG_STORE_LINK.url) {
		return;
	}
	register();
};

export default init;