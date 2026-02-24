<?php


namespace BigCommerce\Api;

/**
 * Class Banners_Api
 *
 * Retrieves banner data from the Banners v2 API collection. This class allows you 
 * to fetch banners and their associated details, such as name, content, location, 
 * and visibility.
 *
 * @package BigCommerce\Api
 */
class Banners_Api extends v2ApiAdapter {

    /**
     * Fetches a list of banners from the Banners v2 API.
     *
     * This method retrieves the banners collection from the API and maps each banner 
     * to a simplified array format containing its relevant data such as ID, name, content, 
     * item ID, location, creation date, visibility, and more.
     *
     * @return array Returns an array of banners with the following keys:
     *               - 'id' (int)
     *               - 'name' (string)
     *               - 'content' (string)
     *               - 'page' (string)
     *               - 'item_id' (int)
     *               - 'location' (string)
     *               - 'date_created' (int)
     *               - 'date_type' (string)
     *               - 'date_from' (int)
     *               - 'date_to' (int)
     *               - 'visible' (bool)
     */
    public function get_banners() {
        return array_map( function ( $banner ) {
            return [
                'id'           => (int) $banner->id,
                'name'         => $banner->name,
                'content'      => $banner->content,
                'page'         => $banner->page,
                'item_id'      => (int) $banner->item_id,
                'location'     => $banner->location,
                'date_created' => (int) $banner->date_created,
                'date_type'    => $banner->date_type,
                'date_from'    => (int) $banner->date_from,
                'date_to'      => (int) $banner->date_to,
                'visible'      => (bool) $banner->visible,
            ];

        }, $this->getCollection( '/banners' ) ?: [] );
    }
}
