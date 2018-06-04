<?php


namespace BigCommerce\Assets\Theme;


class Image_Sizes {
	const BC_THUMB  = 'bc-thumb';
	const BC_SMALL  = 'bc-small';
	const BC_MEDIUM = 'bc-medium';
	const BC_LARGE  = 'bc-large';

	private $sizes = [
		self::BC_THUMB  => [
			'width'  => 86,
			'height' => 86,
			'crop'   => true,
		],
		self::BC_SMALL  => [
			'width'  => 270,
			'height' => 270,
			'crop'   => true,
		],
		self::BC_MEDIUM => [
			'width'  => 370,
			'height' => 370,
			'crop'   => true,
		],
		self::BC_LARGE  => [
			'width'  => 1280,
			'height' => 1280,
			'crop'   => true,
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
