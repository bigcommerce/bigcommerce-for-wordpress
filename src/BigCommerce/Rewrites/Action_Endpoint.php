<?php


namespace BigCommerce\Rewrites;


class Action_Endpoint {
	const QUERY_VAR = 'bigcommerce_action';

	/**
	 * @return void
	 * @action init
	 */
	public function register_route() {
		add_rewrite_endpoint( 'bigcommerce', EP_ROOT, self::QUERY_VAR );
	}

	/**
	 * @param \WP $wp
	 *
	 * @return void
	 * @action parse_request
	 */
	public function handle_request( $wp ) {
		if ( empty( $wp->query_vars[ self::QUERY_VAR ] ) ) {
			return;
		}
		$args     = explode( '/', $wp->query_vars[ self::QUERY_VAR ] );
		$endpoint = array_shift( $args );
		do_action( 'bigcommerce/action_endpoint/' . $endpoint, $args );
	}
}