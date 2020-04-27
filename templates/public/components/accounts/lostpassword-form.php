<?php

/**
 * Template for the lost password form
 *
 * @var string $form_action
 * @var string $login_url
 * @var string $register_url
 * @var string $redirect_to
 * @var string $message
 * @version 1.0.0
 */

?>

<div class="bc-account-page">
	<section class="bc-account-lost-password">
		<p><?php esc_html_e( 'Fill in your email address below to request a password. An email will be sent containing a link to verify your email address.', 'bigcommerce' ); ?></p>
		<?php echo $message; ?>
		<form class="bc-form bc-account-form--lost-password" action="<?php echo esc_url( $form_action ); ?>" method="post">
			<label class="bc-form__control bc-form-account__control" for="bc-account-user-email">
				<span
					class="bc-form__label bc-account-lost-password__form-label bc-form-control-required"><?php echo esc_html( __( 'Email', 'bigcommerce' ) ); ?></span>
				<input type="email" name="user_login" id="bc-account-user-email" value="">
			</label>
			<?php do_action( 'lostpassword_form' ); ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"/>
			<div class="bc-form__actions bc-account-lost-password__actions">
				<button class="bc-btn bc-btn--lost-password" aria-label="<?php __( 'Reset Password', 'bigcommerce' ); ?>"
								type="submit" name="wp-submit"><?php echo esc_html( __( 'Reset Password', 'bigcommerce' ) ); ?></button>
			</div>
		</form>
		<ul class="bc-account-lost-password__account-actions">
			<li class="bc-account-lost-password__account-link">
				<a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Log in', 'bigcommerce' ) ?></a>
			</li>

			<?php if ( $register_url ) { ?>
			<li class="bc-account-lost-password__account-link">
				<a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Register', 'bigcommerce' ); ?></a>
			</li>
			<?php } ?>
		</ul>
	</section>
</div>
