<?php


namespace BigCommerce\Import;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Logging\Error_Log;

/**
 * Class Image_Importer
 *
 * Imports an image from a URL and attaches it to a post
 */
class Image_Importer {
	const SOURCE_URL = 'bigcommerce_source_url';

	private $image_url;
	private $attach_to_post_id;

	public function __construct( $image_url, $attach_to_post_id = 0 ) {
		$this->image_url         = $image_url;
		$this->attach_to_post_id = $attach_to_post_id;
	}

	public function import() {
		$this->require_files();
		$tmp = download_url( $this->image_url );
		if ( is_wp_error( $tmp ) ) {
			do_action( 'bigcommerce/import/log', Error_Log::NOTICE, __( 'Failed to download image', 'bigcommerce' ), [
				'url'   => $this->image_url,
				'error' => $tmp->get_error_messages(),
			] );

			return false;
		}

		$path = parse_url( $this->image_url, PHP_URL_PATH );

		$file_array = [
			'name'     => basename( $path ),
			'tmp_name' => $tmp,
		];

		$image_id = media_handle_sideload( $file_array, $this->attach_to_post_id );
		if ( is_wp_error( $image_id ) ) {
			unlink( $tmp );

			do_action( 'bigcommerce/import/log', Error_Log::NOTICE, __( 'Failed to sideload image', 'bigcommerce' ), [
				'url'   => $this->image_url,
				'error' => $image_id->get_error_messages(),
			] );

			return false;
		}

		update_post_meta( $image_id, self::SOURCE_URL, $this->image_url );
		update_post_meta( $image_id, Image_Sizes::STATE_META, Image_Sizes::VERSION );

		return $image_id;
	}

	private function require_files() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
}
