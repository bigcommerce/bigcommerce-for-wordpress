<?php

namespace BigCommerce\Settings\Sections;


trait Webhooks {

	public function enable_webhooks_toggle() {
		$current = get_option( Import::ENABLE_WEBHOOKS, 0 );

		printf( '<p class="description">%s</p>', esc_html( __( 'Would you like to enable webhooks? Please be advised that some hosts do not handle this increased level of traffic well.', 'bigcommerce' ) ) );

		echo '<fieldset>';
		printf(
			'<p><label><input type="radio" name="%s" value="1" %s /> %s</label></p>',
			esc_attr( Import::ENABLE_WEBHOOKS ),
			checked( 1, (int) $current, false ),
			esc_html( __( 'Yes, I frequently update my catalog or require real-time product and inventory sync.', 'bigcommerce' ) )
		);
		printf(
			'<p><label><input type="radio" name="%s" value="0" %s /> %s</label></p>',
			esc_attr( Import::ENABLE_WEBHOOKS ),
			checked( 0, (int) $current, false ),
			esc_html( __( "No, please disable Webhooks.", 'bigcommerce' ) )
		);
		echo '</fieldset>';
	}

}