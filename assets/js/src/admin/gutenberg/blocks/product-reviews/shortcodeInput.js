/**
 * @module Inspector Input Field
 */
import { GUTENBERG_PRODUCT_REVIEWS as BLOCK } from '../../config/gutenberg-settings';

const { Component } = wp.element;

export default class ShortcodeInput extends Component {

	constructor(...args) {
		super(...args);
		this.changeEvent = this.changeEvent.bind(this);
	}

	changeEvent(event) {
		const { target } = event;

		this.props.setAttributes({
			shortcode: `[${BLOCK.shortcode} id="${target.value}"]`,
			productId: target.value,
		});
	}

	render() {
		return (
			<input
				type="text"
				id={this.props.fieldId}
				value={this.props.productId}
				pattern={/\d+/}
				onChange={this.changeEvent}
			/>
		);
	}
}
