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
 */

use BigCommerce\Settings\Screens\Welcome_Screen;

?>
<div class="bc-plugin-page-header">
	<img src="<?php echo esc_url( bigcommerce()->plugin_dir_url() . 'assets/img/admin/big-commerce-logo.svg' ); ?>" alt="<?php esc_attr_e( 'BigCommerce', 'bigcommerce' ); ?>">
</div>
<div class="bc-welcome">
	<?php do_action( 'bigcommerce/settings/onboarding/progress' ); ?>
	<div class="bc-welcome__content bc-welcome__content--complete">
		<?php do_action( 'bigcommerce/settings/before_title/page=' . Welcome_Screen::NAME ); ?>
		<div class="bc-welcome__content-wrap">
			<h1 class="bc-welcome__content-title"><?php esc_html_e( "You've successfully installed the BigCommerce for WordPress Plugin and connected your BigCommerce Store!", 'bigcommerce' ); ?></h1>

			<?php if ( $new_account ) { ?>
				<p class="bc-welcome__new-account-email"><?php esc_html_e( 'Check your email for a link to confirm your account.', 'bigcommerce' ); ?></p>
			<?php } ?>
		</div>
	</div>
	<div class="bc-welcome__content">
		<p class="bc-welcome__next-steps-title"><?php echo esc_html__( 'Next Steps', 'bigcommerce' ); ?></p>
		<ul class="bc-welcome__next-steps-list">
			<li class="bc-welcome__next-steps-item">
				<i class="bc-icon icon-bc-tag"></i>
				<p class="bc-welcome__next-steps-description">
					<?php echo esc_html__( 'Begin creating and importing products in BigCommerce.', 'bigcommerce' ); ?>
				</p>
				<a href="<?php echo esc_url( $create_url ); ?>" target="_blank" rel="noopener" class="bc-welcome__next-steps-anchor"><?php echo esc_html__( 'Create, import, and manage products', 'bigcommerce' ); ?></a>
			</li>
			<li class="bc-welcome__next-steps-item">
				<i class="bc-icon icon-bc-grid"></i>
				<p class="bc-welcome__next-steps-description">
					<?php echo esc_html__( 'Get support and helpful resources for setting up your new store.', 'bigcommerce' ); ?>
				</p>
				<a href="<?php echo esc_url( $support_url ); ?>" target="_blank" rel="noopener" class="bc-welcome__next-steps-anchor"><?php echo esc_html__( 'Get support', 'bigcommerce' ); ?></a>
			</li>
			<li class="bc-welcome__next-steps-item">
				<i class="bc-icon icon-bc-product_reviews"></i>
				<p class="bc-welcome__next-steps-description">
					<?php echo esc_html__( 'Write your own extensions and get the most out of BigCommerce with helpful documentation.', 'bigcommerce' ); ?>
				</p>
				<a href="<?php echo esc_url( $extend_url ); ?>" target="_blank" rel="noopener" class="bc-welcome__next-steps-anchor"><?php echo esc_html__( "Extend your store's functionality", 'bigcommerce' ); ?></a>
			</li>
			<li class="bc-welcome__next-steps-item">
				<i class="bc-icon icon-bc-gear"></i>
				<p class="bc-welcome__next-steps-description">
					<?php echo esc_html__( 'Apply a color scheme, customize your buttons, modify product grids and more in the BigCommerce for WordPress Customizer.', 'bigcommerce' ); ?>
				</p>
				<a href="<?php echo esc_url( $customize_url ); ?>" target="_blank" rel="noopener" class="bc-welcome__next-steps-anchor"><?php echo esc_html__( 'Customize your WordPress theme experience', 'bigcommerce' ); ?></a>
			</li>
			<li class="bc-welcome__next-steps-item">
				<i class="bc-icon icon-bc-gear"></i>
				<p class="bc-welcome__next-steps-description">
					<?php echo esc_html__( "Customize your store's functionality, sync products, and more on your BigCommerce for WordPress settings page.", 'bigcommerce' ); ?>
				</p>
				<a href="<?php echo esc_url( $settings_url ); ?>" target="_blank" rel="noopener" class="bc-welcome__next-steps-anchor"><?php echo esc_html__( 'Adjust your settings and sync products', 'bigcommerce' ); ?></a>
			</li>
		</ul>
	</div>
</div>
