/* eslint-disable */
/**
 * @module Gutenberg
 * @description Register the Products Gutenberg block
 */

import { GUTENBERG_PRODUCTS } from '../../config/gutenberg-settings';
import { bigCommerceIcon } from './icon';
import Inspector from './inspector';
import shortcodeState from '../../../config/shortcode-state';
import * as tools from '../../../../utils/tools';
import { wpAPIProductsPreview } from '../../../../utils/ajax';

const { createElement } = wp.element;
const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;

/**
 * @function registerBlock
 * @description register the block
 */

const registerBlock = () => {
	registerBlockType(GUTENBERG_PRODUCTS.name, {
		title: GUTENBERG_PRODUCTS.title,

		/**
		 * An icon property should be specified to make it easier to identify a block.
		 * These can be any of WordPress’ Dashicons, or a custom svg element.
		 * @see https://developer.wordpress.org/resource/dashicons/
		 */
		icon: bigCommerceIcon,

		/**
		 * Blocks are grouped into categories to help with browsing and discovery.
		 * The categories provided by core are common, embed, formatting, layout, and widgets.
		 */
		category: GUTENBERG_PRODUCTS.category,

		/**
		 * Additional keywords to surface this block via search input. Limited to 3.
		 */
		keywords: GUTENBERG_PRODUCTS.keywords,

		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
		},

		/**
		 * Attributes used to save and edit our block.
		 */
		attributes: {
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
		},


		/**
		 * The edit function describes the structure of the block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param  {Object} [props] Properties passed from the editor.
		 * @return {Element}        Element to render.
		 */
		edit: (props) => {
			const {attributes: {queryParams}, setAttributes} = props;
			const spinner = createElement('span', {className: 'spinner is-active', key: 'spinner'});
			const previewEl = createElement('div', {className: 'bigcommerce-product-preview', key: 'preview-shortcode'}, [spinner]);
			const state = {
				currentBlock: props.id,
			};

			const getResponse = (queryObj) => {
				shortcodeState.isFetching = true;
				wpAPIProductsPreview(queryObj)
					.end((err, response) => {
						shortcodeState.isFetching = false;
						const block = tools.getNodes(`[data-block="${state.currentBlock}"]`, false, document, true)[0];
						const wrapper = tools.getNodes('.bigcommerce-product-preview', false, block, true)[0];
						if (!wrapper) {
							return;
						}

						if (err) {
							console.error(err);
							wrapper.innerHTML = '';
							wrapper.innerHTML = __('Preview unavailable', 'bigcommerce');
							return;
						}

						wrapper.innerHTML = '';
						wrapper.innerHTML = response.body.rendered;
					});
			};

			const getQueryParameters = (data) => {
				if (shortcodeState.isFetching) {
					return;
				}

				shortcodeState.isFetching = true;

				data.query_params.preview = 1;
				data.query_params.paged = 0;

				setAttributes({
					shortcode: data.shortcode,
					queryParams: {...data.query_params},
				});
			};

			getResponse(queryParams);

			return [
				<Inspector {...{ setAttributes, ...props, key: 'inspector', handleInsert: getQueryParameters }} />,
				createElement('div', { className: props.className, key: 'shortcode-preview-wrapper' }, [previewEl]),
			];
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * @param  {Object} [props] Properties passed from the editor.
		 * @return {Element} Element to render.
		 */
		save: (props) => {
			const { shortcode } = props.attributes;

			return (
				createElement('div', { className: props.className }, shortcode)
			);
		}
	});
};

const init = () => {
	if (!GUTENBERG_PRODUCTS) {
		return;
	}
	registerBlock();
};

export default init;
