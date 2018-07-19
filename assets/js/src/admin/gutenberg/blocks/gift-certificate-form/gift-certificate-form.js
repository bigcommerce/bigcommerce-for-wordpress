/* eslint-disable */
/**
 * @module Gutenberg
 * @description Register the Gift Certificate Form Gutenberg block
 */

import { GUTENBERG_GIFT_CERTIFICATE_FORM as BLOCK } from '../../config/gutenberg-settings';

const { registerBlockType } = wp.blocks;

/**
 * @function registerBlock
 * @description register the block
 */

const registerBlock = () => {
	registerBlockType(BLOCK.name, {
		title: BLOCK.title,

		/**
		 * An icon property should be specified to make it easier to identify a block.
		 * These can be any of WordPress’ Dashicons, or a custom svg element.
		 * @see https://developer.wordpress.org/resource/dashicons/
		 */
		icon: 'tickets-alt',

		/**
		 * Blocks are grouped into categories to help with browsing and discovery.
		 * The categories provided by core are common, embed, formatting, layout, and widgets.
		 */
		category: BLOCK.category,

		/**
		 * Additional keywords to surface this block via search input. Limited to 3.
		 */
		keywords: BLOCK.keywords,

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
		attributes: {},


		/**
		 * The edit function describes the structure of the block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param  {Object} [props] Properties passed from the editor.
		 * @return {Element}        Element to render.
		 */
		edit: (props) => {
			return BLOCK.shortcode;
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
			return BLOCK.shortcode;
		}
	});
};

const init = () => {
	if (!BLOCK) {
		return;
	}
	registerBlock();
};

export default init;
