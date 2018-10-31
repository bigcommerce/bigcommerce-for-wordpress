<?php


namespace BigCommerce\Analytics;


use BigCommerce\Settings\Sections\Analytics;

class Google_Analytics {
	/**
	 * @return void
	 * @action wp_head
	 */
	public function render_tracking_code() {
		$code = get_option( Analytics::GOOGLE_ANALYTICS );
		if ( empty( $code ) ) {
			return;
		}
		?>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo urlencode( $code ); ?>"></script>
		<script data-js="bc-ga-tracker">
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?php echo urlencode( $code ); ?>');
		</script>

		<?php
	}
}