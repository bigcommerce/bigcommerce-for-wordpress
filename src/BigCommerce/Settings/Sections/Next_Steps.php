<?php

namespace BigCommerce\Settings\Sections;

use BigCommerce\Merchant\Onboarding_Api;
use BigCommerce\Merchant\Setup_Status;
use BigCommerce\Settings\Screens\Onboarding_Complete_Screen;

/**
 * Class Next_Steps
 *
 * Displays required and optional next steps after onboarding.
 */
class Next_Steps extends Settings_Section {
	const NAME = 'next_steps';
	/**
	 * @var Setup_Status
	 */
	private $status;

	/** @var string Path to the templates/admin directory */
	private $template_dir;

	public function __construct( Setup_Status $status, $template_dir ) {
		$this->status       = $status;
		$this->template_dir = trailingslashit( $template_dir );
	}

	public function register_settings_section() {
		add_settings_section(
			self::NAME,
			'',
			[ $this, 'render_section' ],
			Onboarding_Complete_Screen::NAME
		);
	}

	public function render_section() {
		$required    = $this->status->get_required_steps();
		$optional    = $this->status->get_optional_steps();
		$new_account = $this->status->new_account();
		include( $this->template_dir . 'next-steps.php' );
	}
}
