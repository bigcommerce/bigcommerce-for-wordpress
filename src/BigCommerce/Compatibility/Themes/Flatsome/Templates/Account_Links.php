<?php


namespace BigCommerce\Compatibility\Themes\Flatsome\Templates;

use BigCommerce\Templates\Controller;
use BigCommerce\Pages\Orders_Page;
use BigCommerce\Pages\Address_Page;
use BigCommerce\Pages\Wishlist_Page;
use BigCommerce\Settings\Sections\Wishlists as Wishlist_Settings;

/**
 * This class is responsible for generating the account links for the user's account page in the Flatsome theme. 
 * It provides links to the order history, addresses, and wish lists (if enabled), and manages the data 
 * passed to the template for rendering.
 *
 * @package BigCommerce
 * @subpackage Compatibility\Themes\Flatsome\Templates
 */
class Account_Links extends Controller {
    /**
     * Constant to define the key for links in the data array.
     */
    const LINKS = 'links';

    /**
     * The template file used to render the account links.
     *
     * @var string
     */
    protected $template = 'compatibility/themes/flatsome/myaccount/account-links.php';

    /**
     * Merges the provided options with default values.
     *
     * This method takes an array of options and merges it with the default options 
     * before returning the final options array.
     *
     * @param array $options The options to merge with defaults.
     *
     * @return array The merged options.
     */
    protected function parse_options( array $options ) {
        $defaults = [];

        return wp_parse_args( $options, $defaults );
    }

    /**
     * Retrieves the data to be passed to the template.
     *
     * This method prepares the data array to include the account links and any other necessary options 
     * for the template rendering.
     *
     * @return array The data array, including the account links.
     */
    public function get_data() {
        $data                = $this->options;
        $data[ self::LINKS ] = $this->get_links();
        return $data;
    }

    /**
     * Generates the links for the user's account page.
     *
     * This method creates an array of links, including order history, addresses, and wish lists (if enabled), 
     * and returns the array of links to be displayed in the template.
     *
     * @return array An array of links with titles and URLs.
     */
    protected function get_links() {
        $links = [
            [
                'title' => __( 'Order History', 'bigcommerce'),
                'url'   => get_permalink( get_option( Orders_Page::NAME, 0 ) ),
            ],
            [
                'title' => __( 'Addresses', 'bigcommerce'),
                'url'   => get_permalink( get_option( Address_Page::NAME, 0 ) ),
            ],
        ];

        if ( get_option( Wishlist_Settings::ENABLED ) ) {
            $links[] = [
                'title' => __( 'Wish Lists', 'bigcommerce'),
                'url'   => get_permalink( get_option( Wishlist_Page::NAME, 0 ) ),
            ];
        }

        return $links;
    }

}