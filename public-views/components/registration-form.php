<?php
/**
 * @var array    $defaults
 * @var array    $countries
 * @var string[] $errors
 */

$error_class = 'bc-form__control--error';
?>

<section class="bc-account-page">
	<form class="bc-form bc-form-2col bc-account-form--register <?php if ( ! empty( $errors ) ) { echo 'bc-form--has-errors'; } ?>" action="" enctype="multipart/form-data" method="post" data-js="bc-dynamic-fields">
		<?php wp_nonce_field( 'register-account' ); ?>
		<input type="hidden" name="bc-action" value="register-account"/>
		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--full <?php if ( in_array( 'email', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-email">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Email Address', 'bigcommerce' ) ); ?></span>
			<input type="email" name="bc-register[email]" id="bc-account-register-email"
						 value="<?php echo esc_html( $defaults[ 'email' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'new_password', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-password">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Password', 'bigcommerce' ) ); ?></span>
			<input type="password" name="bc-register[new_password]" id="bc-account-register-password" value="">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'confirm_password', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-confirm-password">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Confirm Password', 'bigcommerce' ) ); ?></span>
			<input type="password" name="bc-register[confirm_password]" id="bc-account-register-confirm-password" value="">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'first_name', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-firstname">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'First Name', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[first_name]" id="bc-account-register-firstname"
						 value="<?php echo esc_html( $defaults[ 'first_name' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'last_name', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-lastname">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Last Name', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[last_name]" id="bc-account-register-lastname"
						 value="<?php echo esc_html( $defaults[ 'last_name' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'street_1', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-street1">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Address Line 1', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[street_1]" id="bc-account-register-street1"
						 value="<?php echo esc_html( $defaults[ 'street_1' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'street_2', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-street2">
				<span
					class="bc-form__label bc-account-register__form-label"><?php echo esc_html( __( 'Address Line 2', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[street_2]" id="bc-account-register-street2"
						 value="<?php echo esc_html( $defaults[ 'street_2' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'company', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-company">
				<span
					class="bc-form__label bc-account-register__form-label"><?php echo esc_html( __( 'Company Name', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[company]" id="bc-account-register-company"
						 value="<?php echo esc_html( $defaults[ 'company' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'city', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-city">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Suburb/City', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[city]" id="bc-account-register-city"
						 value="<?php echo esc_html( $defaults[ 'city' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'state', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-state" data-js="bc-dynamic-state">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'State/Province', 'bigcommerce' ) ); ?></span>
			<?php if ( ! empty( $states ) ) { ?>
				<select id="bc-account-register-state" name="bc-register[state]" data-js="bc-dynamic-state-control">
					<?php foreach ( $states as $state_abbr => $state_name ) { ?>
						<option value="<?php echo esc_attr( $state_name ); ?>"
										data-state-abbr="<?php echo esc_attr( $state_abbr ); ?>"
							<?php selected( $state_name, $defaults[ 'state' ] ); ?>><?php echo esc_html( $state_name ); ?></option>
					<?php } ?>
				</select>
			<?php } else { ?>
				<input type="text" id="bc-account-register-state" name="bc-register[state]" value="<?php echo esc_attr( $defaults[ 'state' ] ); ?>"
							 data-js="bc-dynamic-state-control" />
			<?php } ?>
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'zip', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-zip">
				<span
					class="bc-form__label bc-account-register-label bc-form-control-required"><?php echo esc_html( __( 'Zip/Postcode', 'bigcommerce' ) ); ?></span>
			<input type="text" name="bc-register[zip]" id="bc-account-register-zip"
						 value="<?php echo esc_html( $defaults[ 'zip' ] ); ?>">
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'country', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-country">
				<span
					class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Country', 'bigcommerce' ) ); ?></span>
			<select name="bc-register[country]" id="bc-account-register-country" data-js="bc-dynamic-country-select">
				<?php foreach ( $countries as $iso => $value ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>"
					        data-country-iso="<?php echo esc_attr( $iso ); ?>"
						<?php selected( $value, $defaults[ 'country' ] ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } ?>
			</select>
		</label>

		<label class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'phone', $errors ) ) { echo $error_class; } ?>" for="bc-account-register-phone">
			<span class="bc-form__label bc-account-register__form-label bc-form-control-required"><?php echo esc_html( __( 'Phone:', 'bigcommerce' ) ); ?></span>
			<input type="tel" name="bc-register[phone]" id="bc-account-register-phone"
						 value="<?php echo esc_html( $defaults[ 'phone' ] ); ?>">
		</label>

		<div class="bc-form__actions bc-account-register__actions">
			<button class="bc-btn bc-btn--register" aria-label="<?php __( 'Register', 'bigcommerce' ); ?>"
							type="submit"><?php echo esc_html( __( 'Register', 'bigcommerce' ) ); ?></button>
		</div>
	</form>
</section>
