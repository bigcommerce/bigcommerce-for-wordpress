<?php


namespace BigCommerce\Api;

/**
 * Class Banners_Api
 *
 * Get banners data from banners v2 api collection
 *
 * @package BigCommerce\Api
 */
class Banners_Api extends v2ApiAdapter {

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
