/**
 * @module Edit
 * @description Product Reviews block edit method.
 */


import { GUTENBERG_PRODUCT_REVIEWS as BLOCK } from '../../config/gutenberg-settings';
import ShortcodeInput from './shortcodeInput';

const { InspectorControls } = wp.editor;
const { PanelRow, PanelBody, BaseControl } = wp.components;
const { withInstanceId } = wp.compose;
const { Fragment } = wp.element;

const editBlock = withInstanceId((props) => {
	const { attributes, setAttributes, instanceId } = props;
	const { productId } = attributes;
	const blockImage = BLOCK.block_html.image;
	const fieldId = `block-product-reviews-input-${instanceId}`;

	return (
		<Fragment>
			<h2
				className={props.className}
				key="product-reviews-title"
			>
				{BLOCK.block_html.title}
			</h2>
			<img
				src={blockImage}
				alt={BLOCK.title}
				className={props.className}
				key="product-reviews-preview"
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
							<ShortcodeInput {...{ setAttributes, ...props, key: 'ShortcodeInput', fieldId, productId }} />
						</BaseControl>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		</Fragment>
	);
});

export default editBlock;
