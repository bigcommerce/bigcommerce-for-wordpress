/**
 * @module InstancedEdit
 * @description Instanced edit to handle debouncing and state.
 */

import * as tools from 'utils/tools';
import { I18N } from 'adminConfig/i18n';
import { wpAPIProductComponentPreview } from 'utils/ajax';
import { GUTENBERG_PRODUCT_COMPONENTS as BLOCK } from '../../config/gutenberg-settings';
import ProductId from './productId';
import ProductComponentType from './productComponentType';

const { InspectorControls } = wp.editor;
const { PanelRow, PanelBody, BaseControl } = wp.components;
const { Component, Fragment } = wp.element;

/**
 * @function getResponse
 * @description Get the AJAX response form the current block instance.
 * @param props
 * @param queryObj
 * @param instanceState
 */
const getResponse = (props, queryObj, instanceState) => {
	const state = {
		currentBlock: props.clientId,
	};

	// If we're already processing a request, cancel it.
	if (instanceState.req) {
		instanceState.req.abort();
	}

	// Send the endpoint request.
	instanceState.req = wpAPIProductComponentPreview(queryObj)
		.end((err, response) => {
			instanceState.req = null;
			const block = tools.getNodes(`[data-block="${state.currentBlock}"]`, false, document, true)[0];
			const wrapper = tools.getNodes('.bigcommerce-product-component-preview', false, block, true)[0];
			const fragment = document.createElement('h2');
			if (!wrapper) {
				return;
			}

			wrapper.innerHTML = '';

			if (err) {
				console.error(err);
				fragment.textContent = `${I18N.messages.ajax_error}`;
				wrapper.appendChild(fragment);
				return;
			}

			if (response.body.rendered.length === 0) {
				fragment.textContent = `${I18N.messages.no_products}`;
				wrapper.appendChild(fragment);
				return;
			}

			wrapper.insertAdjacentHTML('beforeend', response.body.rendered);
		});
};

/**
 * @class InstancedEdit
 * @description Creates an isolated instanced state handler to attach to the current component.
 */
export default class InstancedEdit extends Component {

	constructor(props) {
		super(props);
		this.state = {
			req: null,
		};
	}

	render() {
		const { attributes, setAttributes, instanceId } = this.props;
		const { productId, componentType } = attributes;
		const fieldId = `block-product-components-input-${instanceId}`;
		const componentTypeFieldId = `block-product-components-type-input-${instanceId}`;
		const queryObj = {
			preview: 1,
			id: productId,
			type: componentType,
		};

		getResponse(this.props, queryObj, this.state);

		return (
			<Fragment>
				<div
					className={this.props.className}
					key="shortcode-preview-wrapper"
				>
					<div
						className="bigcommerce-product-component-preview"
						key="preview-shortcode"
					>
						<span
							className="spinner is-active"
							key="spinner"
						/>
					</div>
				</div>
				<InspectorControls>
					<PanelBody
						title={BLOCK.inspector.header}
						initialOpen
					>
						<PanelRow>
							<BaseControl
								label={BLOCK.inspector.product_id_label}
								id={fieldId}
								help={BLOCK.inspector.product_id_description}
							>
								<ProductId {...{ setAttributes, ...this.props, key: 'ProductId', fieldId, productId, componentType }} />
							</BaseControl>
						</PanelRow>
						<PanelRow>
							<BaseControl
								label={BLOCK.inspector.component_id_label}
								id={fieldId}
								help={BLOCK.inspector.component_id_description}
							>
								<ProductComponentType {...{ setAttributes, ...this.props, key: 'ProductComponentType', fieldId, componentTypeFieldId, componentType }} />
							</BaseControl>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	}
}
