/**
 * @module Inspector Input Field
 */
import { GUTENBERG_PRODUCT_COMPONENTS as BLOCK } from '../../config/gutenberg-settings';

const { Component } = wp.element;

export default class ProductId extends Component {

	constructor(...args) {
		super(...args);
		this.changeEvent = this.changeEvent.bind(this);
	}

	changeEvent(event) {
		const { target } = event;
		const id = target.value;

		this.props.setAttributes({
			shortcode: `[${BLOCK.shortcode} id="${id}" type="${this.props.attributes.componentType}"]`,
			productId: id,
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
