/**
 * @module Gutenberg
 * @description Register the Products Gutenberg block
 */

import { bigCommerceIcon } from './icon';
import editBlock from './edit';
import saveBlock from './save';
import ShortcodeBlock from '../shortcode-block/shortcode-block';

export default class ProductsBlock extends ShortcodeBlock {
	constructor(config) {
		super(config);
		this.edit = editBlock;
		this.save = saveBlock;
		this.icon = bigCommerceIcon;
		this.attributes = {
			shortcode: {
				type: 'string',
				default: '',
			},
			queryParams: {
				type: 'object',
				default: {
					preview: 1,
					paged: 0,
				},
			},
		};
	}
}
