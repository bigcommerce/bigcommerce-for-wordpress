<?php

/**
 * Template for the admin Setup Complete screen
 *
 * @var bool   $new_account
 *
 * @var string $settings_url
 * @var string $create_url
 * @var string $support_url
 * @var string $extend_url
 * @var string $customize_url
 * @var string $settings_sections
 */

use BigCommerce\Settings\Screens\Onboarding_Complete_Screen;

?>
<div class="bc-plugin-page-header">
	<img src="<?php echo esc_url( bigcommerce()->plugin_dir_url() . 'assets/img/admin/big-commerce-logo.svg' ); ?>" alt="<?php esc_attr_e( 'BigCommerce', 'bigcommerce' ); ?>">
</div>
<?php do_action( 'bigcommerce/settings/before_title/page=' . Onboarding_Complete_Screen::NAME ); ?>
<div class="bc-welcome">
	<?php do_action( 'bigcommerce/settings/onboarding/progress' ); ?>
	<div class="bc-welcome__content bc-welcome__content--complete">
		<div class="bc-welcome__content-wrap">
			<h1 class="bc-welcome__content-title"><?php esc_html_e( "You've successfully installed the BigCommerce for WordPress Plugin and connected your BigCommerce Store!", 'bigcommerce' ); ?></h1>
		</div>
	</div>

	<?php echo $settings_sections; ?>
</div>
