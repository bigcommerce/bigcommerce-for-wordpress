<?php


namespace BigCommerce\Assets\Theme;


class Image_Sizes {
	const VERSION    = 2;
	const STATE_META = 'bigcommerce_sizing';

	const BC_THUMB          = 'bc-thumb';
	const BC_THUMB_LARGE    = 'bc-thumb-large';
	const BC_SMALL          = 'bc-small';
	const BC_MEDIUM         = 'bc-medium';
	const BC_EXTRA_MEDIUM   = 'bc-xmedium';
	const BC_LARGE          = 'bc-large';
	const BC_CATEGORY_IMAGE = 'bc-category-image';

	// Increment self::VERSION above when adding/changing this list
	private $sizes = [
		self::BC_THUMB        => [
			'width'  => 86,
			'height' => 86,
			'crop'   => true,
		],
		self::BC_THUMB_LARGE  => [
			'width'  => 167,
			'height' => 167,
			'crop'   => true,
		],
		self::BC_SMALL        => [
			'width'  => 270,
			'height' => 270,
			'crop'   => true,
		],
		self::BC_MEDIUM       => [
			'width'  => 370,
			'height' => 370,
			'crop'   => true,
		],
		self::BC_EXTRA_MEDIUM => [
			'width'  => 960,
			'height' => 960,
			'crop'   => true,
		],
		self::BC_LARGE        => [
			'width'  => 1280,
			'height' => 1280,
			'crop'   => true,
		],
		self::BC_CATEGORY_IMAGE => [
			'width'  => 1600,
			'height' => 0,
			'crop'   => false,
		],
	];

	/**
	 * @return void
	 * @action after_setup_theme
	 */
	public function register_sizes() {
		foreach ( $this->sizes as $key => $attributes ) {
			add_image_size( $key, $attributes['width'], $attributes['height'], $attributes['crop'] );
		}
	}
}
