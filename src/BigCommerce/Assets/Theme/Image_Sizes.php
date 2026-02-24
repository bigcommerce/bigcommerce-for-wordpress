<?php

namespace BigCommerce\Assets\Theme;

/**
 * Class Image_Sizes
 *
 * Registers custom image sizes for the BigCommerce theme, providing predefined sizes
 * for product images, thumbnails, and category images. The version constant is incremented
 * when any changes are made to the image sizes list.
 *
 * @package BigCommerce\Assets\Theme
 */
class Image_Sizes {
    /**
     * @var int VERSION The current version of the image sizes list.
     */
    const VERSION = 2;

    /**
     * @var string STATE_META The meta key used for storing image size state.
     */
    const STATE_META = 'bigcommerce_sizing';

    /**
     * @var string BC_THUMB Thumbnail image size identifier.
     *
     * Represents a small thumbnail size (86x86), typically used for small product
     * images or icons.
     */
    const BC_THUMB = 'bc-thumb';

    /**
     * @var string BC_THUMB_LARGE Large thumbnail image size identifier.
     *
     * Represents a larger thumbnail size (167x167), typically used for more prominent
     * image previews in product listings or category pages.
     */
    const BC_THUMB_LARGE = 'bc-thumb-large';

    /**
     * @var string BC_SMALL Small image size identifier.
     *
     * Represents a small image size (270x270), typically used for product images or
     * other small elements that require a compact display.
     */
    const BC_SMALL = 'bc-small';

    /**
     * @var string BC_MEDIUM Medium image size identifier.
     *
     * Represents a medium image size (370x370), typically used for product images
     * in the middle range of display sizes, balancing quality and loading speed.
     */
    const BC_MEDIUM = 'bc-medium';

    /**
     * @var string BC_EXTRA_MEDIUM Extra medium image size identifier.
     *
     * Represents an extra medium image size (960x960), typically used for larger
     * product images or more detailed views in product galleries.
     */
    const BC_EXTRA_MEDIUM = 'bc-xmedium';

    /**
     * @var string BC_LARGE Large image size identifier.
     *
     * Represents a large image size (1280x1280), typically used for high-quality
     * images or larger product detail views that require high resolution.
     */
    const BC_LARGE = 'bc-large';

    /**
     * @var string BC_CATEGORY_IMAGE Category image size identifier.
     *
     * Represents a category image size (1600x0). The height is set to 0, allowing
     * for responsive width adjustments while keeping the height auto-adjusted.
     * Typically used for large images representing categories or banners.
     */
    const BC_CATEGORY_IMAGE = 'bc-category-image';

	// Increment self::VERSION above when adding/changing this list
    /**
     * List of custom image sizes, each defined by width, height, and cropping settings.
     *
     * The size array defines different image sizes, including thumbnail, small, medium,
     * large, and category images. The cropping property determines whether the image should
     * be cropped to fit the dimensions or not.
     *
     * @var array
     */
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
     * Registers custom image sizes with WordPress.
     *
     * This method loops through the defined image sizes and registers each one using
     * the `add_image_size` function. The sizes are registered when the theme is set up.
     *
     * @return void
     * @action after_setup_theme
     */
    public function register_sizes() {
        foreach ( $this->sizes as $key => $attributes ) {
            add_image_size( $key, $attributes['width'], $attributes['height'], $attributes['crop'] );
        }
    }
}
