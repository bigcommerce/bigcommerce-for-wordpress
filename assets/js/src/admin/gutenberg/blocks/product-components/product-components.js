/**
 * @module Gutenberg
 * @description Register the Cart Gutenberg block
 */

import editBlock from './edit';
import ShortcodeBlock from '../shortcode-block/shortcode-block';

export default class ProductComponentBlock extends ShortcodeBlock {
	constructor(config) {
		super(config);
		this.edit = editBlock;
		this.attributes = {
			shortcode: {
				type: 'string',
				default: `[${this.config.shortcode}]`,
			},
			productId: {
				type: 'string',
				default: '',
			},
			componentType: {
				type: 'string',
				default: 'title',
			},
		};
	}
}
