/**
 * @module Inspector Input Field
 */
import { GUTENBERG_PRODUCT_COMPONENTS as BLOCK } from '../../config/gutenberg-settings';

const { Component } = wp.element;

export default class ProductComponentType extends Component {

	constructor(...args) {
		super(...args);
		this.changeEvent = this.changeEvent.bind(this);
	}

	changeEvent(event) {
		const { target } = event;
		const type = target.value;

		this.props.setAttributes({
			shortcode: `[${BLOCK.shortcode} id="${this.props.attributes.productId}" type="${type}"]`,
			componentType: target.value,
		});
	}

	render() {
		return (
			<select
				value={this.props.componentType}
				onChange={this.changeEvent}
				id={this.props.componentTypeFieldId}
			>
				{BLOCK.inspector.components.map(obj => <option key={obj.key} value={obj.key}>{obj.label}</option>)}
			</select>
		);
	}
}
