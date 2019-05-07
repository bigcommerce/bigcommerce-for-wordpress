/* eslint-disable */
/**
 * @module Gutenberg
 * @description Add the "Open in BigCommerce" link to the publish meta box
 */

import {GUTENBERG_CHANNEL_INDICATOR} from '../config/gutenberg-settings';

const { Fragment } = wp.element;
const { registerPlugin } = wp.plugins;


const register = () => {
	const { PluginPostStatusInfo } = wp.editPost;
	const ChannelIndicator = () => (
		<Fragment>
			<PluginPostStatusInfo>
				<span class="bigcommerce-channel-label">{GUTENBERG_CHANNEL_INDICATOR.label}</span>
				<span class="bigcommerce-channel-name">{GUTENBERG_CHANNEL_INDICATOR.value}</span>
			</PluginPostStatusInfo>
		</Fragment>
	);

	registerPlugin('bigcommerce-channel-indicator', {
		render: ChannelIndicator,
	});

};

const init = () => {
	if (!GUTENBERG_CHANNEL_INDICATOR.value) {
		return;
	}
	register();
};

export default init;