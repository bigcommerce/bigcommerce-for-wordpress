<?php

namespace BigCommerce\Compatibility\Akismet;

use BigCommerce\Compatibility\Spam_Checker;

class Akismet implements Spam_Checker {

    /**
	 * @param array $submission
	 *
	 * @return boolean
	 */
	public function is_spam( array $submission ) {
        if ( ! function_exists( 'akismet_init' ) ) {
            return false;
        }
        
        $wpcom_api_key = get_option( 'wordpress_api_key' );

        if ( empty( $wpcom_api_key ) ) {
            return false;
        }

        global $akismet_api_host, $akismet_api_port;

        $response = akismet_http_post( $this->get_query_string( $submission ), $akismet_api_host, '/1.1/comment-check', $akismet_api_port );
        
        if ( $response[1] == 'true' ) {
            update_option( 'akismet_spam_count', get_option( 'akismet_spam_count' ) + 1 );
            return true;
        }
            
        return false;
    }
    
    private function get_query_string( $submission ) {
        $content['comment_author'] = '';
        if ( isset( $submission['first_name'] ) ) {
            $content['comment_author'] .= $submission['first_name'];
        }
        
        if ( isset( $submission['last_name'] ) ) {
            $content['comment_author'] .=  ' ' . $submission['last_name'];
        }
        $content['comment_author'] = trim( $content['comment_author'] );

        if ( isset( $submission['email'] ) ) {
            $content['comment_author_email'] = $submission['email'];
        }

        $server = filter_var_array( $_SERVER, [
            'REMOTE_ADDR'     => FILTER_VALIDATE_IP,
            'HTTP_USER_AGENT' => FILTER_SANITIZE_STRING,
            'HTTP_REFERER'    => FILTER_VALIDATE_URL,
        ] );

        // https://akismet.com/development/api/#comment-check
        // Set remaining required values for akismet api.
        $content['user_ip']      = preg_replace( '/[^0-9., ]/', '', $server['REMOTE_ADDR'] );
        $content['user_agent']   = $server['HTTP_USER_AGENT'];
        $content['referrer']     = $server['HTTP_REFERER'];
        $content['blog']         = get_option( 'home' );
        $content['comment_type'] = 'signup';
        
        if ( empty( $content['referrer'] ) ) {
            $content['referrer'] = get_permalink();
        }
        
        $query_string = '';
        
        foreach ( $content as $key => $data ) {
            if ( ! empty( $data ) ) {
                $query_string .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';
            }
        }

        return $query_string;
    }

}
