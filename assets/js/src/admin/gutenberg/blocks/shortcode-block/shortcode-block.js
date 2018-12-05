/**
 * @module Gutenberg
 * @description The Gutenberg block for a shortcode placeholder
 */

export default class ShortcodeBlock {

	constructor(config) {
		this.config = config;
		this.id = this.config.name;
		this.title = this.config.title;

		/**
		 * An icon property should be specified to make it easier to identify a block.
		 * These can be any of WordPress’ Dashicons, or a custom svg element.
		 * @see https://developer.wordpress.org/resource/dashicons/
		 */
		this.icon = this.config.icon;

		/**
		 * Blocks are grouped into categories to help with browsing and discovery.
		 * The categories provided by core are common, embed, formatting, layout, and widgets.
		 */
		this.category = this.config.category;

		/**
		 * Additional keywords to surface this block via search input. Limited to 3.
		 */
		this.keywords = this.config.keywords;

		this.supports = {
			html: false,
		};

		this.attributes = {
			shortcode: {
				type: 'string',
				default: `[${this.config.shortcode}]`,
			},
		};

		this.edit = this.edit.bind(this);
		this.save = this.save.bind(this);
	}

	/**
	 * The edit function describes the structure of the block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
	 *
	 * @param  {Object} [props] Properties passed from the editor.
	 * @return {Element}        Element to render.
	 */
	edit(props) {
		const { setAttributes } = props;
		const titleKey = `${this.id}-shortcode-title`;
		const imgKey = `${this.id}-shortcode-preview`;

		setAttributes({
			shortcode: `[${this.config.shortcode}]`,
		});

		return [
			<h2
				className={props.className}
				key={titleKey}
			>
				{this.config.block_html.title}
			</h2>,
			<img
				src={this.config.block_html.image}
				alt={this.title}
				className={props.className}
				key={imgKey}
			/>,
		];
	}

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into `post_content`.
	 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
	 *
	 * @param  {Object} [props] Properties passed from the editor.
	 * @return {Element} Element to render.
	 */
	save(props) {
		return props.attributes.shortcode;
	}
}
