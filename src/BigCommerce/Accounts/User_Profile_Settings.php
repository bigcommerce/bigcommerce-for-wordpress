<?php


namespace BigCommerce\Accounts;


class User_Profile_Settings {
	const SYNC_PASSWORD = 'bigcommerce_sync_password';
	const NONCE_ACTION  = 'bc_user_profile';
	const NONCE_NAME    = 'bc_nonce';

	/**
	 * @param \WP_User $user
	 *
	 * @return void
	 * @action show_user_profile
	 * @action edit_user_profile
	 */
	public function render_profile_settings( $user ) {
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}
		$sync = get_user_meta( $user->ID, self::SYNC_PASSWORD, true );
		?>
		<h2><?php _e( 'BigCommerce', 'bigcommerce' ); ?></h2>
		<table class="form-table">
			<tr id="bigcommerce-sync-password" class="">
				<th scope="row">
					<?php _e( 'Authentication', 'bigcommerce' ); ?>
					<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
				</th>
				<td>
					<label for="<?php echo esc_attr( self::SYNC_PASSWORD ); ?>">
						<input id="<?php echo esc_attr( self::SYNC_PASSWORD ); ?>" type="checkbox"
									 name="<?php echo esc_attr( self::SYNC_PASSWORD ); ?>" value="1" <?php checked( $sync ); ?> />
						<?php _e( 'Synchronize Password', 'bigcommerce' ); ?>
					</label>
					<p
						class="description"><?php _e( "Validate the user's password with the BigCommerce API.", 'bigcommerce' ) ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * @param int $user_id
	 *
	 * @return void
	 * @action personal_options_update
	 * @action edit_user_profile_update
	 */
	public function save_profile_settings( $user_id ) {
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST[ self::NONCE_NAME ], self::NONCE_ACTION ) ) {
			return;
		}

		update_user_meta( $user_id, self::SYNC_PASSWORD, ! empty( $_POST[ self::SYNC_PASSWORD ] ) );
	}
}