<?php


namespace BigCommerce\Analytics;


use BigCommerce\Settings\Sections\Analytics;

class Segment {
	/**
	 * @return void
	 * @action wp_head
	 */
	public function render_tracking_code() {
		$key = get_option( Analytics::SEGMENT );
		$settings = $this->get_settings();
		?>
		<!-- Segment Analytics Code -->
		<script type="text/javascript" data-js="bc-segment-tracker">
			!function(){var analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.loadPlatform=function(t,e,a,o){window[o||"analytics"]=analytics;window._analytics_js_global_name=o;analytics.platformSettings=e;analytics.platformPlan=a;var n=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.com/analytics.js/v1";t&&(n+="/"+t);var r=document.createElement("script");r.type="text/javascript";r.async=!0;r.src=n+"/platform/analytics.min.js";var i=document.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)};analytics.SNIPPET_VERSION="4.0.0_platform";
				analytics.loadPlatform(<?php echo ( $key ? wp_json_encode( $key ) : 'null' ); ?>, <?php echo wp_json_encode( $settings ) ?>,{},"analytics");
				analytics.page()
			}}();
		</script>
		<!-- End Segment Analytics Code -->
		<?php
	}

	private function get_settings() {
		$settings = [];

		$google = get_option( Analytics::GOOGLE_ANALYTICS );
		if ( $google ) {
			$settings[ 'Google Analytics' ] = [
				'enhancedEcommerce' => true,
				'includeSearch'     => true,
				'nonInteration'     => false,
				'sendUserId'        => true,
				'trackingId'        => $google,
			];
		}

		$facebook = get_option( Analytics::FACEBOOK_PIXEL );
		if ( $facebook ) {
			$settings[ 'Facebook Pixel' ] = [
				'pixelId'                => $facebook,
				'initWithExistingTraits' => true,
			];
		}

		/**
		 * Filter the configuration object passed to Segment
		 */
		$settings = apply_filters( 'bigcommerce/analytics/segment/settings', $settings );
		return (object) $settings;
	}

}
