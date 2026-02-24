<?php

namespace BigCommerce\Import\Processors;

use BigCommerce\Import\Import_Type;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Post_Types\Queue_Task\Queue_Task;

class Headless {

	public function maybe_switch_headless( $old_value, $new_value ): void {
		if ( $old_value === $new_value ) {
			return;
		}

		$this->start_import();
	}

	/**
	 * Start usual import process with storing products in WP database
	 */
	private function start_import(): void {
		if ( ! $this->current_user_can_start_import() ) {
			return;
		}
		update_option( Import_Type::IMPORT_TYPE, Import_Type::IMPORT_TYPE_FULL );
		/**
		 * Begins the import process when the `Runner\Cron_Runner::START_CRON` action is triggered.
		 *
		 * @return void
		 */
		do_action( Cron_Runner::START_CRON );
	}

	private function current_user_can_start_import(): bool {
		$post_type = get_post_type_object( Product::NAME );

		return ! empty( $post_type ) && current_user_can( $post_type->cap->edit_posts );
	}
}
