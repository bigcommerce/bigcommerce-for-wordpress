<?php

namespace BigCommerce\Editor;

/**
 * A class to render the "Add Products" button in the editor.
 */
class Add_Products_Button {
	/**
	 * The CSS class for the BigCommerce logo icon.
	 *
	 * @var string
	 */
	const BC_LOGO        = 'bc-icon icon-bc-b-logo';

	/**
	 * The CSS class for the Add Products button.
	 *
	 * @var string
	 */
	const BUTTON_CLASSES = 'button bc-add-products';

	/**
	 * The trigger class for the Add Products button.
	 *
	 * @var string
	 */
	const BUTTON_TRIGGER = 'bc-add-products';

	/**
	 * The target class for the Add Products button.
	 *
	 * @var string
	 */
	const BUTTON_TARGET  = 'bc-shortcode-ui';

	/**
	 * Renders the Add Products button HTML.
	 *
	 * @return string The HTML for the button.
	 */
	public function render_button() {
		$label = __( 'Add Products', 'bigcommerce' );
		/**
		 * Filter the label of the Add Products button.
		 *
		 * @param string $label The button label.
		 */
		$label = apply_filters( 'bigcommerce/editor/shortcode_button/label', $label );

		return sprintf( '<button type="button" class="%s" data-js="%s" data-content="%s"><i class="%s"></i> %s</button>', self::BUTTON_CLASSES, self::BUTTON_TRIGGER, self::BUTTON_TARGET, self::BC_LOGO, $label );
	}
}
