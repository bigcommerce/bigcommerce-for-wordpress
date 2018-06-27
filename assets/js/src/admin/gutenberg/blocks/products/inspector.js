/* eslint-disable */
/**
 * @module Products Block Inspector
 */
import { GUTENBERG_PRODUCTS } from '../../config/gutenberg-settings';
import { trigger } from '../../../../utils/events';
import shortcodeState from '../../../config/shortcode-state';

const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const { PanelRow, PanelBody, Button } = wp.components;

export default class Inspector extends Component {

	constructor() {
		super( ...arguments );
		this.triggerDialog = this.triggerDialog.bind(this);
	}

	triggerDialog(e) {
		const { target } = e;
		const queryParams = this.props.attributes.queryParams;
		shortcodeState.isGutenberg = true;
		shortcodeState.insertCallback = this.props.handleInsert;

		trigger({ event: 'bigcommerce/init_shortcode_ui', data: { target, queryParams }, native: false });
	}

	render() {
		return (
			<InspectorControls>
				<PanelBody
					title={GUTENBERG_PRODUCTS.inspector_title}
					initialOpen={true}
				>
					<PanelRow>
						<Button
							isPrimary={true}
							type="button"
							className="button bc-add-products"
							data-js="bc-add-products"
							data-content="bc-shortcode-ui"
							onClick={this.triggerDialog}
						>
							{GUTENBERG_PRODUCTS.inspector_button_title}
						</Button>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		);
	}
}
