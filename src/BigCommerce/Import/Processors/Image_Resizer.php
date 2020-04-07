<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

class Image_Resizer implements Import_Processor {

	/**
	 * @var int
	 */
	private $limit;

	public function __construct( $limit = 20 ) {
		$this->limit = $limit;
	}

	public function run() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$status = new Status();
		$status->set_status( Status::RESIZING_IMAGES );

		$sql = "SELECT SQL_CALC_FOUND_ROWS p.ID FROM {$wpdb->posts} p
		        INNER JOIN {$wpdb->postmeta} bcid ON p.ID=bcid.post_id AND bcid.meta_key='bigcommerce_id'
		        LEFT JOIN {$wpdb->postmeta} version ON p.ID=version.post_id AND version.meta_key=%s
		        WHERE p.post_type='attachment' AND ( version.meta_value != %s OR version.meta_value IS NULL )
		        LIMIT %d";

		$query = $wpdb->prepare( $sql, Image_Sizes::STATE_META, Image_Sizes::VERSION, $this->limit );

		$image_ids = $wpdb->get_col( $query );

		$total_remaining = (int) $wpdb->get_var( "SELECT FOUND_ROWS()" );

		if ( empty( $image_ids ) ) {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'No images found requiring regeneration', 'bigcommerce' ), [] );
		} else {
			do_action( 'bigcommerce/log', Error_Log::DEBUG, __( 'Found images to regenerate', 'bigcommerce' ), [
				'batch' => $image_ids,
				'total' => $total_remaining,
			] );
			foreach ( $image_ids as $post_id ) {
				$this->regenerate_image( $post_id );
			}
		}

		if ( empty( $image_ids ) || $total_remaining <= $this->limit ) {
			$status->set_status( Status::RESIZED_IMAGES );
		}
	}

	private function regenerate_image( $post_id ) {
		// flag the version so we don't regenerate again
		update_post_meta( $post_id, Image_Sizes::STATE_META, Image_Sizes::VERSION );

		$fullsizepath = get_attached_file( $post_id );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
			do_action( 'bigcommerce/log', Error_Log::NOTICE, __( 'Image file does not exist. Skipping thumbnail regeneration.', 'bigcommerce' ), [
				'attachment_id' => $post_id,
				'path'          => $fullsizepath,
			] );

			return;
		}

		$metadata = wp_generate_attachment_metadata( $post_id, $fullsizepath );

		if ( is_wp_error( $metadata ) ) {
			do_action( 'bigcommerce/log', Error_Log::WARNING, __( 'Image regeneration failed.', 'bigcommerce' ), [
				'attachment_id' => $post_id,
				'error'         => $metadata->get_error_message(),
			] );

			return;
		}

		wp_update_attachment_metadata( $post_id, $metadata );
		do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Regenerated image thumbnails.', 'bigcommerce' ), [
			'attachment_id' => $post_id,
		] );
	}
}
