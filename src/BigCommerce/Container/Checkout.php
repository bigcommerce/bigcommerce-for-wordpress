<?php


namespace BigCommerce\Container;


use BigCommerce\Checkout\Customer_Login;
use BigCommerce\Checkout\Requirements_Notice;
use BigCommerce\Settings\Sections\Channels;
use Pimple\Container;

class Checkout extends Provider {
	const REQUIREMENTS_NOTICE = 'checkout.requirements_notice';
	const LOGIN               = 'checkout.customer_login';

	public function register( Container $container ) {
		$this->requirements( $container );
		$this->customer_login( $container );
	}

	private function requirements( Container $container ) {
		$container[ self::REQUIREMENTS_NOTICE ] = function ( Container $container ) {
			return new Requirements_Notice( $container[ Merchant::SETUP_STATUS ] );
		};

		add_action( 'admin_notices', $this->create_callback( 'verify_checkout_requirements', function () use ( $container ) {
			if ( $container[ Settings::CONFIG_STATUS ] >= Settings::STATUS_COMPLETE ) {
				$container[ self::REQUIREMENTS_NOTICE ]->check_requirements();
			}
		} ), 10, 0 );
		add_action( 'admin_post_' . Requirements_Notice::REFRESH, $this->create_callback( 'refresh_checkout_requirements', function () use ( $container ) {
			$container[ self::REQUIREMENTS_NOTICE ]->refresh_status();
		} ), 10, 0 );

		add_filter( 'pre_option_' . \BigCommerce\Settings\Sections\Cart::OPTION_EMBEDDED_CHECKOUT, $this->create_callback( 'embedded_checkout_requirement_check', function ( $value ) use ( $container ) {
			return $container[ self::REQUIREMENTS_NOTICE ]->filter_embedded_checkout( $value );
		} ), 10, 1 );

		add_filter( 'bigcommerce/checkout/can_embed', $this->create_callback( 'embedded_checkout_supported', function ( $supported ) use ( $container ) {
			return $container[ self::REQUIREMENTS_NOTICE ]->can_enable_embedded_checkout();
		} ), 1, 1 );
	}

	private function customer_login( Container $container ) {
		$container[ self::LOGIN ] = function ( Container $container ) {
			return new Customer_Login( $container[ Merchant::ONBOARDING_API ], $container[ Api::FACTORY ]->store() );
		};

		add_filter( 'bigcommerce/checkout/url', $this->create_callback( 'checkout_url', function ( $url ) use ( $container ) {
			return $container[ self::LOGIN ]->set_login_token_for_checkout( $url );
		} ), 10, 1 );

	}

}
