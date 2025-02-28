<?php
/**
 * Template to display next onboarding steps on the settings screen (Open Source Version)
 *
 * @var array $required
 * @var array $optional
 * @var bool $new_account
 */

printf( '<div class="complete__next-steps-header"><i class="icon icon-authenticate"></i><h2 class="next-step-heading">%s</h2><p class="complete__new-account-email">%s</p></div>',
	$new_account ? __( 'Authenticate and Confirm your Open Source Account', 'opensource' ) : __( 'Next Steps', 'opensource' ),
	$new_account ? __( 'Check your email for a link to confirm your account.', 'opensource' ) : __( 'Complete the steps below to optimize your Open Source Plugin experience.', 'opensource' )
);

if ( ! empty( $required ) ) {
	printf( '<h3 class="next-steps__section-title">%s</h3>', esc_html( __( 'Required Steps', 'opensource' ) ) );
	foreach ( $required as $key => $step ) {
		printf( '<div class="next-step-wrapper required-step %s">', sanitize_html_class( 'next-step--' . $key ) );
		printf( '<div class="next-step-status-wrapper">' );
		printf( '<i class="icon %s"></i> <span class="next-step-incomplete">%s</span>', sanitize_html_class( 'icon-' . $step['icon'] ), esc_html( __( 'Incomplete', 'opensource' ) ) );
		printf( '</div>' );
		printf( '<h3 class="h2 next-step-heading">%s</h3>', esc_html( $step['heading'] ) );
		if ( ! empty( $step['url'] ) && ! empty( $step['label'] ) ) {
			printf( '<a href="%s" class="next-step-cta" target="_blank" rel="noopener">%s</a>', esc_url( $step['url'] ), esc_html( $step['label'] ) );
		}
		echo '</div>';
	}
}

if ( ! empty( $optional ) ) {
	printf( '<h3 class="next-steps__section-title">%s</h3>', esc_html( __( 'Optional Steps', 'opensource' ) ) );
	foreach ( $optional as $key => $step ) {
		printf( '<div class="next-step-wrapper optional-step %s">', sanitize_html_class( 'next-step--' . $key ) );
		printf( '<div class="next-step-status-wrapper">' );
		printf( '<i class="icon %s"></i> <span class="next-step-incomplete">%s</span>', sanitize_html_class( 'icon-' . $step['icon'] ), esc_html( __( 'Incomplete', 'opensource' ) ) );
		printf( '</div>' );
		printf( '<h3 class="h2 next-step-heading">%s</h3>', esc_html( $step['heading'] ) );
		if ( ! empty( $step['url'] ) && ! empty( $step['label'] ) ) {
			printf( '<a href="%s" class="next-step-cta" target="_blank" rel="noopener">%s</a>', esc_url( $step['url'] ), esc_html( $step['label'] ) );
		}
		echo '</div>';
	}
}
