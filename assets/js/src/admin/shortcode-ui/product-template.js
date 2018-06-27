/**
 * @module Product Template
 * @description Return product HTML from json results.
 */

import { I18N } from '../config/i18n';

/**
 * @function productTemplate
 * @description Render the product card html with json response data.
 * @param productData json obj
 * @returns {string}
 */
export const productTemplate = productData => (
	`
		<div class="bc-shortcode-ui__product${productData.selected}" data-product="${productData.bcid}">
			<div class="bc-shortcode-ui__product-inner">
				<figure class="bc-shortcode-ui__product-image ${productData.classes}" style="background-image: url(${productData.image});"></figure>
				<div class="bc-shortcode-ui__product-meta">
					<h3 class="bc-shortcode-ui__product-title">${productData.title}</h3>
					<span class="bc-shortcode-ui__product-price">${productData.price}</span>
				</div>
				<div class="bc-shortcode-ui__product-description">${productData.desc}</div>
				<i class="bc-icon icon-bc-selected"></i>
			</div>
			<button type="button" class="bc-shortcode-ui__product-anchor" data-js="add-remove-product" data-postid="${productData.id}" data-bcid="${productData.bcid}" data-title="${productData.title}" data-price="${productData.price}">
				<span class="bc-shortcode-ui__product-anchor-status">${productData.button_text}</span>
			</button>
		</div>
	`
);

export const selectedProduct = productData => (
	`
		<li class="bc-shortcode-ui__selected-product" data-product="${productData.bcid}" data-postid="${productData.id}">
			<h5 class="bc-shortcode-ui__selected-product-title">${productData.title} ${productData.price}</h5>
			<span class="bc-shortcode-ui__selected-product-id" data-bcid="${productData.bcid}">${I18N.text.id_prefix} ${productData.bcid}</span>
			<button type="button" class="bc-shortcode-ui__remove-selected" data-js="remove-product" data-bcid="${productData.bcid}" data-postid="${productData.id}">${I18N.buttons.remove_selected}</button>
		</li>
	`
);
