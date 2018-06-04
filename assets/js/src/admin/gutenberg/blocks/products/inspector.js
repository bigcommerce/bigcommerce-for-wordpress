/* eslint-disable */
/**
 * @module Products Block Inspector
 */
import { GUTENBERG_PRODUCTS } from '../../config/gutenberg-settings';
import { trigger } from '../../../../utils/events';
import shortcodeState from '../../../config/shortcode-state';

const { Component } = wp.element;
const { InspectorControls } = wp.blocks;
const { PanelRow, PanelBody, Button } = wp.components;

export default class Inspector extends Component {

	constructor() {
		super( ...arguments );
		this.triggerDialog = this.triggerDialog.bind(this);
	}

	triggerDialog(e) {
		const { target } = e;
		shortcodeState.isGutenberg = true;
		shortcodeState.insertCallback = this.props.handleInsert;

		// TODO: update shortcode dialog UI to match values from
		// this.props.attributes.queryParams

		trigger({ event: 'bigcommerce/init_shortcode_ui', data: { target }, native: false });
		trigger({ event: 'bigcommerce/reset_shortcode_ui', native: false });
	}

	render() {
		const { setAttributes } = this.props;

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
