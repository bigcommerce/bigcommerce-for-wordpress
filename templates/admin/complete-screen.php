<?php

/**
 * Template for the admin Setup Complete screen (Open Source Version)
 *
 * @var bool   $new_account
 * @var string $settings_url
 * @var string $create_url
 * @var string $support_url
 * @var string $extend_url
 * @var string $customize_url
 * @var string $settings_sections
 */

use OpenSource\Settings\Screens\Onboarding_Complete_Screen;

?>
<div class="plugin-page-header">
	<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/img/admin/plugin-logo.svg' ); ?>" alt="<?php esc_attr_e( 'Open Source Plugin', 'opensource' ); ?>">
</div>
<?php do_action( 'opensource/settings/before_title/page=' . Onboarding_Complete_Screen::NAME ); ?>
<div class="welcome">
	<?php do_action( 'opensource/settings/onboarding/progress' ); ?>
	<div class="welcome__content welcome__content--complete">
		<div class="welcome__content-wrap">
			<h1 class="welcome__content-title"><?php esc_html_e( "You've successfully installed the Open Source Plugin and configured your settings!", 'opensource' ); ?></h1>
		</div>
	</div>

	<?php echo $settings_sections; ?>
</div>
