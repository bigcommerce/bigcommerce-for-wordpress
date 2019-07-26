/**
 * @module Inspector Input Field
 */
import { debounce } from 'lodash';
import { GUTENBERG_PRODUCT_COMPONENTS as BLOCK } from '../../config/gutenberg-settings';

const { Component, createRef } = wp.element;

export default class ProductId extends Component {

	constructor(...args) {
		super(...args);
		this.fieldRef = createRef();
		this.changeEvent = this.changeEvent.bind(this);
		this.debouncedSetAtts = debounce(this.setAttributes, 500).bind(this);
		this.state = {
			value: this.props.productId,
		};
	}

	/**
	 * @function setAttributes
	 * @description set the Gutenberg state attributes.
	 */
	setAttributes() {
		this.props.setAttributes({
			shortcode: `[${BLOCK.shortcode} id="${this.state.value}" type="${this.props.attributes.componentType}"]`,
			productId: this.state.value,
		});
	}

	/**
	 * @function changeEvent
	 * @description Event handler for changes in the input field value.
	 */
	changeEvent() {
		this.setState({ value: this.fieldRef.current.value });
		this.debouncedSetAtts();
	}

	render() {
		return (
			<input
				type="text"
				id={this.props.fieldId}
				value={this.state.value}
				pattern={/\d+/}
				onChange={this.changeEvent}
				ref={this.fieldRef}
			/>
		);
	}
}
