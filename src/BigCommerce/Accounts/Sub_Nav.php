<?php


namespace BigCommerce\Accounts;


use BigCommerce\Pages\Account_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Templates\Sub_Nav_Links;

/**
 * Class Sub_Nav
 *
 * Responsible for rendering and saving profile settings, as well as managing the visibility of the sub-navigation menu 
 * above the content for account-related pages.
 */
class Sub_Nav {
    /**
     * Handle subnav visibility above the content
     *
     * This filter is applied to the page content and will insert the sub-navigation 
     * if the user is logged in and the page is part of the account-related pages.
     *
     * @param string $content The content of the page.
     * @return string Filtered content with subnav included if applicable.
     * @filter the_content
     */
    public function add_subnav_above_content( $content ) {
        if ( ! is_singular() || ! is_user_logged_in() ) {
            return $content;
        }
        $post_id = get_queried_object_id();
        if ( $post_id !== get_the_ID() ) {
            return $content; // don't filter if we're not on the main post
        }
        /**
         * Filter whether to display the account subnav before the post content
         * on account pages.
         *
         * @param bool $display True to display the subnav, false to skip it.
         * @param int  $post_id The ID of the current page.
         */
        if ( ! apply_filters( 'bigcommerce/account/do_subnav', true, $post_id ) ) {
            return $content;
        }
        switch ( $post_id ) {
            case get_option( Account_Page::NAME, 0 ):
            case get_option( Orders_Page::NAME, 0 ):
            case get_option( Address_Page::NAME, 0 ):
            case get_option( Wishlist_Page::NAME, 0 ):
                return $this->get_subnav() . $content;
            default:
                return $content;
        }
    }

    /**
     * Get and render the sub-navigation menu
     *
     * Renders the sub-navigation links component based on available account pages.
     *
     * @return string The HTML of the sub-navigation menu.
     */
    private function get_subnav() {
        $component = Sub_Nav_Links::factory( [
            Sub_Nav_Links::LINKS => $this->get_links(),
        ] );

        return $component->render();
    }

    /**
     * Get sub-navigation links
     *
     * Retrieves the links for the account-related pages and formats them for the sub-navigation menu.
     *
     * @return array The list of links to be displayed in the sub-navigation.
     */
    private function get_links() {
        $links = [];
        foreach ( [ Account_Page::NAME, Orders_Page::NAME, Address_Page::NAME, Wishlist_Page::NAME ] as $option ) {
            $post_id = get_option( $option, 0 );
            if ( $post_id ) {
                $links[] = [
                    'url'     => get_permalink( $post_id ),
                    'label'   => get_the_title( $post_id ),
                    'current' => ( $post_id == get_queried_object_id() ),
                ];
            }
        }

        /**
         * Filter the links that show in the account subnav.
         *
         * @param array[] $links Each link will have the properties:
         *                       `url` - The URL of the link.
         *                       `label` - The label of the link.
         *                       `current` - Whether the link is to the current page.
         */
        return apply_filters( 'bigcommerce/account/subnav/links', $links );
    }
}
