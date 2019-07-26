/**
 * @module Inspector Input Field
 */
import { debounce } from 'lodash';
import { GUTENBERG_PRODUCT_COMPONENTS as BLOCK } from '../../config/gutenberg-settings';

const { Component, createRef } = wp.element;

export default class ProductComponentType extends Component {

	constructor(...args) {
		super(...args);
		this.fieldRef = createRef();
		this.changeEvent = this.changeEvent.bind(this);
		this.debouncedSetAtts = debounce(this.setAttributes, 500).bind(this);
		this.state = {
			value: this.props.componentType,
		};
	}

	/**
	 * @function setAttributes
	 * @description set the Gutenberg state attributes.
	 */
	setAttributes() {
		this.props.setAttributes({
			shortcode: `[${BLOCK.shortcode} id="${this.props.attributes.productId}" type="${this.state.value}"]`,
			componentType: this.state.value,
		});
	}

	/**
	 * @function changeEvent
	 * @description Event handler for changes in the select field value.
	 */
	changeEvent() {
		this.setState({ value: this.fieldRef.current.value });
		this.debouncedSetAtts();
	}

	render() {
		return (
			<select
				value={this.state.value}
				onChange={this.changeEvent}
				id={this.props.componentTypeFieldId}
				ref={this.fieldRef}
			>
				{BLOCK.inspector.components.map(obj => <option key={obj.key} value={obj.key}>{obj.label}</option>)}
			</select>
		);
	}
}
