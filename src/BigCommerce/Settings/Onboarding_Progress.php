<?php


namespace BigCommerce\Settings;

use BigCommerce\Container\Settings;
use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Settings\Screens\Api_Credentials_Screen;
use BigCommerce\Settings\Screens\Welcome_Screen;

class Onboarding_Progress {
	/**
	 * @var int The onboarding configuration state
	 */
	private $state;

	/**
	 * @var string Path to directory containing admin templates
	 */
	private $template_dir;

	public function __construct( $state, $template_dir ) {
		$this->state        = $state;
		$this->template_dir = $template_dir;
	}

	/**
	 * Render the onboarding progress bar
	 *
	 * @return void
	 * @action bigcommerce/settings/onboarding/progress
	 */
	public function render() {
		$steps = $this->steps();
		include trailingslashit( $this->template_dir ) . '/onboarding-progress.php';
	}

	private function steps() {
		$steps   = [];
		$steps[] = [
			'label'  => __( 'Welcome', 'bigcommerce' ),
			'active' => ( $this->state === Settings::STATUS_NEW ) && ( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) === Welcome_Screen::NAME ),
		];

		if ( $this->connecting_existing_account() ) {
			$steps[] = [
				'label'  => __( 'Connect Account', 'bigcommerce' ),
				'active' => ( $this->state === Settings::STATUS_ACCOUNT_PENDING ) || ( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) === Api_Credentials_Screen::NAME ),
			];
			$steps[] = [
				'label'  => __( 'Connect Your Channel', 'bigcommerce' ),
				'active' => $this->state === Settings::STATUS_API_CONNECTED,
			];
		} else {
			$steps[] = [
				'label'  => __( 'Create Account', 'bigcommerce' ),
				'active' => ( $this->state === Settings::STATUS_NEW ) && ( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) !== Welcome_Screen::NAME ),
			];
			$steps[] = [
				'label'  => __( 'Provision Your Store', 'bigcommerce' ),
				'active' => $this->state === Settings::STATUS_ACCOUNT_PENDING,
			];
		}

		$steps[] = [
			'label'  => __( 'Configure Your Store', 'bigcommerce' ),
			'active' => $this->state >= Settings::STATUS_CHANNEL_CONNECTED && $this->state < Settings::STATUS_COMPLETE,
		];

		$steps[] = [
			'label'  => __( 'Complete', 'bigcommerce' ),
			'active' => $this->state === Settings::STATUS_COMPLETE,
		];

		/**
		 * Filters the list of steps in the onboarding progress bar.
		 *
		 * @param array[] $steps The steps in the process. Properties:
		 *                       - label  string The label for the step
		 *                       - active bool   Whether the step is currently active
		 * @param int     $state The current onboarding status
		 */
		return apply_filters( 'bigcommerce/settings/onboarding/steps', $steps, $this->state );
	}

	private function connecting_existing_account() {
		if ( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) === Api_Credentials_Screen::NAME ) {
			return true;
		}

		if ( $this->state === Settings::STATUS_NEW ) {
			return false;
		}

		$account_id = get_option( Onboarding_Api::ACCOUNT_ID, '' );

		return empty( $account_id );
	}

	/**
	 * Render the subheader showing the current step
	 *
	 * @return void
	 * @action bigcommerce/settings/before_title/page= . Welcome_Screen::NAME
	 * @action bigcommerce/settings/before_title/page= . Create_Account_Screen::NAME
	 * @action bigcommerce/settings/before_title/page= . Api_Credentials_Screen::NAME
	 * @action bigcommerce/settings/before_title/page= . Pending_Account_Screen::NAME
	 * @action bigcommerce/settings/before_title/page= . Connect_Channel_Screen::NAME
	 * @action bigcommerce/settings/before_title/page= . Store_Type_Screen::NAME
	 * @action bigcommerce/settings/before_title/page= . Nav_Menu_Screen::NAME
	 */
	public function step_subheader() {
		$steps = $this->steps();
		foreach ( $steps as $index => $step ) {
			if ( $step['active'] ) {
				printf( '<p class="bc-onboarding-step-sub-title"><span class="bc-onboarding-step-sub-title__number">%02d</span> %s</p>', $index + 1, esc_html( $step['label'] ) );
				break;
			}
		}
	}
}
