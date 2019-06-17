/**
 * @module Edit
 * @description Products block edit method.
 */


import { wpAPIProductsPreview, wpAPIShortcodeBuilder } from 'utils/ajax';
import * as tools from 'utils/tools';
import shortcodeState from 'adminConfig/shortcode-state';
import { I18N } from 'adminConfig/i18n';
import Inspector from './inspector';

const getResponse = (props, queryObj) => {
	const state = {
		currentBlock: props.clientId,
	};

	shortcodeState.isFetching = true;
	wpAPIProductsPreview(queryObj)
		.end((err, response) => {
			shortcodeState.isFetching = false;
			const block = tools.getNodes(`[data-block="${state.currentBlock}"]`, false, document, true)[0];
			const wrapper = tools.getNodes('.bigcommerce-product-preview', false, block, true)[0];
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

const editBlock = (props) => {
	const { attributes: { queryParams, shortcode }, setAttributes } = props;

	if (shortcode.length === 0) {
		shortcodeState.isFetching = true;
		wpAPIShortcodeBuilder(queryParams)
			.end((err, response) => {
				shortcodeState.isFetching = false;

				if (err) {
					console.error(err);
				}

				const data = {
					query_params: { ...response.body.attributes },
				};
				data.query_params.preview = 1;
				data.query_params.paged = 0;

				setAttributes({
					shortcode: response.body.shortcode,
					queryParams: { ...data.query_params },
				});
			});
	}

	const getQueryParameters = (data) => {
		if (shortcodeState.isFetching) {
			return;
		}

		shortcodeState.isFetching = true;

		data.query_params.preview = 1;
		data.query_params.paged = 0;

		setAttributes({
			shortcode: data.shortcode,
			queryParams: { ...data.query_params },
		});
	};

	getResponse(props, queryParams);

	return [
		<Inspector {...{ setAttributes, ...props, key: 'inspector', handleInsert: getQueryParameters }} />,
		<div
			className={props.className}
			key="shortcode-preview-wrapper"
		>
			<div
				className="bigcommerce-product-preview"
				key="preview-shortcode"
			>
				<span
					className="spinner is-active"
					key="spinner"
				/>
			</div>
		</div>,
	];
};

export default editBlock;
