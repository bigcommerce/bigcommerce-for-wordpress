<?php


namespace BigCommerce\Import;

/**
 * Class Image_Importer
 *
 * Imports an image from a URL and attaches it to a post
 */
class Image_Importer {
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

			return false;
		}

		update_post_meta( $image_id, 'bigcommerce_source_url', $this->image_url );

		return $image_id;
	}

	private function require_files() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
}