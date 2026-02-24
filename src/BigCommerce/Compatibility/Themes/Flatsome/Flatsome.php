<?php


namespace BigCommerce\Compatibility\Themes\Flatsome;

use BigCommerce\Compatibility\Themes\Theme;
use BigCommerce\Templates\Login_Form;

/**
 * This class defines the compatibility layer for the Flatsome theme in the BigCommerce ecosystem. 
 * It specifies the supported version of the theme, maps template files to corresponding template classes, 
 * and loads additional compatibility functions.
 *
 * @package BigCommerce
 * @subpackage Compatibility\Themes\Flatsome
 */
class Flatsome extends Theme {

    /**
     * The supported version of the Flatsome theme.
     *
     * @var string
     */
    protected $supported_version = '3.10.1';

    /**
     * Template files mapped to their corresponding template classes.
     *
     * @var array
     */
    protected $templates = [
        'myaccount/account-links.php' => Templates\Account_Links::class,
        'myaccount/form-login.php'    => Login_Form::class,
    ];

    /**
     * Loads the compatibility functions for the Flatsome theme.
     *
     * This method includes the necessary functions for the Flatsome theme compatibility, 
     * which are stored in a separate file for better maintainability.
     */
    public function load_compat_functions() {
        include_once( dirname( __FILE__ ) . '/functions.php' );
    }

}