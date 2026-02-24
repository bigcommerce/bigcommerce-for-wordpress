<?php


namespace BigCommerce\Analytics;


use BigCommerce\Settings\Sections\Analytics;

/**
 * Class Google_Analytics
 *
 * Renders the Google Analytics tracking code on the site.
 *
 * This class is responsible for injecting the Google Analytics tracking code into the 
 * `<head>` section of the website. The tracking code is used to gather analytics 
 * data about site visitors, including traffic and user behavior. The tracking code 
 * is only added if a valid Google Analytics ID is set in the settings.
 *
 * @package BigCommerce\Analytics
 */
class Google_Analytics {
    
    /**
     * Renders the Google Analytics tracking code in the site's head section.
     *
     * This function retrieves the Google Analytics tracking ID from the site settings 
     * and generates the necessary JavaScript code for integrating Google Analytics tracking. 
     * The script is added to the `<head>` section of the webpage using the `wp_head` action hook.
     * If no Google Analytics ID is set, the function does nothing.
     *
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
