<?php
/**
 * @var int      $user_id
 * @var string   $first_name
 * @var string   $last_name
 * @var string   $company
 * @var string   $email
 * @var string   $phone
 * @var string[] $errors
 * @version 1.0.0
 */
$error_class = 'bc-form__control--error'; // REQUIRED
?>

<section class="bc-account-page" data-form-type="bc-account-form">
	<form class="bc-form bc-form-2col bc-account-form--profile bc-account-profile <?php if ( ! empty( $errors ) ) { echo 'bc-form--has-errors'; } ?>" action=""
				enctype="multipart/form-data" method="post">
		<?php wp_nonce_field( 'edit-profile' . $user_id ); ?>
		<input type="hidden" name="bc-action" value="edit-profile"/>
		<input type="hidden" name="bc-profile[user_id]" value="<?php echo esc_attr( (int) $user_id ); ?>"/>
		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'first_name', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-firstname">
			<span class="bc-form__label bc-account-profile__form-label bc-form-control-required"><?php echo esc_html( __( 'First Name', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-profile[first_name]" id="bc-account-profile-firstname" value="<?php echo esc_attr( $first_name ); ?>" data-form-field="bc-form-field-first_name">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'last_name', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-lastname">
			<span class="bc-form__label bc-account-profile__form-label bc-form-control-required"><?php echo esc_html( __( 'Last Name', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-profile[last_name]" id="bc-account-profile-lastname" value="<?php echo esc_attr( $last_name ); ?>" data-form-field="bc-form-field-last_name">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'company', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-company">
			<span class="bc-form__label bc-account-profile__form-label"><?php echo esc_html( __( 'Company Name', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-profile[company]" id="bc-account-profile-company" value="<?php echo esc_attr( $company ); ?>" data-form-field="bc-form-field-company">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'phone', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-phone">
			<span class="bc-form__label bc-account-profile__form-label"><?php echo esc_html( __( 'Phone Number', 'bigcommerce' ) ); ?></span>
			<input type="tel" name="bc-profile[phone]" id="bc-account-profile-phone" value="<?php echo esc_attr( $phone ); ?>" data-form-field="bc-form-field-phone">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'email', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-email">
			<span class="bc-form__label bc-account-profile__form-label bc-form-control-required"><?php echo esc_html( __( 'Email Address', 'bigcommerce' ) ); ?></span>
			<input type="email" name="bc-profile[email]" id="bc-account-profile-email" value="<?php echo esc_attr( $email ); ?>" data-form-field="bc-form-field-email">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'current_password', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-password">
			<span class="bc-form__label bc-account-profile__form-label bc-form-control-required"><?php echo esc_html( __( 'Current Password', 'bigcommerce' ) ); ?></span>
			<input type="password" name="bc-profile[current_password]" id="bc-account-profile-password" value="" data-form-field="bc-form-field-current_password">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'new_password', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-new-password">
			<span class="bc-form__label bc-account-profile__form-label bc-form-control-required"><?php echo esc_html( __( 'New Password', 'bigcommerce' ) ); ?></span>
			<input type="password" name="bc-profile[new_password]" id="bc-account-profile-new-password" value="" data-form-field="bc-form-field-new_password">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'confirm_password', $errors ) ) { echo $error_class; } ?>" for="bc-account-profile-confirm-password">
			<span class="bc-form__label bc-account-profile__form-label bc-form-control-required"><?php echo esc_html( __( 'Confirm Password', 'bigcommerce' ) ); ?></span>
			<input type="password" name="bc-profile[confirm_password]" id="bc-account-profile-confirm-password" value="" data-form-field="bc-form-field-confirm_password">
		</label>

		<div class="bc-form__actions bc-account-profile__actions">
			<button class="bc-btn bc-btn--account" aria-label="<?php __( 'Update Details', 'bigcommerce' ); ?>" type="submit"><?php echo esc_html( __( 'Update Details', 'bigcommerce' ) ); ?></button>
		</div>
	</form>
</section>
