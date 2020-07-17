<?php
/**
 * The form to edit an address
 *
 * @var int      $id
 * @var string   $first_name
 * @var string   $last_name
 * @var string   $company
 * @var string   $street_1
 * @var string   $street_2
 * @var string   $city
 * @var string   $state
 * @var string   $zip
 * @var string   $country
 * @var string   $phone
 * @var string[] $countries
 * @var string[] $states
 * @var string[] $errors
 * @version 1.0.0
 */
$error_class = 'bc-form__control--error'; // REQUIRED
?>

<!-- data-js="bc-dynamic-fields" is required -->
<form action="" enctype="multipart/form-data" method="post" class="bc-form bc-form-2col <?php if ( ! empty( $errors ) ) { echo 'bc-form--has-errors'; } ?>" data-js="bc-dynamic-fields" data-form-type="bc-address-form">
	<?php wp_nonce_field( 'edit-address' . $id ); ?>
	<input type="hidden" name="bc-action" value="edit-address" />
	<input type="hidden" name="bc-address[id]" value="<?php echo esc_attr( (int) $id ); ?>" />
	<label for="bc-account-address-firstname" class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'first_name', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'First Name:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[first_name]" id="bc-account-address-firstname" value="<?php echo esc_attr( $first_name ); ?>" data-form-field="bc-form-field-first_name">
	</label>

	<label for="bc-account-address-lastname" class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'last_name', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'Last Name:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[last_name]" id="bc-account-address-lastname" value="<?php echo esc_attr( $last_name ); ?>" data-form-field="bc-form-field-last_name">
	</label>

	<label for="bc-account-address-company" class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'company', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label"><?php echo esc_html( __( 'Company:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[company]" id="bc-account-address-company" value="<?php echo esc_attr( $company ); ?>" data-form-field="bc-form-field-company">
	</label>

	<label for="bc-account-address-street1" class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'street_1', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'Address Line 1:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[street_1]" id="bc-account-address-street1" value="<?php echo esc_attr( $street_1 ); ?>" data-form-field="bc-form-field-street_1">
	</label>

	<label for="bc-account-address-street2" class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'street_2', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label"><?php echo esc_html( __( 'Address Line 2:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[street_2]" id="bc-account-address-street2" value="<?php echo esc_attr( $street_2 ); ?>" data-form-field="bc-form-field-street_2">
	</label>

	<label for="bc-account-address-city" class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'city', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'Suburb/City:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[city]" id="bc-account-address-city" value="<?php echo esc_attr( $city ); ?>" data-form-field="bc-form-field-city">
	</label>

	<!-- data-js="bc-dynamic-state" is required -->
	<label for="bc-account-address-state" class="bc-form__control bc-form-2col__control bc-form-2col__control--left bc-account-address-state <?php if ( in_array( 'state', $errors ) ) { echo $error_class; } ?>" data-js="bc-dynamic-state">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'State/Province:', 'bigcommerce' ) ); ?></span>
		<?php if ( ! empty( $states ) ) { ?>
			<!-- data-js="bc-dynamic-state-control" is required -->
			<select id="bc-account-address-state" name="bc-address[state]" data-js="bc-dynamic-state-control" data-form-field="bc-form-field-state">
				<?php foreach ( $states as $state_abbr => $state_name ) { ?>
					<option value="<?php echo esc_attr( $state_name ); ?>" data-state-abbr="<?php echo esc_attr( $state_abbr ); ?>"
						<?php selected( $state_name, $state ); ?>><?php echo esc_html( $state_name ); ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<!-- data-js="bc-dynamic-state-control" is required -->
			<input type="text" id="bc-account-address-state" name="bc-address[state]" value="<?php echo esc_attr( $state ); ?>" data-js="bc-dynamic-state-control" data-form-field="bc-form-field-state" />
		<?php } ?>
	</label>

	<label for="bc-account-address-zip" class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'zip', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'Zip/Postcode:', 'bigcommerce' ) ); ?></span>
		<input type="text" name="bc-address[zip]" id="bc-account-address-zip" value="<?php echo esc_attr( $zip ); ?>" data-form-field="bc-form-field-zip">
	</label>

	<label for="bc-account-address-country" class="bc-form__control bc-form-2col__control bc-form-2col__control--left <?php if ( in_array( 'country', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'Country:', 'bigcommerce' ) ); ?></span>
		<!-- data-js="bc-dynamic-country-select" is required -->
		<select name="bc-address[country]" id="bc-account-address-country" data-js="bc-dynamic-country-select" data-form-field="bc-form-field-country">
			<?php foreach ( $countries as $iso => $value ) { ?>
				<option value="<?php echo esc_attr( $value ); ?>" data-country-iso="<?php echo esc_attr( $iso ); ?>"
					<?php selected( $value, $country ); ?>><?php echo esc_html( $value ); ?></option>
			<?php } ?>
		</select>
	</label>

	<label for="bc-account-address-phone" class="bc-form__control bc-form-2col__control bc-form-2col__control--right <?php if ( in_array( 'phone', $errors ) ) { echo $error_class; } ?>">
		<span class="bc-form__label bc-account-address__form-label bc-form-control-required"><?php echo esc_html( __( 'Phone:', 'bigcommerce' ) ); ?></span>
		<input type="tel" name="bc-address[phone]" id="bc-account-address-phone" value="<?php echo esc_attr( $phone ); ?>" data-form-field="bc-form-field-phone">
	</label>

	<div class="bc-account-address-form-actions">
		<button class="bc-btn bc-account-address-form-save" aria-label="<?php __( 'Save Address', 'bigcommerce' ); ?>" type="submit" data-js="bc-account-address-form-save"><?php echo esc_html( __( 'Save', 'bigcommerce' ) ); ?></button>
		<!-- data-js="bc-account-address-form-cancel" is required -->
		<button class="bc-btn bc-btn--inverse bc-account-address-form-cancel" aria-label="<?php __( 'Cancel Editing', 'bigcommerce' ); ?>" type="button" data-js="bc-account-address-form-cancel"><?php echo esc_html( __( 'Cancel', 'bigcommerce' ) ); ?></button>
	</div>

</form>