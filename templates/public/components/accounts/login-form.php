<?php
/**
 * @var string $form
 * @var string $register_link
 * @var string $message
 * @version 1.0.0
 */
?>

<div class="bc-account-page">
	<section class="bc-account-login">
		<div class="bc-account-login__form">
			<div class="bc-account-login__form-inner">
				<?php echo $message; ?>
				<?php echo $form; ?>
				<a href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>"
					 title="<?php echo esc_attr( 'Forgot Password', 'bigcommerce' ); ?>">
					<?php esc_html_e( 'Forgot your password?', 'bigcommerce' ); ?>
				</a>
			</div>
		</div>
		<?php if ( $register_link ) { ?>
			<div class="bc-account-login__register">
				<div class="bc-account-login__register-inner">
					<h3 class="bc-account-login__register-title"><?php esc_html_e( 'New Customer', 'bigcommerce' ); ?></h3>
					<p class="bc-account-login__register-description"><?php esc_html_e( "Create an account with us and you'll be able to:", 'bigcommerce' ); ?></p>
					<ul class="bc-account-login__register-list">
						<li><?php esc_html_e( 'Check out faster', 'bigcommerce' ); ?></li>
						<li><?php esc_html_e( 'Save multiple shipping addresses', 'bigcommerce' ); ?></li>
						<li><?php esc_html_e( 'Access order history', 'bigcommerce' ); ?></li>
						<li><?php esc_html_e( 'Track new orders', 'bigcommerce' ); ?></li>
					</ul>
					<a class="bc-btn bc-btn--register" href="<?php echo esc_url( $register_link ); ?>"
						 title="<?php esc_attr( 'Register', 'bigcommerce' ); ?>"><?php esc_html_e( 'Register', 'bigcommerce' ); ?></a>
				</div>
			</div>
		<?php } ?>
	</section>
</div>