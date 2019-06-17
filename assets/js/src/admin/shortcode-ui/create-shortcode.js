/**
 * @module Create Shortcode
 * @description create a shortcode based on the selected options and add it to the WYSIWYG;
 */

import delegate from 'delegate';
import * as tools from '../../utils/tools';
import shortcodeState from '../config/shortcode-state';
import { wpAPIShortcodeBuilder } from '../../utils/ajax';
import { I18N } from '../config/i18n';
import { trigger } from '../../utils/events';

const el = {};

/**
 * @function queryObjectToString
 * @description iterate over the wpAPIQueryObj object and create a query string.
 * @returns {string}
 */
const shortcodeObjectToString = () => {
	const str = [];

	if (shortcodeState.selectedProducts.bc_id.length > 0) {
		Object.entries(shortcodeState.selectedProducts).forEach(([key, value]) => {
			if (value.length <= 0) {
				return;
			}
			const k = encodeURIComponent(key);
			const v = encodeURIComponent(value);
			str.push(`${k}=${v}`);
		});
	} else {
		Object.entries(shortcodeState.wpAPIQueryObj).forEach(([key, value]) => {
			if (value.length <= 0) {
				return;
			}
			const k = encodeURIComponent(key);
			const v = encodeURIComponent(value);
			str.push(`${k}=${v}`);
		});
	}

	Object.entries(shortcodeState.wpAPIDisplaySettings).forEach(([key, value]) => {
		if (value.length <= 0) {
			return;
		}
		const k = encodeURIComponent(key);
		const v = encodeURIComponent(value);
		str.push(`${k}=${v}`);
	});

	return str.length ? str.join(I18N.operations.query_string_separator) : '';
};

/**
 * @function insertAtCaret
 * @description insert the shortcode at the current caret position in the text field.
 * @param targetField
 * @param shortcode
 */
const insertAtCaret = (targetField, shortcode) => {
	if (document.selection) {
		//IE support
		targetField.focus();
		const sel = document.selection.createRange();
		sel.text = shortcode;
	} else if (targetField.selectionStart || targetField.selectionStart === 0) {
		const startPos = targetField.selectionStart;
		const endPos = targetField.selectionEnd;
		targetField.value = targetField.value.substring(0, startPos)
			+ shortcode
			+ targetField.value.substring(endPos, targetField.value.length);
	} else {
		targetField.value += shortcode;
	}
};

/**
 * @function addShortcodeToTheEditor
 * @description Place the shortcode string in the current editor.
 * @param shortcode
 * @param attributes
 */
const addShortcodeToTheEditor = (shortcode, attributes) => {
	if (shortcodeState.isGutenberg) {
		trigger({ event: 'bigcommerce/init_shortcode_ui', data: { hide: true }, native: false });
		trigger({ event: 'bigcommerce/reset_shortcode_ui', native: false });
		if (shortcodeState.insertCallback) {
			shortcodeState.insertCallback({ query_params: attributes, shortcode });
		}
		return;
	}

	if (tools.hasClass(shortcodeState.currentEditor, 'html-active')) {
		const target = tools.getNodes('.wp-editor-area', false, shortcodeState.currentEditor, true)[0];
		insertAtCaret(target, shortcode);
		trigger({ event: 'bigcommerce/init_shortcode_ui', data: { hide: true }, native: false });
		return;
	}

	window.tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);
	trigger({ event: 'bigcommerce/init_shortcode_ui', data: { hide: true }, native: false });
};

/**
 * @function generateShortcode
 * @description run the WP API query to generate the shortcode output.
 */
const generateShortcode = () => {
	shortcodeState.isFetching = true;

	wpAPIShortcodeBuilder(shortcodeObjectToString())
		.end((err, res) => {
			shortcodeState.isFetching = false;

			if (err) {
				console.error(err);
			}

			addShortcodeToTheEditor(res.body.shortcode, res.body.attributes);
		});
};

const cacheElements = () => {
	el.container = tools.getNodes('.bc-shortcode-ui__actions', false, document, true)[0];
	el.shortcodeButton = tools.getNodes('bc-shortcode-ui-embed-button', false, el.container, false)[0];
};

const bindEvents = () => {
	delegate(el.container, '[data-js="bc-shortcode-ui-embed-button"]', 'click', generateShortcode);
};

const init = () => {
	cacheElements();
	bindEvents();
};

export default init;
