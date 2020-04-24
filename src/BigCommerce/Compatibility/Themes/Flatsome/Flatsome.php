<?php


namespace BigCommerce\Compatibility\Themes\Flatsome;

use BigCommerce\Compatibility\Themes\Theme;
use BigCommerce\Templates\Login_Form;

class Flatsome extends Theme {

	protected $supported_version = '3.10.1';

	protected $templates = [
		'myaccount/account-links.php' => Templates\Account_Links::class,
		'myaccount/form-login.php'    => Login_Form::class,
	];

	public function load_compat_functions() {
		include_once( dirname( __FILE__ ) . '/functions.php' );
	}

}