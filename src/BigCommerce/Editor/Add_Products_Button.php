<?php

namespace BigCommerce\Editor;

class Add_Products_Button {
	const BC_LOGO        = 'bc-icon icon-bc-b-logo';
	const BUTTON_CLASSES = 'button bc-add-products';
	const BUTTON_TRIGGER = 'bc-add-products';
	const BUTTON_TARGET  = 'bc-shortcode-ui';

	public function render_button() {
		$label = __( 'Add Products', 'bigcommerce' );
		/**
		 * Filter the label of the Add Products button
		 *
		 * @param string $label The button label
		 */
		$label = apply_filters( 'bigcommerce/editor/shortcode_button/label', $label );

		return sprintf( '<button type="button" class="%s" data-js="%s" data-content="%s"><i class="%s"></i> %s</button>', self::BUTTON_CLASSES, self::BUTTON_TRIGGER, self::BUTTON_TARGET, self::BC_LOGO, $label );
	}
}
