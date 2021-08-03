<?php


namespace BigCommerce\Taxonomies\Channel;

use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Pages;


class BC_Status {

	const STATUS = 'bigcommerce_channel_bc_status';

	const STATUS_ACTIVE   = 'active';
	const STATUS_INACTIVE = 'inactive';
	const STATUS_DELETED  = 'deleted';
	const STATUS_ARCHIVED = 'archived';
	
	/**
	 * Prevent product import for non active status
	 *
	 * @return void
	 * 
	 * @action bigcommerce/import/start
	 */
	public function maybe_cancel_import() {
		if ( ! in_array( $this->get_current_channel_status(), [ self::STATUS_ACTIVE, self::STATUS_INACTIVE ] ) ) {
			do_action( 'bigcommerce/import/error', __( 'Inactive channel. Product import canceled.', 'bigcommerce' ) );
		}
	}

	/**
	 * Show admin notices for non active status
	 *
	 * @return void
	 * 
	 * @action admin_notices
	 */
	public function admin_notices() {
		$message = '';
		switch ( $this->get_current_channel_status() ) {
			case self::STATUS_INACTIVE:
				$message = __( 'Your BC Channel is Inactive and your store is disabled to the public.', 'bigcommerce' );
				break;
			case self::STATUS_DELETED:
				$message = __( 'Your BC Channel has been deleted and your store is no longer active.', 'bigcommerce' );
				break;
			case self::STATUS_ARCHIVED:
				$message = __( 'Your BC Channel has been Archived and your store is no longer active.', 'bigcommerce' );
				break;
		}

		if ( $message ) {
			$class = 'notice notice-error';
			printf(
				'<div class="%s"><p>%s %s</p></div>',
				esc_attr( $class ),
				esc_html( $message ),
				sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'https://login.bigcommerce.com/deep-links/manage/channel-manager/' ), __( 'Manage Channels', 'bigcommerce') )
			); 
		}
	}

	/**
	 * Get current channel status
	 *
	 * @return string
	 */
	public function get_current_channel_status() {
		try {
			$connections = new Connections();
			$current     = $connections->current();

			return get_term_meta( $current->term_id, self::STATUS, true );
		} catch (\Exception $e) {
			return '';
		}
	}

}