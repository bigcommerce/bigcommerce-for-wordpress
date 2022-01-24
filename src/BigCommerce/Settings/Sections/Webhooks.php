<?php

namespace BigCommerce\Settings\Sections;

trait Webhooks {

	public function enable_products_webhooks_toggle() {
		$current = get_option( Import::ENABLE_PRODUCTS_WEBHOOKS, 0 );

		printf( '<p class="description">%s</p>', esc_html( __( 'Would you like to enable webhooks? Please be advised that some hosts do not handle this increased level of traffic well.', 'bigcommerce' ) ) );
		printf( '<p class="description">%s</p>', esc_html( __( 'Note: If you are finding that products are not automatically updating via webhooks, disable this feature and then re-enable it to reset the webhook subscription.', 'bigcommerce' ) ) );

		$labels = [
			__( 'Yes, I frequently update my catalog or require real-time product and inventory sync.', 'bigcommerce' ),
			__( "No, please disable Webhooks.", 'bigcommerce' ),
		];

		$this->render_settings_fields( $current, Import::ENABLE_PRODUCTS_WEBHOOKS, $labels );
	}

	public function enable_customer_webhooks_toggle() {
		$current = get_option( Import::ENABLE_CUSTOMER_WEBHOOKS, 0 );

		printf( '<p class="description">%s</p>', esc_html( __( 'Would you like to enable webhooks? Please be advised that some hosts do not handle this increased level of traffic well.', 'bigcommerce' ) ) );
		printf( '<p class="description">%s</p>', esc_html( __( 'Note: If you are finding that customers are not automatically updating via webhooks, disable this feature and then re-enable it to reset the webhook subscription.', 'bigcommerce' ) ) );

		$labels = [
				__( 'Yes, I require real-time customers sync.', 'bigcommerce' ),
				__( "No, please disable Webhooks.", 'bigcommerce' ),
		];

		$this->render_settings_fields( $current, Import::ENABLE_CUSTOMER_WEBHOOKS, $labels );
	}

	private function render_settings_fields( $current, $option_name, $labels = [] ) {
		echo '<fieldset>';
		printf(
			'<p><label><input type="radio" name="%s" value="1" %s /> %s</label></p>',
			esc_attr( $option_name ),
			checked( 1, (int) $current, false ),
			esc_html( $labels[0] )
		);
		printf(
			'<p><label><input type="radio" name="%s" value="0" %s /> %s</label></p>',
			esc_attr( $option_name ),
			checked( 0, (int) $current, false ),
			esc_html( $labels[1] )
		);
		echo '</fieldset>';
	}

}
