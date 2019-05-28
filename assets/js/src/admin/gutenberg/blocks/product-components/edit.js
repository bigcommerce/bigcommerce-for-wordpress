/**
 * @module Edit
 * @description Product Components block edit method.
 */


import { GUTENBERG_PRODUCT_COMPONENTS as BLOCK } from '../../config/gutenberg-settings';
import ProductId from './productId';
import ProductComponentType from './productComponentType';

const { InspectorControls } = wp.editor;
const { PanelRow, PanelBody, BaseControl } = wp.components;
const { withInstanceId } = wp.compose;
const { Fragment } = wp.element;

const editBlock = withInstanceId((props) => {
	const { attributes, setAttributes, instanceId } = props;
	const { productId, componentType } = attributes;
	const blockImage = BLOCK.block_html.image;
	const fieldId = `block-product-components-input-${instanceId}`;
	const componentTypeFieldId = `block-product-components-type-input-${instanceId}`;

	return (
		<Fragment>
			<h2
				className={props.className}
				key="product-components-title"
			>
				{BLOCK.block_html.title}
			</h2>
			<img
				src={blockImage}
				alt={BLOCK.title}
				className={props.className}
				key="product-components-preview"
			/>
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
							<ProductId {...{ setAttributes, ...props, key: 'ProductId', fieldId, productId, componentType }} />
						</BaseControl>
					</PanelRow>
					<PanelRow>
						<BaseControl
							label={BLOCK.inspector.component_id_label}
							id={fieldId}
							help={BLOCK.inspector.component_id_description}
						>
							<ProductComponentType {...{ setAttributes, ...props, key: 'ProductComponentType', fieldId, componentTypeFieldId, componentType }} />
						</BaseControl>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		</Fragment>
	);
});

export default editBlock;
