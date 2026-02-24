<?php


namespace BigCommerce\Import;

use BigCommerce\Assets\Theme\Image_Sizes;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Settings\Sections\Import;

/**
 * Class Image_Importer
 *
 * Imports an image from a URL and attaches it to a post
 */
class Image_Importer {
	const SOURCE_URL           = 'bigcommerce_source_url';
	const URL_ZOOM             = 'url_zoom';
	const URL_STD              = 'url_standard';
	const URL_THUMB            = 'url_thumbnail';
	const URL_TINY             = 'url_tiny';
	const IMAGE_ALT            = 'image_alt';
	const FULL_IMAGE_IMPORT    = 'bigcommerce_allow_full_image_import';
	const CDN_IMAGE_IMPORT     = 'bigcommerce_allow_cdn_image_import';
	const DISABLE_IMAGE_IMPORT = 'bigcommerce_disable_image_import';

	const MIMES = [
		IMAGETYPE_GIF     => 'image/gif',
		IMAGETYPE_JPEG    => 'image/jpg',
		IMAGETYPE_PNG     => 'image/png',
		IMAGETYPE_SWF     => 'image/swf',
		IMAGETYPE_PSD     => 'image/psd',
		IMAGETYPE_BMP     => 'image/bmp',
		IMAGETYPE_TIFF_II => 'image/tiff',
		IMAGETYPE_TIFF_MM => 'image/tiff',
		IMAGETYPE_JPC     => 'image/jpc',
		IMAGETYPE_JP2     => 'image/jp2',
		IMAGETYPE_JPX     => 'image/jpx',
		IMAGETYPE_JB2     => 'image/jb2',
		IMAGETYPE_SWC     => 'image/swc',
		IMAGETYPE_IFF     => 'image/iff',
		IMAGETYPE_WBMP    => 'image/wbmp',
		IMAGETYPE_XBM     => 'image/xbm',
		IMAGETYPE_ICO     => 'image/ico',
	];

	private $image_url;
	private $attach_to_post_id;

	public function __construct( $image_url, $attach_to_post_id = 0 ) {
		$this->image_url         = $image_url;
		$this->attach_to_post_id = $attach_to_post_id;
	}

	/**
	 * @param false $is_category
	 *
	 * @return false|int|\WP_Error
	 */
	public function import( $is_category = false ) {
		$this->require_files();

		if ( self::should_load_from_cdn() && ! $is_category ) {
			return $this->process_cdn_items();
		}

		$tmp = download_url( $this->image_url );
		if ( is_wp_error( $tmp ) ) {
			/**
			 * Action to log messages during the BigCommerce logging process.
			 *
			 * This action is triggered when a log entry needs to be created. It logs the message, context, level, and path to the log file.
			 *
			 * @param string $level The log level (e.g., INFO, ERROR).
			 * @param string $message The log message to be recorded.
			 * @param array $context Additional context for the log entry.
			 * @param string $path The path where the log is being written.
			 * @return void
			 */
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
			if ( file_exists( $tmp ) ) {
				unlink( $tmp );
			}

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

	/**
	 * Check if images serving from BigCommerce CDN is enabled
	 *
	 * @return bool
	 */
	public static function should_load_from_cdn() {
		return get_option( Import::ENABLE_IMAGE_IMPORT ) === self::CDN_IMAGE_IMPORT;
	}

	/**
	 * Check whether image import is enabled
	 * @return bool
	 */
	public static function is_image_import_allowed() {
		return get_option( Import::ENABLE_IMAGE_IMPORT ) !== self::DISABLE_IMAGE_IMPORT;
	}

	/**
	 * Is featured image exists only on WP side
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public static function has_local_featured_image( $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$thumb_id = get_post_thumbnail_id( $post_id );

		if ( empty( $thumb_id ) ) {
			return false;
		}

		$thumbnail_bc_id = get_post_meta( $thumb_id, 'bigcommerce_id', true );

		// Local images doesn't have BC id
		if ( ! empty( $thumbnail_bc_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return false|int|\WP_Error
	 */
	private function process_cdn_items() {
		$path = parse_url( $this->image_url, PHP_URL_PATH );
		$name = basename( $path );

		$attachment = [
			'guid'           => $this->image_url,
			'post_title'     => $name,
			'post_status'    => 'inherit',
			'post_mime_type' => $this->get_image_mime_type(),
			'post_type'      => 'attachment',
		];

		try {
			$image_id = wp_insert_attachment( $attachment, $name, $this->attach_to_post_id );

			if ( is_wp_error( $image_id ) || empty( $image_id ) ) {
				return false;
			}

			update_post_meta( $image_id, self::SOURCE_URL, $this->image_url );

			return $image_id;
		} catch ( \Exception $exception ) {
			do_action( 'bigcommerce/import/log', Error_Log::NOTICE, __( 'Failed to save CDN image', 'bigcommerce' ), [
					'url'   => $this->image_url,
					'error' => $exception->getMessage(),
			] );

			return false;
		}
	}

	/**
	 * Get image mime type
	 *
	 * @return string
	 */
	private function get_image_mime_type() {
		try {
			if ( empty( $this->image_url ) ) {
				return '';
			}

			$image_type = exif_imagetype( $this->image_url );

			if ( array_key_exists( $image_type, self::MIMES ) ) {
				return self::MIMES[ $image_type ];
			}

			return '';
		} catch ( \Exception $exception ) {
			do_action( 'bigcommerce/import/log', Error_Log::NOTICE, __( 'Failed to get CDN image mime type', 'bigcommerce' ), [
					'url'   => $this->image_url,
					'error' => $exception->getMessage(),
			] );

			return '';
		}
	}
}
