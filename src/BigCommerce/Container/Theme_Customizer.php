<?php

namespace BigCommerce\Container;

use BigCommerce\Customizer\Panels;
use BigCommerce\Customizer\Sections;
use BigCommerce\Customizer\Styles;
use Pimple\Container;

/**
 * Registers theme customizer panels, sections, and styles in WordPress.
 *
 * This class provides functionality for setting up customizer configurations,
 * adding styles, and integrating with WordPress actions such as `customize_register`.
 */
class Theme_Customizer extends Provider {
	
	/**
	 * Identifier for the primary customizer panel.
	 *
	 * @var string
	 */
	const PANEL_PRIMARY = 'customizer.panel.primary';

	/**
	 * Identifier for the buttons customizer section.
	 *
	 * @var string
	 */
	const SECTION_BUTTONS = 'customizer.section.buttons';

	/**
	 * Identifier for the colors customizer section.
	 *
	 * @var string
	 */
	const SECTION_COLORS = 'customizer.section.colors';

	/**
	 * Identifier for the product single customizer section.
	 *
	 * @var string
	 */
	const SECTION_PRODUCT_SINGLE = 'customizer.section.product_single';

	/**
	 * Identifier for the product archive customizer section.
	 *
	 * @var string
	 */
	const SECTION_PRODUCT_ARCHIVE = 'customizer.section.product_archive';

	/**
	 * Identifier for the product categories customizer section.
	 *
	 * @var string
	 */
	const SECTION_PRODUCT_CATEGORIES = 'customizer.section.product_categories';

	/**
	 * Identifier for the cart customizer section.
	 *
	 * @var string
	 */
	const SECTION_CART = 'customizer.section.cart';

	/**
	 * Identifier for the checkout customizer section.
	 *
	 * @var string
	 */
	const SECTION_CHECKOUT = 'customizer.section.checkout';

	/**
	 * Identifier for the banners customizer section.
	 *
	 * @var string
	 */
	const SECTION_BANNERS = 'customizer.section.banners';

	/**
	 * Identifier for the customizer styles.
	 *
	 * @var string
	 */
	const STYLES = 'customizer.styles';

    /**
     * Registers customizer panels, sections, and styles in the Pimple container.
     *
     * @param Container $container Dependency injection container.
     *
     * @return void
     */
    public function register(Container $container) {
		/** 
		 * @return Panels\Primary The primary customizer panel instance.
		 */
        $container[self::PANEL_PRIMARY] = function (Container $container) {
            return new Panels\Primary();
        };

		/**
		 * @return Sections\Buttons The buttons customizer section instance.
		 */
        $container[self::SECTION_BUTTONS] = function (Container $container) {
            return new Sections\Buttons();
        };

		/**
		 * @return Sections\Colors The colors customizer section instance.
		 */
        $container[self::SECTION_COLORS] = function (Container $container) {
            return new Sections\Colors();
        };

		/**
		 * @return Sections\Product_Single The product single customizer section instance.
		 */
        $container[self::SECTION_PRODUCT_SINGLE] = function (Container $container) {
            return new Sections\Product_Single();
        };

		/**
		 * @return Sections\Product_Archive The product archive customizer section instance.
		 */
        $container[self::SECTION_PRODUCT_ARCHIVE] = function (Container $container) {
            return new Sections\Product_Archive();
        };

		/**
		 * @return Sections\Product_Category The product categories customizer section instance.
		 */
        $container[self::SECTION_PRODUCT_CATEGORIES] = function (Container $container) {
            return new Sections\Product_Category();
        };

		/**
		 * @return Sections\Cart The cart customizer section instance.
		 */
        $container[self::SECTION_CART] = function (Container $container) {
            return new Sections\Cart();
        };

		/**
		 * @return Sections\Checkout The checkout customizer section instance.
		 */
        $container[self::SECTION_CHECKOUT] = function (Container $container) {
            return new Sections\Checkout();
        };

		/**
		 * @return Sections\Banners The banners customizer section instance.
		 */
        $container[self::SECTION_BANNERS] = function (Container $container) {
            return new Sections\Banners();
        };

		/**
		 * @return Styles The styles instance for printing customizer styles.
		 */
        $container[self::STYLES] = function (Container $container) {
            $path = dirname($container['plugin_file']) . '/assets/customizer.template.css';
            return new Styles($path);
        };

        add_action('customize_register', $this->create_callback('customize_register', function ($wp_customize) use ($container) {
            $container[self::PANEL_PRIMARY]->register($wp_customize);
            $container[self::SECTION_BUTTONS]->register($wp_customize);
            $container[self::SECTION_COLORS]->register($wp_customize);
            $container[self::SECTION_PRODUCT_SINGLE]->register($wp_customize);
            $container[self::SECTION_PRODUCT_ARCHIVE]->register($wp_customize);
            $container[self::SECTION_PRODUCT_CATEGORIES]->register($wp_customize);
            $container[self::SECTION_BANNERS]->register($wp_customize);

            if (get_option(\BigCommerce\Settings\Sections\Cart::OPTION_ENABLE_CART, true)) {
                $container[self::SECTION_CART]->register($wp_customize);
            }

            if (get_option(\BigCommerce\Settings\Sections\Cart::OPTION_EMBEDDED_CHECKOUT, true)) {
                $container[self::SECTION_CHECKOUT]->register($wp_customize);
            }
        }), 10, 1);

        add_action('wp_head', $this->create_callback('customizer_styles', function () use ($container) {
            $container[self::STYLES]->print_styles();
        }), 10, 0);
    }
}
