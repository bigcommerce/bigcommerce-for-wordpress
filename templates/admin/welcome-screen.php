<?php

/**
 * Template for the admin Welcome screen
 *
 * @var string $connect_account_url
 * @var string $create_account_url
 * @var string $credentials_url
 * @var array  $notices
 */
?>
<div class="bc-welcome">
	<div class="bc-welcome__content">
		<div class="bc-welcome__content-wrap">
			<img src="<?php echo esc_url( bigcommerce()->plugin_dir_url() . '/assets/img/admin/big-commerce-logo.svg' ); ?>" alt="<?php esc_attr_e('Big Commerce', 'bigcommerce'); ?>">
			<h1 class="bc-welcome__content-title"><?php esc_html_e( 'Build your online store with BigCommerce', 'bigcommerce' ); ?></h1>
			<p><?php esc_html_e( 'Customize your site, manage shipping and payments, and list your products on Amazon, eBay, and Facebook with the #1 ecommerce platform. Try it free, no credit card required.', 'bigcommerce' ); ?></p>
		</div>
		<div class="bc-welcome__btn-group">
			<a class="bc-admin-btn" href="<?php echo esc_url( $connect_account_url ); ?>"><?php esc_html_e( 'Connect My Account', 'bigcommerce' ); ?></a>
			<a class="bc-admin-btn bc-admin-btn--outline" href="<?php echo esc_url( $create_account_url ); ?>"><?php esc_html_e( 'Create New Account', 'bigcommerce' ); ?></a>
		</div>
		<div class="bc-welcome__alt-actions">
			<?php printf(
				'%s <a href="%s">%s</a>',
				esc_html( __( 'Multiple WordPress sites connecting to a single BigCommerce store?', 'bigcommerce' ) ),
				esc_url( $credentials_url ),
				esc_html( __( 'Enter your API credentials', 'bigcommerce' ) )
			); ?>
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
