<?php


namespace BigCommerce\Forms;

/**
 * Handles redirection after a form submission.
 */
class Form_Redirect {

	/**
	 * Redirects the user to a given URL after a form submission.
	 *
	 * This method applies a filter to modify the redirect URL and triggers actions before performing the redirect.
	 *
	 * @param string $url The destination URL for the redirect.
	 *
	 * @return void
	 * @action bigcommerce/form/before_redirect
	 * @filter bigcommerce/form/redirect_url
	 */
	public function redirect( $url ) {
		/**
		 * Filter the redirect URL after a form submission.
		 * Return `false` to abort the redirect.
		 *
		 * @param string $url The destination URL of the redirect.
		 */
		$url = apply_filters( 'bigcommerce/form/redirect_url', $url );
		if ( empty( $url ) ) {
			return;
		}

		/**
		 * Fires immediately before redirecting the user after a form submission.
		 *
		 * @param string $url The destination URL of the redirect.
		 */
		do_action( 'bigcommerce/form/before_redirect', $url );
		wp_safe_redirect( esc_url_raw( $url ) );
		exit();
	}
}