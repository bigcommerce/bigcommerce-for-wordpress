<?php
/**
 * Template to display next onboarding steps on the settings screen
 *
 * @var array $required
 * @var array $optional
 * @var bool $new_account
 */

printf( '<div class="bc-complete__next-steps-header"><i class="bc-icon icon-bc-authenticate"></i><h2 class="bc-next-step-heading">%s</h2><p class="bc-complete__new-account-email">%s</p></div>',
	$new_account ? __( 'Authenticate and Confirm your BigCommerce Account', 'bigcommerce' ) : __( 'Next Steps', 'bigcommerce' ),
	$new_account ? __( 'Check your email for a link to confirm your account.', 'bigcommerce' ) : __( 'Complete the steps below to optimize your BigCommerce for WordPress experience.', 'bigcommerce' )
);

if ( ! empty( $required ) ) {
	printf( '<h3 class="bc-next-steps__section-title">%s</h3>', esc_html( __( 'Required Steps', 'bigcommerce' ) ) );
	foreach ( $required as $key => $step ) {
		printf( '<div class="bc-next-step-wrapper bc-required-step %s">', sanitize_html_class( 'bc-next-step--' . $key ) );
		printf( '<div class="bc-next-step-status-wrapper">' );
		printf( '<i class="bc-icon %s"></i> <span class="bc-next-step-incomplete">%s</span>', sanitize_html_class( 'icon-bc-' . $step['icon'] ), esc_html( __( 'Incomplete', 'bigcommerce' ) ) );
		printf( '</div>' );
		printf( '<h3 class="h2 bc-next-step-heading">%s</h3>', esc_html( $step['heading'] ) );
		if ( ! empty( $step['url'] ) && ! empty( $step['label'] ) ) {
			printf( '<a href="%s" class="bc-next-step-cta" target="_blank" rel="noopener">%s</a>', esc_url( $step['url'] ), esc_html( $step['label'] ) );
		}
		echo '</div>';
	}
}


if ( ! empty( $optional ) ) {
	printf( '<h3 class="bc-next-steps__section-title">%s</h3>', esc_html( __( 'Optional Steps', 'bigcommerce' ) ) );
	foreach ( $optional as $key => $step ) {
		printf( '<div class="bc-next-step-wrapper bc-optional-step %s">', sanitize_html_class( 'bc-next-step--' . $key ) );
		printf( '<div class="bc-next-step-status-wrapper">' );
		printf( '<i class="bc-icon %s"></i> <span class="bc-next-step-incomplete">%s</span>', sanitize_html_class( 'icon-bc-' . $step['icon'] ), esc_html( __( 'Incomplete', 'bigcommerce' ) ) );
		printf( '</div>' );
		printf( '<h3 class="h2 bc-next-step-heading">%s</h3>', esc_html( $step['heading'] ) );
		if ( ! empty( $step['url'] ) && ! empty( $step['label'] ) ) {
			printf( '<a href="%s" class="bc-next-step-cta" target="_blank" rel="noopener">%s</a>', esc_url( $step['url'] ), esc_html( $step['label'] ) );
		}
		echo '</div>';
	}
}
