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

	constructor(...args) {
		super(...args);
		this.triggerDialog = this.triggerDialog.bind(this);
	}

	triggerDialog(e) {
		const { target } = e;
		const queryParams = this.props.attributes.queryParams;
		shortcodeState.insertCallback = this.props.handleInsert;

		trigger({ event: 'bigcommerce/init_shortcode_ui', data: { target, queryParams }, native: false });
	}

	render() {
		return (
			<InspectorControls>
				<PanelBody
					title={GUTENBERG_PRODUCTS.inspector.title}
					initialOpen
				>
					<PanelRow>
						<Button
							isPrimary
							type="button"
							className="button bc-add-products"
							data-js="bc-add-products"
							data-content="bc-shortcode-ui"
							onClick={this.triggerDialog}
						>
							{GUTENBERG_PRODUCTS.inspector.button_title}
						</Button>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		);
	}
}
