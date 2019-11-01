<?php

/**
 * Template for the admin Welcome screen
 *
 * @var string $connect_account_url
 * @var string $create_account_url
 * @var string $credentials_url
 * @var array  $notices
 * @var string $video
 */
use BigCommerce\Settings\Screens\Welcome_Screen;
?>
<div class="bc-plugin-page-header">
	<img src="<?php echo esc_url( bigcommerce()->plugin_dir_url() . 'assets/img/admin/big-commerce-logo.svg' ); ?>" alt="<?php esc_attr_e( 'BigCommerce', 'bigcommerce' ); ?>">
</div>
<div class="bc-welcome">
	<?php do_action( 'bigcommerce/settings/onboarding/progress' ); ?>
	<div class="bc-onboarding__video">
		<div class="bc-onboarding__video-embed">
			<?php echo $video; ?>
		</div>
	</div>
	<div class="bc-welcome__content">
		<div class="bc-welcome__content-wrap">
			<div class="bc-welcome--copy">
				<?php do_action( 'bigcommerce/settings/before_title/page=' . Welcome_Screen::NAME ); ?>
				<h1 class="bc-welcome__content-title"><?php esc_html_e( 'Build your online store with BigCommerce', 'bigcommerce' ); ?></h1>
				<p><?php esc_html_e( 'Customize your site, manage shipping and payments, and list your products on Amazon, eBay, and Facebook with the #1 ecommerce platform. Try it free, no credit card required.', 'bigcommerce' ); ?></p>
			</div>

			<div class="bc-welcome--actions">
				<div class="bc-welcome__btn-group">
					<p><?php esc_html_e( 'Are you new to BigCommerce?', 'bigcomerce' ); ?></p>

					<a class="bc-admin-btn" href="<?php echo esc_url( $create_account_url ); ?>"><?php esc_html_e( 'Create New Account', 'bigcommerce' ); ?></a>

					<p class="bc-welcome-or"><strong><?php esc_html_e( 'OR', 'bigcommerce' ); ?></strong></p>

					<p><?php esc_html_e( 'Do you already have a BigCommerce account?', 'bigcomerce' ); ?></p>

					<a class="bc-admin-btn bc-admin-btn--outline" href="<?php echo esc_url( $connect_account_url ); ?>"><?php esc_html_e( 'Connect Your Account', 'bigcommerce' ); ?></a>
					<?php printf(
						'<a href="%s" class="bc-admin-btn bc-admin-btn--outline">%s</a>',
						esc_url( $credentials_url ),
						esc_html( __( 'Enter your API credentials', 'bigcommerce' ) )
					); ?>
					<p class="bc-welcome-already-connected">
						<?php printf( '%s <a href="%s" class="bc-admin-anchor">%s</a>',
							esc_html( __( "If you've already connected another WP site, use API Credentials.", 'bigcommerce' ) ),
							esc_url( 'https://developer.bigcommerce.com/bigcommerce-for-wordpress/setup/multi-site' ),
							esc_html( __( 'Learn Why', 'bigcommerce' ) )
						); ?>
					</p>
				</div>
			</div>
		</div>
	</div>

	<?php if ( count( $notices ) > 0 ) { ?>
	<div class="bc-welcome__notices">
		<?php foreach ( $notices as $notice ) { ?>
			<div class="bc-welcome__notice">
				<?php if ( ! empty( $notice[ 'title' ] ) ) { ?>
					<h3 class="bc-welcome__notice-title"><?php echo $notice[ 'title' ] ; ?></h3>
				<?php } ?>
				<?php if ( ! empty( $notice[ 'content' ] ) ) { ?>
					<div class="bc-welcome__notice-content"><?php echo $notice[ 'content' ] ; ?></div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php } ?>
</div>
