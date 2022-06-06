<?php

namespace BigCommerce\Webhooks\Product;

use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Import\Importers\Products\Product_Importer;
use BigCommerce\Import\Importers\Products\Product_Remover;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Taxonomies\Channel\Channel;

class Channels_UnAssign extends Channels_Manager {

	public function handle_request( $product_id, $channel_id ) {
		$channel = $this->get_channel( $channel_id );

		if ( empty( $channel ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Requested channel does not exist', 'bigcommerce' ), [
				'channel_id' => $channel_id,
			], 'webhooks' );

			return;
		}

		$remover = new Product_Remover();
		$remover->remove_by_product_id( $product_id, $channel );
	}

}
