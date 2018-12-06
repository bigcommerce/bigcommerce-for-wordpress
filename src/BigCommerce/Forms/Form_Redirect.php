<?php


namespace BigCommerce\Forms;


class Form_Redirect {

	public function redirect( $url ) {
		/**
		 * Filter the redirect URL after a form submission.
		 * Return `false` to abort the redirect.
		 *
		 * @param string $url The destination URL of the redirect
		 */
		$url = apply_filters( 'bigcommerce/form/redirect_url', $url );
		if ( empty( $url ) ) {
			return;
		}

		do_action( 'bigcommerce/form/before_redirect', $url );
		wp_safe_redirect( esc_url_raw( $url ) );
		exit();
	}
}