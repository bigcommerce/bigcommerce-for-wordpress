<?php


namespace BigCommerce\Analytics;


use BigCommerce\Settings\Sections\Analytics;

/**
 * Class Facebook_Pixel
 *
 * Renders the Facebook Pixel tracking code on the site.
 *
 * This class is responsible for injecting the Facebook Pixel tracking code into the 
 * `<head>` section of the website. The tracking code is used to track user interactions
 * for the purpose of running analytics and ad targeting on Facebook.
 * The tracking code is only added if a valid Facebook Pixel ID is set in the settings.
 *
 * @package BigCommerce\Analytics
 */
class Facebook_Pixel {
    
    /**
     * Renders the Facebook Pixel tracking code in the site's head section.
     *
     * This function retrieves the Facebook Pixel ID from the site settings and generates 
     * the necessary JavaScript code for integrating Facebook Pixel tracking. The script is 
     * added to the `<head>` section of the webpage using the `wp_head` action hook.
     * If no Facebook Pixel ID is set, the function does nothing.
     *
     * @return void
     * @action wp_head
     */
    public function render_tracking_code() {
        $code = get_option( Analytics::FACEBOOK_PIXEL );

        if ( empty( $code ) ) {
            return;
        }

        ?>
        <!-- Facebook Pixel Code -->
        <script data-js="bc-facebook-pixel">
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo urlencode( $code ); ?>');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                       src="https://www.facebook.com/tr?id=<?php echo urlencode( $code ); ?>&ev=PageView&noscript=1"
            /></noscript>
        <!-- End Facebook Pixel Code -->
        <?php
    }
}
